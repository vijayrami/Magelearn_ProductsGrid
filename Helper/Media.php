<?php

namespace Magelearn\ProductsGrid\Helper;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Media
 * @package Magelearn\ProductsGrid\Helper
 */
class Media extends AbstractHelper
{
    const TEMPLATE_MEDIA_PATH = 'magelearn';

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var AdapterFactory
     */
    protected $imageFactory;

    /**
     * Media constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $imageFactory
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $imageFactory
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->imageFactory = $imageFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $data
     * @param string $fileName
     * @param string $type
     * @param null $oldImage
     *
     * @return $this
     */
    public function uploadImage(&$data, $fileName = 'image', $type = '', $oldImage = null)
    {
        if (isset($data[$fileName]['delete']) && $data[$fileName]['delete']) {
            if ($oldImage) {
                try {
                    $this->removeImage($oldImage, $type);
                } catch (Exception $e) {
                    $this->_logger->critical($e->getMessage());
                }
            }
            $data[$fileName] = '';
        } else {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $fileName]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);

                $path = $this->getBaseMediaPath($type);

                $image = $uploader->save(
                    $this->mediaDirectory->getAbsolutePath($path)
                );

                if ($oldImage) {
                    $this->removeImage($oldImage, $type);
                }

                $data[$fileName] = $this->_prepareFile($image['file']);
            } catch (Exception $e) {
                $data[$fileName] = isset($data[$fileName]['value']) ? $data[$fileName]['value'] : '';
            }
        }

        return $this;
    }

    /**
     * @param $file
     * @param $type
     *
     * @return $this
     * @throws FileSystemException
     */
    public function removeImage($file, $type)
    {
        $image = $this->getMediaPath($file, $type);
        if ($this->mediaDirectory->isFile($image)) {
            $this->mediaDirectory->delete($image);
        }

        return $this;
    }

    /**
     * @param $file
     * @param string $type
     *
     * @return string
     */
    public function getMediaPath($file, $type = '')
    {
        return $this->getBaseMediaPath($type) . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getBaseMediaPath($type = '')
    {
        return trim(static::TEMPLATE_MEDIA_PATH . '/' . $type, '/');
    }
    
    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
    
    /**
     * @param $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->_prepareFile($file);
    }
    
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return rtrim($this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');
    }

    /**
     * @param $path
     *
     * @return $this
     * @throws FileSystemException
     */
    public function removePath($path)
    {
        $pathMedia = $this->mediaDirectory->getRelativePath($path);
        if ($this->mediaDirectory->isDirectory($pathMedia)) {
            $this->mediaDirectory->delete($path);
        }

        return $this;
    }
}

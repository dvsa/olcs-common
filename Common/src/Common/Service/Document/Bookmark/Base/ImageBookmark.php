<?php

namespace Common\Service\Document\Bookmark\Base;

use Common\Service\Document\Bookmark\Interfaces\FileStoreAwareInterface;
use RuntimeException;

/**
 * Image bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class ImageBookmark extends DynamicBookmark implements FileStoreAwareInterface
{
    protected $fileStore;

    public function setFileStore(/* FileStoreService */$fileStore)
    {
        $this->fileStore = $fileStore;
    }

    public function getFileStore()
    {
        return $this->fileStore;
    }

    /**
     * @NOTE: only jpegs with an extension of .jpeg are supported at
     * the moment. If this needs to change then feel free to alter
     * the API of this method but make sure the RTF parser can handle
     * the new format
     */
    protected function getImage($name, $width = null, $height = null)
    {
        $info = [];
        $type = 'jpeg';
        //$path = __DIR__ . '/../Image/' . $name . '.' . $type;
        $path = '/templates/Image/' . $name . '.' . $type;

        //$data = file_get_contents($path);
        $file = $this->getFileStore()->read($path);

        if ($file === null) {
            throw new RuntimeException('Image path ' . $path . ' does not exist');
        }

        $data = $file->getContent();

        if ($width === null || $height === null) {
            $info = getimagesizefromstring($data);
        }

        if ($width === null) {
            $width = $info[0];
        }

        if ($height === null) {
            $height = $info[1];
        }

        return $this->getParser()->renderImage($data, $width, $height, 'jpeg');
    }
}

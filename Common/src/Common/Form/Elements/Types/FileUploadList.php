<?php

/**
* File Upload List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Common\Form\Elements\Types\FileUploadListItem;
use Common\Form\Elements\Types\Html;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;

/**
 * File Upload List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileUploadList extends Fieldset
{
    /**
     * array of image extensions that can be previewed
     */
    protected $previewableExtensions = ['gif', 'jpg', 'jpeg', 'bmp', 'tif', 'tiff', 'png'];

    /**
     * Set the files in the file list
     *
     * @param array $fileData
     * @param object $url
     */
    public function setFiles($fileData = array(), $url = null)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        foreach ($fileData as $file) {

            $file['url'] = $url->fromRoute(
                'getfile',
                array('identifier' => base64_encode($file['identifier']))
            );

            $size = $file['size'];
            $unit = 0;

            while ($size > 1024) {
                $size = $size / 1024;
                $unit++;
            }

            $file['size'] = round($size, 1) . $units[$unit];

            $fileItem = new FileUploadListItem('file-' . $file['id']);
            $fileItem->setAttribute('class', 'file');

            $id = new Hidden('id');
            $id->setValue($file['id']);

            $version = new Hidden('version');
            $version->setValue($file['version']);

            $html = new Html('link', array('render-container' => false));
            $html->setAttribute('data-container-class', 'file-upload');
            $html->setValue(
                '<p><a href="' . $file['url'] . '">'
                . $file['description'] . '</a> <span>' . $file['size'] . '</span></p>'
            );

            $remove = new Submit('remove', array('render-container' => false));
            $remove->setValue('Remove');
            $remove->setAttribute('class', 'file__remove');
            $remove->setAttribute('data-container-class', 'file-upload');

            $fileItem->add($html);
            $fileItem->add($remove);
            $fileItem->add($id);
            $fileItem->add($version);

            $this->add($fileItem);

            // show image previews if permitted
            if (($this->getOption('preview_images') === true) && $this->isPreviewableImage($file)) {

                $imagePreview = new Html('preview', array('render-container' => true));
                $imagePreview->setValue(
                    '<p><img src="' . $file['url'] . '" /></p>'
                );
                $this->add($imagePreview);

            }
        }
    }

    /**
     * Is this file an image we can preview?
     *
     * @param $file
     * @return bool
     */
    private function isPreviewableImage($file)
    {
        if (
            in_array(
                strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION)),
                $this->getPreviewableExtensions()
            )
        ) {
            return true;
        }
        return false;
    }

    /**
     * Return list of image extensions we can preview
     * @return mixed
     */
    public function getPreviewableExtensions()
    {
        return $this->previewableExtensions;
    }
}

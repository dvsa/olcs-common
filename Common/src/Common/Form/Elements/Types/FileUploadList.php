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
                array('file' => $file['identifier'], 'name' => $file['fileName'])
            );

            $size = $file['size'];
            $unit = 0;

            while ($size > 1024) {
                $size = $size / 1024;
                $unit++;
            }

            $file['size'] = round($size, 1) . $units[$unit];

            $fileItem = new FileUploadListItem('file-' . $file['id']);
            $fileItem->setAttribute('class', 'field');

            $id = new Hidden('id');
            $id->setValue($file['id']);

            $version = new Hidden('version');
            $version->setValue($file['version']);

            $html = new Html('link', array('render-container' => false));
            $html->setAttribute('data-container-class', 'inline');
            $html->setValue(
                '<p><a href="' . $file['url'] . '">'
                . $file['fileName'] . '</a> <span>(' . $file['size'] . ')</span></p>'
            );

            $remove = new Submit('remove', array('render-container' => false));
            $remove->setValue('remove');
            $remove->setAttribute('class', 'remove');
            $remove->setAttribute('data-container-class', 'inline');

            $fileItem->add($html);
            $fileItem->add($remove);
            $fileItem->add($id);
            $fileItem->add($version);

            $this->add($fileItem);
        }
    }
}

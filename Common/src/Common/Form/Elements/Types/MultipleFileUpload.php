<?php

/**
 * Multiple File Upload Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Common\Form\Elements\InputFilters\File;

/**
 * Multiple File Upload Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MultipleFileUpload extends Fieldset
{
    /**
     * Add fields to the fieldset
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setLabel('Upload file');
        $this->setAttribute('class', 'file-upload');

        $list = new FileUploadList('list');
        $this->add($list);

        $messages = new Element\Hidden('__messages__');
        $this->add($messages);

        $fileControlFieldset = new Fieldset('file-controls');
        $fileControlFieldset->setAttribute('class', 'field');

        $uploader = new File('file', array('render-container' => false));
        $uploader->setAttribute('class', 'inline');

        $button = new Element\Submit('upload', array('render-container' => false));
        $button->setValue('Upload');
        $button->setAttribute('class', 'action--primary');

        $fileControlFieldset->add($uploader);
        $fileControlFieldset->add($button);

        $this->add($fileControlFieldset);
    }
}

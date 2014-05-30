<?php

/**
 * Add a filesize validator to the file
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\File as ZendFile;
use Zend\Validator\File\FilesSize;

/**
 * Add a filesize validator to the file
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class File extends ZendFile
{
    const SIZE_KB = 1024;

    const SIZE_MB = 1048576;

    /**
     * Get the max file size
     *
     * @return int
     */
    protected function getMaxFileSize()
    {
        return 2 * self::SIZE_MB;
    }

    /**
     * Add validator
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();

        $spec['validators'] = array(
            new FilesSize(array('size' => $this->getMaxFileSize()))
        );

        return $spec;
    }
}
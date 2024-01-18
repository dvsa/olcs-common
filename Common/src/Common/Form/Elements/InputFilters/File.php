<?php

/**
 * Add a filesize validator to the file
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\File as LaminasFile;
use Laminas\Validator\File\FilesSize;

/**
 * Add a filesize validator to the file
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class File extends LaminasFile
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
    public function getInputSpecification(): array
    {
        $spec = parent::getInputSpecification();

        $spec['validators'] = array(
            new FilesSize($this->getMaxFileSize())
        );

        return $spec;
    }
}

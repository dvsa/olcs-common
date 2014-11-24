<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class DecompressUploadToTmp
 * @package Common\Filter
 */
class DecompressUploadToTmp extends DecompressToTmp
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $tmpDir = $this->createTmpDir();

        $this->getDecompressFilter()->setOptions(['options' => ['target' => $tmpDir]]);
        $value['extracted_dir'] = $tmpDir;
        $this->getDecompressFilter()->filter($value['tmp_name']);

        return $value;
    }
}

<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Exception;

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

        $this->getDecompressFilter()->setTarget($tmpDir);
        $value['extracted_dir'] = $tmpDir;
        $this->getDecompressFilter()->filter($value['tmp_name']);

        return $value;
    }
}

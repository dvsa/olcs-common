<?php

/**
 * File Content
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

/**
 * File Content
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileContent implements \JsonSerializable
{
    private $fileName;

    public function __construct($name)
    {
        $this->fileName = $name;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function jsonSerialize()
    {
        return $this->fileName;
    }
}

<?php

namespace Common\Util;

/**
 * File Content
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileContent
{
    /** @var string */
    private $fileName;

    /** @var string */
    private $mimeType;

    /**
     * FileContent constructor.
     *
     * @param string $name     File name
     * @param string $mimeType Mime type
     */
    public function __construct($name, $mimeType = null)
    {
        $this->fileName = $name;
        $this->mimeType = $mimeType;
    }

    /**
     * Get File Name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get Mime Type
     *
     * @return null|string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fileName;
    }
}

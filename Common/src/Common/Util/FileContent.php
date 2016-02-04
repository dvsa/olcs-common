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
    private $content;

    public function __construct($content)
    {
        $this->content = base64_encode($content);
    }

    public function __toString()
    {
        $content = $this->content;
        $this->content = null;
        return $content;
    }

    public function jsonSerialize()
    {
        return '%s';
    }
}

<?php

/**
 * Json String
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Json String
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class JsonString
{
    private $string;

    public function __construct(ArraySerializableInterface $data)
    {
        $array = $data->getArrayCopy();

        $replacements = [];

        foreach ($array as $key => $value) {
            if ($value instanceof FileContent) {
                $replacements[] = $value;
            }
        }

        $this->string = vsprintf(json_encode($array), $replacements);
    }

    public function __toString()
    {
        return $this->string;
    }
}

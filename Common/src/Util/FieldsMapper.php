<?php

namespace Common\Util;

class FieldsMapper
{

    const DIMENSION_SEP = '.';

    public static function toExtractedDomainModel(array $input)
    {
        $output = [];
        foreach ($input as $key => $value) {
            $output = array_merge_recursive($output, self::buildMultidimensionalArray($key, $value));
        }

        return $output;
    }

    public static function fromExtractedDomainModel(array $input, &$out = [], $lastKey = '')
    {
        foreach ($input as $key => $child) {
            if (is_array($child)) {
                $out = self::fromExtractedDomainModel($child, $out, $lastKey .  self::DIMENSION_SEP . $key);
            } else {
                $out[trim($lastKey . self::DIMENSION_SEP . $key, self::DIMENSION_SEP)] = $child;
            }
        }

        return $out;
    }

    public static function buildMultidimensionalArray($path, $value)
    {
        $array = array();
        foreach (explode(self::DIMENSION_SEP, $path) as $key) {
            if (!isset($a)) {
                $array[$key] = array();
                $a           = &$array[$key];
            } else {
                $a[$key] = array();
                $a       = &$a[$key];
            }
        }
        $a = $value;
        return $array;
    }

}

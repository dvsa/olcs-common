<?php

namespace Common\Util;

class ArrayUtils
{
    public static function recursiveDiff($a, $b)
    {
        $r = [];
        foreach ($a as $k => $v) {
            if (is_array($v) && isset($a[$k]) && isset($b[$k])) {
                $r[$k] = self::recursiveDiff($a[$k], $b[$k]);
            } else {
                $r = array_diff_key($a, $b);
            }

            if (isset($r[$k]) && is_array($r[$k]) && count($r[$k]) == 0) {
                unset($r[$k]);
            }
        }
        return $r;
    }
}

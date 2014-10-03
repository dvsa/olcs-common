<?php

namespace Common\FormTester;

use Zend\Stdlib\ArrayUtils;
use Zend\Form\Element\Button;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;

class Utils
{
    public static function extractFields(\Zend\Form\FieldsetInterface $form)
    {
        $data = [];
        foreach ($form->getElements() as $element) {
            if (!($element instanceof Button || $element instanceof Submit || $element instanceof Hidden)) {
                $data[$element->getName()] = true;
            }
        }

        foreach ($form->getFieldsets() as $fieldset) {
            $extracted = static::extractFields($fieldset);
            if (!empty($extracted)) {
                $data[$fieldset->getName()] = $extracted;
            }
        }

        return $data;
    }

    public static function full_array_diff_recursive($a1, $a2)
    {
        return ArrayUtils::merge(
            static::array_diff_recursive($a1, $a2),
            static::array_diff_recursive($a2, $a1)
        );
    }

    public static function array_diff_recursive($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = static::array_diff_recursive($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    public static function flatten($array, $prefix = '')
    {
        $return = [];
        $prefix = (($prefix == '') ? $prefix : $prefix . '->');
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, static::flatten($value, $prefix . $key));
            } else {
                $return[$prefix.$key] = $value;
            }
        }

        return $return;
    }
}
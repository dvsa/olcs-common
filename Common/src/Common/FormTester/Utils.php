<?php

namespace Common\FormTester;

use Zend\Form\FieldsetInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Form\Element\Button;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;

/**
 * Class Utils
 *
 * Utility package for the form tester abstract; contains methods which need recursion, placed in their own class for
 * tidiness, should be considered as private functions for the Abstract form tester.
 *
 * @package Common\FormTester
 * @private
 */
class Utils
{
    /**
     * @param FieldsetInterface $form
     * @return array
     */
    public static function extractFields(FieldsetInterface $form)
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

    /**
     * @param $a1
     * @param $a2
     * @return array
     */
    public static function fullArrayDiffRecursive($a1, $a2)
    {
        return ArrayUtils::merge(
            static::arrayDiffRecursive($a1, $a2),
            static::arrayDiffRecursive($a2, $a1)
        );
    }

    /**
     * @param $aArray1
     * @param $aArray2
     * @return array
     */
    public static function arrayDiffRecursive($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = static::arrayDiffRecursive($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
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

    /**
     * @param $array
     * @param string $prefix
     * @return array
     */
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

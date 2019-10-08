<?php

namespace Common\Form\Elements\Validators;

class EcmtShortTermNoOfPermitsCombinedTotalValidator
{
    /**
     * Verify that the number of non-zero values found is greater than 0
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function validateNonZeroValuePresent($value, $context)
    {
        $nonZeroValuesFound = self::getNonZeroValuesFound($context);

        return $nonZeroValuesFound > 0;
    }

    /**
     * Verify that the number of non-zero values found is less than 2
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function validateMultipleNonZeroValuesNotPresent($value, $context)
    {
        $nonZeroValuesFound = self::getNonZeroValuesFound($context);

        return $nonZeroValuesFound <= 1;
    }

    /**
     * Get the total number of requested permits across the specified context, disregarding any empty or non-numeric
     * values
     *
     * @param array $context
     *
     * @return int
     */
    private static function getNonZeroValuesFound($context)
    {
        $nonZeroValuesFound = 0;

        foreach ($context as $name => $value) {
            if ((substr($name, 0, 8) == 'required') && is_string($value)) {
                $trimmedValue = trim($value);
                if (ctype_digit($trimmedValue)) {
                    if (intval($trimmedValue) > 0) {
                        $nonZeroValuesFound++;
                    }
                }
            }
        }

        return $nonZeroValuesFound;
    }
}

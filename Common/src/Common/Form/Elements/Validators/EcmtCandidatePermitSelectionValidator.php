<?php

namespace Common\Form\Elements\Validators;

class EcmtCandidatePermitSelectionValidator
{
    public const CANDIDATE_VALUE_PREFIX = 'candidate-';

    /**
     * Verify that at least one checkbox has been ticked amongst all candidate permit checkboxes
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    public static function validate($value, $context)
    {
        foreach ($context as $name => $value) {
            if (strpos($name, self::CANDIDATE_VALUE_PREFIX) === 0 && $value == '1') {
                return true;
            }
        }

        return false;
    }
}

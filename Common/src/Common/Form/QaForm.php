<?php

namespace Common\Form;

class QaForm extends Form
{
    const QA_FIELDSET_NAME = 'qa';

    /**
     * Allow validators to run by filling in missing keys in input data
     *
     * @param array $data
     */
    public function setData($data)
    {
        if (!array_key_exists(self::QA_FIELDSET_NAME, $data)) {
            $data[self::QA_FIELDSET_NAME] = [];
        }

        foreach ($this->get(self::QA_FIELDSET_NAME)->getFieldsets() as $fieldset) {
            $fieldsetName = $fieldset->getName();
            if (!array_key_exists($fieldsetName, $data[self::QA_FIELDSET_NAME])) {
                $data[self::QA_FIELDSET_NAME][$fieldsetName]['qaElement'] = '';
            }
        }

        parent::setData($data);
    }
}

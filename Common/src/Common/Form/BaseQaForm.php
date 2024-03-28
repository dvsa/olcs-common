<?php

namespace Common\Form;

class BaseQaForm extends Form
{
    public const QA_FIELDSET_NAME = 'qa';

    /**
     * Allow validators to run by filling in missing keys in input data
     *
     *
     * @return $data
     */
    public function updateDataForQa(array $data)
    {
        if (!array_key_exists(self::QA_FIELDSET_NAME, $data)) {
            $data[self::QA_FIELDSET_NAME] = [];
        }

        foreach ($this->get(self::QA_FIELDSET_NAME)->getFieldsets() as $fieldset) {
            $fieldsetName = $fieldset->getName();
            if (!array_key_exists($fieldsetName, $data[self::QA_FIELDSET_NAME])) {
                $data[self::QA_FIELDSET_NAME][$fieldsetName] = [];
            }

            foreach ($fieldset->getElements() as $element) {
                $elementName = $element->getName();
                if (!array_key_exists($elementName, $data[self::QA_FIELDSET_NAME][$fieldsetName])) {
                    $data[self::QA_FIELDSET_NAME][$fieldsetName][$elementName] = '';
                }
            }
        }

        return $data;
    }
}

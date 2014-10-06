<?php

/**
 * GoodsDiscStartNumberValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * GoodsDiscStartNumber
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDiscStartNumberValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'lessError' => 'Decreasing the start number is not permitted'
    );

    /**
     * Original start number
     *
     * @var int
     */
    private $originalStartNumber;

    /**
     * Custom validation for goods disc start number
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $originalStartNumber = $this->getOriginalStartNumber();

        if (is_int($originalStartNumber) &&  ($originalStartNumber > $value)) {
            $this->error('lessError');
            return false;
        }

        return true;
    }

    /**
     * Sets original start number
     *
     * @param int $originalStartNumber
     */
    public function setOriginalStartNumber($originalStartNumber)
    {
        $this->originalStartNumber = $originalStartNumber;
    }

    /**
     * Gets original start number
     *
     * @return int
     */
    public function getOriginalStartNumber()
    {
        return $this->originalStartNumber;
    }
}

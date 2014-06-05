<?php

/**
 * PreviousHistoryPenaltiesConvictionsPrevConvictionValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * PreviousHistoryPenaltiesConvictionsPrevConvictionValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PreviousHistoryPenaltiesConvictionsPrevConvictionValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'noOffence' => 'Please add at least one offence'
    );

    /**
     * Rows in table
     *
     * @var int
     */
    private $rows;

    /**
     * Custom validation for has offence field
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value)
    {
        if ($this->getRows() < 1 && $value == 'Y') {

            $this->error('noOffence');

            return false;
        }

        return true;
    }

    /**
     * Sets total amount of table rows
     *
     * @param int $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * Gets total amount of table rows
     *
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }
}

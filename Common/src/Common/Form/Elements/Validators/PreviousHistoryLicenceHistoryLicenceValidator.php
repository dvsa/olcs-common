<?php

/**
 * PreviousHistoryLicenceHistoryLicenceValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * PreviousHistoryLicenceHistoryLicenceValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PreviousHistoryLicenceHistoryLicenceValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'noLicence' => 'Please add at least one licence'
    );

    /**
     * Rows in table
     *
     * @var int
     */
    private $rows;

    /**
     * Custom validation for licence field
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value)
    {
        if ($this->getRows() < 1 && $value == 'Y') {

            $this->error('noLicence');

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

<?php

/**
 * One Row in Tables Required Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators\Lva;

use Zend\Validator\AbstractValidator;

/**
 * One Row in Tables Required Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OneRowInTablesRequiredValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Please add at least one %label%'
    );

    /**
     * Message variables
     *
     * @var array
     */
    protected $messageVariables = array(
        'label' => 'label'
    );

    /**
     * Holds the label
     *
     * @var string
     */
    protected $label = 'vehicle';

    /**
     * Holds the rows quantities
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Crud action flag
     *
     * @var string
     */
    protected $crud = false;

    /**
     * Set the label variable
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get the label variable
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the rows
     *
     * @param array $rowx
     */
    public function setRows($rows = [])
    {
        $this->rows = $rows;
    }

    /**
     * Get the rows
     *
     * @param array $rowx
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set the crud action flag
     *
     * @param bool
     */
    public function setCrud($crud)
    {
        $this->crud = $crud;
    }

    /**
     * Get the crud action flag
     *
     * @param bool $crud
     */
    public function getCrud()
    {
        return $this->crud;
    }

    /**
     * Custom validation
     *
     * @param mixed $value
     */
    public function isValid($value)
    {
        $rows = $this->getRows();
        if ($value == 'Y' && !$this->getCrud()) {

            $hasRow = false;
            foreach ($rows as $row) {
                if ($row > 0) {
                    $hasRow = true;
                    break;
                }
            }
            if (!$hasRow) {
                $this->error('required');
                return false;
            }
        }

        return true;
    }
}

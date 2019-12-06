<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Service\Qa\DateTimeFactory;
use DateTime;
use IntlDateFormatter;
use Zend\I18n\View\Helper\DateFormat;
use Zend\Validator\AbstractValidator;

class DateBeforeValidator extends AbstractValidator
{
    const ERR_DATE_NOT_BEFORE = 'date_not_before';

    /** @var DateFormat */
    private $dateFormat;

    /** @var DateTimeFactory */
    private $dateTimeFactory;

    /** @var array */
    protected $messageTemplates = [
        self::ERR_DATE_NOT_BEFORE => 'Date is too far away'
    ];

    /** @var array */
    protected $messageVariables = [
        'dateMustBeBefore'  => 'formattedDateMustBeBefore',
    ];

    /**
     * Create service instance
     *
     * @param DateFormat $dateFormat
     * @param DateTimeFactory $dateTimeFactory
     * @param array $options
     *
     * @return DateBeforeValidator
     */
    public function __construct(DateFormat $dateFormat, DateTimeFactory $dateTimeFactory, array $options)
    {
        $this->dateFormat = $dateFormat;
        $this->dateTimeFactory = $dateTimeFactory;

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $dateMustBeBefore = $this->getOption('dateMustBeBefore');
        $formattedValue = (new DateTime($value))->format('Y-m-d');

        $valid = $formattedValue < $dateMustBeBefore;
        if (!$valid) {
            $dateMustBeBeforeDateTime = $this->dateTimeFactory->create($dateMustBeBefore);

            $this->formattedDateMustBeBefore = $this->dateFormat->__invoke(
                $dateMustBeBeforeDateTime,
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::NONE
            );

            $this->error(self::ERR_DATE_NOT_BEFORE);
        }

        return $valid;
    }
}

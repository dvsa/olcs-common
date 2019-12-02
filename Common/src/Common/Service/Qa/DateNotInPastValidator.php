<?php

namespace Common\Service\Qa;

use DateTime;
use Zend\Validator\AbstractValidator;

class DateNotInPastValidator extends AbstractValidator
{
    const ERR_DATE_IN_PAST = 'date_in_past';

    /** @var array */
    protected $messageTemplates = [
        self::ERR_DATE_IN_PAST => 'Date is in the past'
    ];

    /** @var DateTimeFactory */
    private $dateTimeFactory;

    /**
     * Create service instance
     *
     * @param DateTimeFactory $dateTimeFactory
     * @param array $options
     *
     * @return DateNotInPastValidator
     */
    public function __construct(DateTimeFactory $dateTimeFactory, array $options)
    {
        $this->dateTimeFactory = $dateTimeFactory;

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $formattedCurrentDateTime = $this->dateTimeFactory->create()->format('Y-m-d');
        $formattedValue = (new DateTime($value))->format('Y-m-d');

        $valid = $formattedValue >= $formattedCurrentDateTime;
        if (!$valid) {
            $this->error(self::ERR_DATE_IN_PAST);
        }

        return $valid;
    }
}

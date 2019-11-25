<?php

namespace Common\Service\Qa;

use Zend\Validator\AbstractValidator;

class DateNotInPastValidator extends AbstractValidator
{
    const ERR_DATE_IN_PAST = 'date_in_past';

    /** @var array */
    protected $messageTemplates = [
        self::ERR_DATE_IN_PAST => 'qanda.ecmt-removal.permit-start-date.error.in-past'
    ];

    /** @var DateTimeFactory */
    private $dateTimeFactory;

    /**
     * Create service instance
     *
     * @param DateTimeFactory $dateTimeFactory
     *
     * @return DateNotInPastValidator
     */
    public function __construct(DateTimeFactory $dateTimeFactory)
    {
        $this->dateTimeFactory = $dateTimeFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        $formattedCurrentDateTime = $this->dateTimeFactory->create()->format('Y-m-d');

        $valid = $value >= $formattedCurrentDateTime;
        if (!$valid) {
            $this->error(self::ERR_DATE_IN_PAST);
        }

        return $valid;
    }
}

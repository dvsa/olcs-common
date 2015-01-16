<?php

/**
 * Fee Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Fee Payment Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeePaymentEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'FeePayment';

    const METHOD_CARD_OFFLINE = 'fpm_card_offline';
    const METHOD_CARD_ONLINE  = 'fpm_card_online';
    const METHOD_CASH         = 'fpm_cash';
    const METHOD_CHEQUE       = 'fpm_cheque';
    const METHOD_POSTAL_ORDER = 'fpm_po';
    const METHOD_WAIVE        = 'fpm_waive';

    /**
     * Helper function to check whether payment type is one of the defined values
     *
     * @param string $value value to test
     * @return boolean
     */
    public function isValidPaymentType($value)
    {
        return in_array(
            $value,
            [
                self::METHOD_CARD_OFFLINE,
                self::METHOD_CARD_ONLINE,
                self::METHOD_CASH,
                self::METHOD_CHEQUE,
                self::METHOD_POSTAL_ORDER,
                self::METHOD_WAIVE,
            ]
        );
    }
}

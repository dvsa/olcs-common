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
    const METHOD_CARD_ONLINE = 'fpm_card_online';
    const METHOD_CASH = 'fpm_cash';
    const METHOD_CHEQUE = 'fpm_cheque';
    const METHOD_POSTAL_ORDER = 'fpm_po';
}

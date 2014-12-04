<?php

namespace Common\Service\Cpms;

/**
 * CPMS Identity Provider Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
use CpmsClient\Authenticate;

/**
 * CPMS Identity Provider Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class IdentityProvider implements Authenticate\IdentityProviderInterface
{
    use Authenticate\IdentityProviderTrait;
}

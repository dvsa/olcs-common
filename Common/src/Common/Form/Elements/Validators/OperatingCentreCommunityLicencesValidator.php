<?php

/**
 * OperatingCentreCommunityLicencesValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * OperatingCentreCommunityLicencesValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreCommunityLicencesValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'too-many' => 'OperatingCentreCommunityLicencesValidator.too-many'
    );

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        $total = $this->getTotal($context);

        if ($value > $total) {
            $this->error('too-many');
            return false;
        }

        return true;
    }

    /**
     * Get the total vehicles
     *
     * @param array $context
     * @return int
     */
    private function getTotal($context)
    {
        if (isset($context['totAuthVehicles'])) {
            return $context['totAuthVehicles'];
        }

        $total = 0;

        $total += (isset($context['totAuthSmallVehicles']) ? $context['totAuthSmallVehicles'] : 0);
        $total += (isset($context['totAuthMediumVehicles']) ? $context['totAuthMediumVehicles'] : 0);
        $total += (isset($context['totAuthLargeVehicles']) ? $context['totAuthLargeVehicles'] : 0);

        return $total;
    }
}

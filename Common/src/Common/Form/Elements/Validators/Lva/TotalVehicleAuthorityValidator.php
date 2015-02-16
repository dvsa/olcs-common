<?php

/**
 * Total Vehicle Authority Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators\Lva;

use Zend\Validator\AbstractValidator;

/**
 * Total Vehicle Authority Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalVehicleAuthorityValidator extends AbstractValidator
{
    /**
     * @var int
     */
    private $totalLicences;

    /**
     * @var int
     */
    private $totalVehicleAuthority;

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'vehicle-authority-exceeded' => 'vehicle-authority-exceeded'
    );

    /**
     * Is valid
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $totalLicences = (int)$value + $this->getTotalLicences();
        if ($totalLicences > $this->getTotalVehicleAuthority()) {
            $this->error('vehicle-authority-exceeded');
            return false;
        }

        return true;
    }

    /**
     * Get total licences
     *
     * @return int
     */
    public function getTotalLicences()
    {
        return $this->totalLicences;
    }

    /**
     * Set total licences
     *
     * @param int
     */
    public function setTotalLicences($totalLicences)
    {
        $this->totalLicences = $totalLicences;
    }

    /**
     * Get total vehicle authority
     *
     * @return int
     */
    public function getTotalVehicleAuthority()
    {
        return $this->totalVehicleAuthority;
    }

    /**
     * Set total vehicle authority
     *
     * @param int
     */
    public function setTotalVehicleAuthority($totalVehicleAuthority)
    {
        $this->totalVehicleAuthority = $totalVehicleAuthority;
    }
}

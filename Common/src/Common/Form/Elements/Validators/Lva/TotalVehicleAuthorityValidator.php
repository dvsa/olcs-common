<?php

/**
 * Total Vehicle Authority Validator
 *
 * @author Alex Peshkov <alex.peshkov@alex.pehkov.co.uk>
 */
namespace Common\Form\Elements\Validators\Lva;

use Zend\Validator\AbstractValidator;

/**
 * Total Vehicle Authority Validator
 *
 * @author Alex Peshkov <alex.peshkov@alex.pehkov.co.uk>
 */
class TotalVehicleAuthorityValidator extends AbstractValidator
{
    /**
     * @var int
     */
    private $totalDiscs;

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
        $totalLicences = (int)$value + $this->getTotalDiscs();
        if ($totalLicences > $this->getTotalVehicleAuthority()) {
            $this->error('vehicle-authority-exceeded');
            return false;
        }

        return true;
    }

    /**
     * Get total discs
     *
     * @return int
     */
    public function getTotalDiscs()
    {
        return $this->totalDiscs;
    }

    /**
     * Set total discs
     *
     * @param int
     */
    public function setTotalDiscs($totalDiscs)
    {
        $this->totalDiscs = $totalDiscs;
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

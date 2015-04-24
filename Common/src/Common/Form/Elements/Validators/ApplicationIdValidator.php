<?php

/**
 * Application Id Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Id Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationIdValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'appIdNotValid' => 'The application ID is not valid',
        'appRestricted' => 'A transport manager cannot be added to a restricted licence',
        'tmAlreadyLinked' => 'The transport manager is already linked to this application'
    );

    /**
     * Application data
     *
     * @var array
     */
    private $appData;

    /**
     * Transort manager applications data
     *
     * @var array
     */
    private $tmAppData;

    /**
     * Custom validation for application id
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $appData = $this->getAppData();
        if (!$appData) {
            $this->error('appIdNotValid');
            return false;
        }
        if (
                $appData['licenceType']['id'] == LicenceEntityService::LICENCE_TYPE_RESTRICTED ||
                $appData['licenceType']['id'] == LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
            ) {
            $this->error('appRestricted');
            return false;
        }
        if (count($this->getTmAppData())) {
            $this->error('tmAlreadyLinked');
            return false;
        }
        return true;
    }

    /**
     * Sets appData
     *
     * @param array $appData
     */
    public function setAppData($appData)
    {
        $this->appData = $appData;
    }

    /**
     * Gets appData
     *
     * @return array
     */
    public function getAppData()
    {
        return $this->appData;
    }

    /**
     * Sets transport manager applications data
     *
     * @param array $tmAppData
     */
    public function setTmAppData($tmAppData)
    {
        $this->tmAppData = $tmAppData;
    }

    /**
     * Gets transport manager applications data
     *
     * @return array
     */
    public function getTmAppData()
    {
        return $this->tmAppData;
    }
}

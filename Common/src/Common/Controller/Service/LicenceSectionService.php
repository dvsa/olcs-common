<?php

/**
 * Licence Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

use Zend\Form\Form;
use Common\Form\Fieldsets\Custom\SectionButtons;

/**
 * Licence Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceSectionService extends AbstractSectionService
{
    /**
     * Goods or PSV keys
     */
    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    /**
     * Licence types keys
     */
    const LICENCE_TYPE_RESTRICTED = 'ltyp_r';
    const LICENCE_TYPE_STANDARD_INTERNATIONAL = 'ltyp_si';
    const LICENCE_TYPE_STANDARD_NATIONAL = 'ltyp_sn';
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    /**
     * Whether or not the licence is psv
     *
     * @var boolean
     */
    private $isPsv;

    /**
     * Holds the licence type
     *
     * @var string
     */
    private $licenceType;

    /**
     * Holds the licence data
     *
     * @var array
     */
    private $licenceData;

    /**
     * Holds the licenceDataBundle
     *
     * @var array
     */
    protected $licenceDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'niFlag',
            'licNo'
        ),
        'children' => array(
            'goodsOrPsv' => array(
                'properties' => array(
                    'id'
                )
            ),
            'licenceType' => array(
                'properties' => array(
                    'id'
                )
            ),
            'organisation' => array(
                'children' => array(
                    'type' => array(
                        'properties' => array(
                            'id'
                        )
                    )
                )
            )
        )
    );

    /**
     * Whether or not the licence is psv
     *
     * @return boolean
     */
    public function isPsv()
    {
        if ($this->isPsv === null) {
            $licenceData = $this->getLicenceData();
            $this->isPsv = ($licenceData['goodsOrPsv']['id'] == self::LICENCE_CATEGORY_PSV);
        }

        return $this->isPsv;
    }

    /**
     * Get the licence type
     *
     * @return array
     */
    public function getLicenceType()
    {
        if ($this->licenceType === null) {
            $licenceData = $this->getLicenceData();
            $this->licenceType = $licenceData['licenceType']['id'];
        }

        return $this->licenceType;
    }

    /**
     * Get the licence data
     *
     * @return array
     */
    public function getLicenceData()
    {
        if ($this->licenceData === null) {
            $this->licenceData = $this->fetchLicenceData();
        }

        return $this->licenceData;
    }

    /**
     * Fetch licence data
     *
     * @return array
     */
    private function fetchLicenceData()
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall('Licence', 'GET', $this->getIdentifier(), $this->licenceDataBundle);
    }

    /**
     * Alter form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        $form->remove('form-actions');

        $form->add(new SectionButtons());

        return $form;
    }
}

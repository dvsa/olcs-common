<?php

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Common\Util;
use Common\Service\Data\SectionConfig;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaController extends AbstractActionController
{
    use Util\HelperServiceAware,
        Util\EntityServiceAware;

    /**
     * Internal/External
     *
     * @var string
     */
    protected $location;

    /**
     * Licence/Variation/Application
     *
     * @var string
     */
    protected $lva;

    /**
     * Check if a button is pressed
     *
     * @param string $button
     * @return boolean
     */
    protected function isButtonPressed($button)
    {
        $data = (array)$this->getRequest()->getPost();

        return isset($data['form-actions'][$button]);
    }

    /**
     * Get accessible sections
     */
    protected function getAccessibleSections()
    {
        $licenceType = $this->getTypeOfLicenceData();

        $access = array(
            $this->location,
            $this->lva,
            $licenceType['goodsOrPsv'],
            $licenceType['licenceType']
        );

        $sectionConfig = new SectionConfig();
        $inputSections = $sectionConfig->getAll();

        $sections = $this->getHelperService('AccessHelper')->setSections($inputSections)
            ->getAccessibleSections($access);

        return array_keys($sections);
    }

    /**
     * Get licence type information
     *
     * @return array
     */
    protected function getTypeOfLicenceData()
    {
        throw new \Exception('getTypeOfLicenceData must be implemented');
    }
}

<?php

/**
 * AbstractApplicationControllerTestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Controller\Application;

use CommonTest\Controller\AbstractSectionControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;
use Zend\View\Model\ViewModel;

/**
 * AbstractApplicationControllerTestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractApplicationControllerTestCase extends AbstractSectionControllerTestCase
{
    protected $identifierName = 'applicationId';
    protected $additionalMockedMethods = array('getNamespaceParts');

    /**
     * Setup an action
     *
     * @param string $action
     * @param int $id
     * @param array $data
     */
    protected function setUpAction($action = 'index', $id = null, $data = array(), $files = array())
    {
        if (strstr($this->controllerName, '\\Common\\Controller\\')) {
            $this->routeName = str_replace(
                array('\\Common\\Controller\\', 'Controller', '\\'),
                array('', '', '/'),
                $this->controllerName
            );
        }

        parent::setUpAction($action, $id, $data, $files);

        $this->controller->expects($this->any())
            ->method('getNamespaceParts')
            ->will($this->returnValue(array_reverse(explode('\\', trim($this->controllerName, '\\')))));

        $mockUrlPlugin = $this->getMock('\Zend\View\Helper\Url', array('__invoke'));
        $mockUrlPlugin->expects($this->any())
            ->method('__invoke')
            ->will($this->returnValue('URL'));

        $inlineScript = new \Zend\View\Helper\InlineScript();

        $mockViewHelperManager = new \Zend\View\HelperPluginManager();
        $mockViewHelperManager->setService('url', $mockUrlPlugin);
        $mockViewHelperManager->setService('inlineScript', $inlineScript);

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('viewhelpermanager', $mockViewHelperManager);

        if (class_exists('\Olcs\Helper\ApplicationJourneyHelper')) {
            $mockApplicationJourneyHelper = $this->getMock(
                '\Olcs\Helper\ApplicationJourneyHelper',
                array('makeRestCall')
            );
            $mockApplicationJourneyHelper->setServiceLocator($this->serviceManager);
            $mockApplicationJourneyHelper->expects($this->any())
                ->method('makeRestCall')
                ->will($this->returnCallback(array($this, 'mockRestCall')));
            $this->serviceManager->setService('ApplicationJourneyHelper', $mockApplicationJourneyHelper);
        }
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     * @param array $bundle
     */
    public function mockRestCall($service, $method, $data = array(), $bundle = array())
    {
        if ($method == 'PUT' || $method == 'DELETE') {
            return null;
        }

        if (!empty($bundle)) {
            $which = base64_encode(json_encode($bundle));
        } else {
            $which = 'default';
        }

        if (isset($this->restResponses[$service][$method][$which])) {

            return $this->restResponses[$service][$method][$which];

        } elseif (isset($this->restResponses[$service][$method]['default'])) {

            return $this->restResponses[$service][$method]['default'];
        }

        $headerBundle = array(
            'properties' => array('id'),
            'children' => array(
                'status' => array(
                    'properties' => array('id')
                ),
                'licence' => array(
                    'properties' => array(
                        'id',
                        'licNo'
                    ),
                    'children' => array(
                        'organisation' => array(
                            'properties' => array(
                                'name'
                            )
                        )
                    )
                )
            )
        );

        if ($service == 'Application' && $method == 'GET' && $bundle == $headerBundle) {
            return array(
                'id' => 1,
                'status' => array(
                    'id' => 'apsts_new'
                ),
                'licence' => array(
                    'id' => 1,
                    'licNo' => 'AB123456',
                    'organisation' => array(
                        'name' => 'Foo ltd'
                    )
                )
            );
        }

        return $this->mockRestCalls($service, $method, $data, $bundle);
    }

    protected function getContentView($view)
    {
        if ($view instanceof ViewModel) {

            $children = $view->getChildrenByCaptureTo('content');

            if (is_array($children) && !empty($children)) {
                return array_shift($children);
            }

            return $view;
        }

        $this->fail('Trying to get last content child of a Response object instead of a ViewModel');
    }

    protected function getNavView($view)
    {
        if ($view instanceof ViewModel) {

            $navChildren = $view->getChildrenByCaptureTo('navigation');
            $this->assertEquals(1, count($navChildren));

            return $navChildren[0];
        }

        $this->fail('Trying to get nav child of a Response object instead of a ViewModel');
    }

    /**
     * Get licence data
     *
     * @param string $goodsOrPsv
     * @return array
     */
    protected function getLicenceData($goodsOrPsv = 'goods', $licenceType = 'ltyp_sn', $niFlag = 'N')
    {
        return array(
            'licence' => array(
                'id' => 10,
                'version' => 1,
                'goodsOrPsv' => array(
                    'id' => ($goodsOrPsv == 'goods' ? 'lcat_gv' : 'lcat_psv')
                ),
                'niFlag' => $niFlag,
                'licenceType' => array(
                    'id' => $licenceType
                ),
                'organisation' => array(
                    'type' => array(
                        'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                    ),
                    'companyOrLlpNo' => 12345678,
                    'name' => 'Bob Ltd'
                )
            )
        );
    }

    /**
     * Get application completion data
     *
     * @return type
     */
    protected function getApplicationCompletionData($lastSection = '')
    {
        return array(
            'id' => '1',
            'version' => 1,
            'sectionTypeOfLicenceStatus' => 2,
            'sectionTypeOfLicenceOperatorLocationStatus' => 2,
            'sectionTypeOfLicenceOperatorTypeStatus' => 2,
            'sectionTypeOfLicenceLicenceTypeStatus' => 2,
            'sectionYourBusinessStatus' => 2,
            'sectionYourBusinessBusinessTypeStatus' => 2,
            'sectionYourBusinessBusinessDetailsStatus' => 2,
            'sectionYourBusinessAddressesStatus' => 2,
            'sectionYourBusinessPeopleStatus' => 2,
            'sectionYourBusinessSoleTraderStatus' => 2,
            'sectionTaxiPhvStatus' => 2,
            'sectionTaxiPhvLicenceStatus' => 2,
            'sectionOperatingCentresStatus' => 2,
            'sectionOperatingCentresAuthorisationStatus' => 2,
            'sectionOperatingCentresFinancialEvidenceStatus' => 2,
            'sectionTransportManagersStatus' => 2,
            'sectionTransportManagersPlaceholderStatus' => 2,
            'sectionVehicleSafetyStatus' => 2,
            'sectionVehicleSafetyVehicleStatus' => 2,
            'sectionVehicleSafetySafetyStatus' => 2,
            'sectionVehicleSafetyVehiclePsvStatus' => 2,
            'sectionVehicleSafetyUndertakingsStatus' => 2,
            'sectionPreviousHistoryStatus' => 2,
            'sectionPreviousHistoryFinancialHistoryStatus' => 2,
            'sectionPreviousHistoryLicenceHistoryStatus' => 2,
            'sectionPreviousHistoryConvictionsPenaltiesStatus' => 2,
            'sectionReviewDeclarationsStatus' => 2,
            'sectionReviewDeclarationsSummaryStatus' => 2,
            'sectionPaymentSubmissionStatus' => 0,
            'sectionPaymentSubmissionPaymentStatus' => 0,
            'sectionPaymentSubmissionSummaryStatus' => 0,
            'lastSection' => $lastSection
        );
    }
}

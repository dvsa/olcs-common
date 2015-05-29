<?php

/**
 * Application Snapshot Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Data\CategoryDataService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Snapshot Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSnapshotProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const ON_SUBMIT = 0;
    const ON_GRANT = 1;
    const ON_REFUSE = 2;
    const ON_WITHDRAW = 3;
    const ON_NTU = 4;

    public function storeSnapshot($applicationId, $event)
    {
        $applicationType = $this->getServiceLocator()->get('Entity\Application')
            ->getApplicationType($applicationId);

        $html = $this->getHtml($applicationType, $applicationId);
        $file = $this->uploadFile($html);
        $this->createDocumentRecord($applicationId, $event, $applicationType, $file);
    }

    protected function createDocumentRecord($applicationId, $event, $applicationType, $file)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($applicationId);

        $documentEntity = $this->getServiceLocator()->get('Entity\Document');

        $date = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s');

        $code = $this->getDocumentCode($applicationId, $applicationType);

        $defaults = [
            'identifier' => $file->getIdentifier(),
            'application' => $applicationId,
            'licence' => $licenceId,
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL,
            'issuedDate' => $date,
            'isExternal' => false,
            'isScan' => false
        ];

        // merge defaults with event specific values
        $data = array_merge($defaults, $this->getDocumentData($event, $code));
        $documentEntity->save($data);
    }

    /**
     * Get Document entity data
     *
     * @param int    $event One the of the self::ON_* constants
     * @param string $code  Application code eg GV79
     *
     * @return array Document entity data
     */
    protected function getDocumentData($event, $code)
    {
        $descriptionPrefix = $code . ' Application Snapshot ';

        switch ($event) {
            case self::ON_GRANT:
                return [
                    'filename' => $descriptionPrefix . 'Grant.html',
                    'description' => $descriptionPrefix .'(at grant/valid)',
                ];
            case self::ON_SUBMIT:
                return [
                    'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL,
                    'filename' => $descriptionPrefix . 'Submit.html',
                    'description' => $descriptionPrefix .'(at submission)',
                    'isExternal' => true,
                ];
            case self::ON_REFUSE:
                return [
                    'filename' => $descriptionPrefix . 'Refuse.html',
                    'description' => $descriptionPrefix .'(at refuse)',
                ];
            case self::ON_WITHDRAW:
                return [
                    'filename' => $descriptionPrefix . 'Withdraw.html',
                    'description' => $descriptionPrefix .'(at withdraw)',
                ];
            case self::ON_NTU:
                return [
                    'filename' => $descriptionPrefix . 'NTU.html',
                    'description' => $descriptionPrefix .'(at NTU)',
                ];
        }
    }

    /**
     * Get Application code
     *
     * @param int $applicationId   Application ID
     * @param int $applicationType Application = 0 or Variation = 1, see ApplicationEntityService::APPLICATION_TYPE_*
     *
     * @return string Eg GV80A
     */
    protected function getDocumentCode($applicationId, $applicationType)
    {
        $typeOfLicence = $this->getServiceLocator()->get('Entity\Application')->getTypeOfLicenceData($applicationId);

        // All New application options
        if ($applicationType == ApplicationEntityService::APPLICATION_TYPE_NEW) {
            if ($typeOfLicence['goodsOrPsv'] == LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
                return ApplicationEntityService::CODE_GV_APP;
            }

            if ($typeOfLicence['licenceType'] == LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
                return ApplicationEntityService::CODE_PSV_APP_SR;
            }

            return ApplicationEntityService::CODE_PSV_APP;
        }

        // All Variation options
        $isUpgrade = $this->getServiceLocator()->get('Processing\VariationSection')
            ->isRealUpgrade($applicationId);

        if ($typeOfLicence['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {

            if ($isUpgrade) {
                return ApplicationEntityService::CODE_GV_VAR_UPGRADE;
            }

            return ApplicationEntityService::CODE_GV_VAR_NO_UPGRADE;
        } else {

            if ($isUpgrade) {
                return ApplicationEntityService::CODE_PSV_VAR_UPGRADE;
            }

            return ApplicationEntityService::CODE_PSV_VAR_NO_UPGRADE;
        }
    }

    protected function uploadFile($content)
    {
        $uploader = $this->getServiceLocator()->get('FileUploader')->getUploader();
        $uploader->setFile(['content' => $content]);

        return $uploader->upload();
    }

    /**
     * Here we physically setup the review controller and catch the HTML so we can persist the snapshot
     *
     * @param type $applicationType
     * @param int $applicationId
     */
    protected function getHtml($applicationType, $applicationId)
    {
        // Applications and Variations use a different controller and adapter
        if ($applicationType == ApplicationEntityService::APPLICATION_TYPE_NEW) {
            $appType = 'Application';
        } else {
            $appType = 'Variation';
        }

        // Setup the controller dependencies
        $controllerPluginManager = $this->getServiceLocator()->get('ControllerPluginManager');
        $event = $this->getServiceLocator()->get('Application')->getMvcEvent();

        // Inject the 'application' route param if it is not present (this can
        // happen if we grant via a licence fee route, for example)
        $routeMatch = $event->getRouteMatch();
        if (empty($routeMatch->getParam('application'))) {
            $routeMatch->setParam('application', $applicationId);
        }

        $adapter = $this->getServiceLocator()->get(sprintf('%sReviewAdapter', $appType));

        // Format the controller name
        $controllerName = sprintf('Lva%s/Review', $appType);

        // Setup the controller
        $controllerManager = $this->getServiceLocator()->get('ControllerManager');
        $controller = $controllerManager->get($controllerName);

        $controller->setPluginManager($controllerPluginManager);
        $controller->setEvent($event);
        $controller->setAdapter($adapter);

        // Execute the controller method, to grab the view model
        $model = $controller->indexAction();

        // Grab the HTML, from the view model
        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        return $renderer->render($model);
    }
}

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

    public function storeSnapshot($applicationId, $event)
    {
        $applicationType = $this->getServiceLocator()->get('Entity\Application')
            ->getApplicationType($applicationId);

        $html = $this->getHtml($applicationType);
        $file = $this->uploadFile($html);
        $this->createDocumentRecord($applicationId, $event, $applicationType, $file);
    }

    protected function createDocumentRecord($applicationId, $event, $applicationType, $file)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($applicationId);

        $documentEntity = $this->getServiceLocator()->get('Entity\Document');

        $date = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s');

        $code = $this->getDocumentCode($applicationId, $applicationType);

        $descriptionPrefix = $code . ' Application Snapshot ';

        $fileName = $descriptionPrefix . ($event == self::ON_SUBMIT ? 'Submit' : 'Grant') . '.html';
        $description = $descriptionPrefix . ($event == self::ON_SUBMIT ? '(at submission)' : '(at grant/valid)');

        $documentEntity->save(
            [
                'identifier' => $file->getIdentifier(),
                'application' => $applicationId,
                'licence' => $licenceId,
                'category' => CategoryDataService::CATEGORY_APPLICATION,
                // At the moment we assume all submits are external, and granting is internal (This may change)
                'subCategory' => (
                    $event == self::ON_SUBMIT
                    ? CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL
                    : CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL
                ),
                'filename' => $fileName,
                'fileExtension' => 'doc_html',
                'issuedDate' => $date,
                'description' => $description,
                // At the moment we assume all submits are external, and granting is internal (This may change)
                'isDigital' => $event == self::ON_SUBMIT,
                'isScan' => false
            ]
        );
    }

    protected function getDocumentCode($applicationId, $applicationType)
    {
        $typeOfLicence = $this->getServiceLocator()->get('Entity\Application')->getTypeOfLicenceData($applicationId);

        // All New application options
        if ($applicationType == ApplicationEntityService::APPLICATION_TYPE_NEW) {
            if ($typeOfLicence['goodsOrPsv'] == LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
                return 'GV79';
            }

            if ($typeOfLicence['licenceType'] == LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
                return 'PSV356';
            }

            return 'PSV421';
        }

        // All Variation options
        $isUpgrade = $this->getServiceLocator()->get('Processing\VariationSection')
            ->isLicenceUpgrade($applicationId);

        if ($typeOfLicence['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return $isUpgrade ? 'GV80A' : 'GV81';
        } else {
            return $isUpgrade ? 'PSV431A' : 'PSV431';
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
     */
    protected function getHtml($applicationType)
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

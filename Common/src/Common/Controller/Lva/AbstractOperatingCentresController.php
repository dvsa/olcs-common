<?php

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Category;
use Common\Data\Mapper\Lva\OperatingCentres;
use Common\Data\Mapper\Lva\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\ApplicationOperatingCentre\Update as AppUpdate;
use Dvsa\Olcs\Transfer\Command\LicenceOperatingCentre\Update as LicUpdate;
use Dvsa\Olcs\Transfer\Command\VariationOperatingCentre\Update as VarUpdate;
use Dvsa\Olcs\Transfer\Command\Licence\CreateOperatingCentre as LicCreateOperatingCentre;
use Dvsa\Olcs\Transfer\Command\Application\CreateOperatingCentre as AppCreateOperatingCentre;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as LicDeleteOperatingCentres;
use Dvsa\Olcs\Transfer\Command\Application\DeleteOperatingCentres as AppDeleteOperatingCentres;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteOperatingCentre as VarDeleteOperatingCentre;
use Dvsa\Olcs\Transfer\Query\Application\OperatingCentres as AppOperatingCentres;
use Dvsa\Olcs\Transfer\Query\ApplicationOperatingCentre\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Query\Licence\OperatingCentres as LicOperatingCentres;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres as AppUpdateOperatingCentres;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres as LicUpdateOperatingCentres;
use Dvsa\Olcs\Transfer\Query\LicenceOperatingCentre\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Query\VariationOperatingCentre\VariationOperatingCentre;

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractOperatingCentresController extends AbstractController
{
    use Traits\CrudTableTrait {
        Traits\CrudTableTrait::handleCrudAction as traitHandleCrudAction;
    }

    protected $section = 'operating_centres';

    protected $listQueryMap = [
        'licence' => LicOperatingCentres::class,
        'variation' => AppOperatingCentres::class,
        'application' => AppOperatingCentres::class,
    ];

    protected $getItemCommandMap = [
        'licence' => LicenceOperatingCentre::class,
        'variation' => VariationOperatingCentre::class,
        'application' => ApplicationOperatingCentre::class,
    ];

    protected $updateCommandMap = [
        'licence' => LicUpdateOperatingCentres::class,
        'variation' => AppUpdateOperatingCentres::class,
        'application' => AppUpdateOperatingCentres::class,
    ];

    protected $updateItemCommandMap = [
        'licence' => LicUpdate::class,
        'variation' => VarUpdate::class,
        'application' => AppUpdate::class,
    ];

    protected $deleteCommandMap = [
        'licence' => LicDeleteOperatingCentres::class,
        'variation' => VarDeleteOperatingCentre::class,
        'application' => AppDeleteOperatingCentres::class,
    ];

    protected $createCommandMap = [
        'licence' => LicCreateOperatingCentre::class,
        // Variation create is the same as New Apps
        'variation' => AppCreateOperatingCentre::class,
        'application' => AppCreateOperatingCentre::class,
    ];

    protected $documents;

    /**
     * Operating centre list action
     */
    public function indexAction()
    {
        $resultData = $this->fetchOcData();

        if ($resultData['requiresVariation']) {
            $this->getServiceLocator()->get('Lva\Variation')
                ->addVariationMessage($this->getIdentifier(), $this->section);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = OperatingCentres::mapFromResult($resultData);
        }

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-operating_centres')
            ->getForm($resultData)
            ->setData($data);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction([$data['table']]);

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableValidation($form->getInputFilter());
            }

            if ($form->isValid()) {

                $response = $this->processUpdateOc($form, $crudAction);

                if ($response !== null) {
                    return $response;
                }
            }
        }

        if (isset($resultData['isVariation']) && $resultData['isVariation']) {
            $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');
        } else {
            $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
        }

        return $this->render('operating_centres', $form);
    }

    protected function processUpdateOc($form, $crudAction)
    {
        $dtoData = OperatingCentres::mapFromForm($form->getData());
        $dtoData['id'] = $this->getIdentifier();

        if ($crudAction !== null) {
            $dtoData['partial'] = true;
            $dtoData['partialAction'] = $this->getActionFromCrudAction($crudAction);
        } else {
            $dtoData['partial'] = false;
        }

        $dtoClass = $this->updateCommandMap[$this->lva];
        $response = $this->handleCommand($dtoClass::create($dtoData));

        if ($response->isOk()) {
            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('operating_centres');
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
        } else {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            $errors = $response->getResult()['messages'];

            if ($crudAction !== null) {
                if (!empty($errors)) {

                    foreach ($errors as $error) {
                        $fm->addErrorMessage($error);
                    }
                } else {
                    $fm->addUnknownError();
                }

                return $this->redirect()->refreshAjax();
            } else {
                OperatingCentres::mapFormErrors($form, $errors, $fm);
            }
        }
    }

    /**
     * Create Operating centre action
     */
    public function addAction()
    {
        $request = $this->getRequest();

        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        }

        $resultData = $this->fetchOcData();

        $this->documents = $resultData['documents'];

        $resultData['action'] = 'add';
        // Only applicable when editing (On a variation)
        $resultData['wouldIncreaseRequireAdditionalAdvertisement'] = false;
        // Only applicable when editing (On a variation)
        $resultData['canUpdateAddress'] = true;

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-operating_centre')
            ->getForm($resultData, $request)
            ->setData($data);

        $hasProcessedPostcode = $this->getServiceLocator()->get('Helper\Form')
            ->processAddressLookupForm($form, $request);

        if ($form->has('advertisements')) {
            $hasProcessedFiles = $this->processFiles(
                $form,
                'advertisements->file',
                [$this, 'processAdvertisementFileUpload'],
                [$this, 'deleteFile'],
                [$this, 'getDocuments']
            );
        } else {
            $hasProcessedFiles = false;
        }

        if (!$hasProcessedFiles && !$hasProcessedPostcode && $request->isPost() && $form->isValid()) {

            $dtoData = OperatingCentre::mapFromForm($form->getData());
            $dtoData[$this->getIdentifierIndex()] = $this->getIdentifier();

            $dtoClass = $this->createCommandMap[$this->lva];
            $response = $this->handleCommand($dtoClass::create($dtoData));
            echo '<pre>';
            print_r($response);
            die();
            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            if ($response->isServerError()) {
                $fm->addUnknownError();
            } else {
                $translator = $this->getServiceLocator()->get('Helper\Translation');
                OperatingCentre::mapFormErrors($form, $response->getResult()['messages'], $fm, $translator);
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('add-operating-centre');

        return $this->render('add_operating_centre', $form);
    }

    /**
     * Update Operating centre action
     */
    public function editAction()
    {
        $request = $this->getRequest();

        $resultData = $this->fetchOcItemData();

        $this->documents = $resultData['operatingCentre']['adDocuments'];

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            if (!$resultData['canUpdateAddress']) {
                $data['address'] = $resultData['operatingCentre']['address'];
            }
        } else {
            $data = OperatingCentre::mapFromResult($resultData);
        }

        $resultData['canAddAnother'] = false;
        $resultData['action'] = 'edit';

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-operating_centre')
            ->getForm($resultData, $request)
            ->setData($data);

        if ($form->get('address')->has('searchPostcode')) {
            $hasProcessedPostcode = $this->getServiceLocator()->get('Helper\Form')
                ->processAddressLookupForm($form, $request);
        } else {
            $hasProcessedPostcode = false;
        }

        if ($form->has('advertisements')) {
            $hasProcessedFiles = $this->processFiles(
                $form,
                'advertisements->file',
                [$this, 'processAdvertisementFileUpload'],
                [$this, 'deleteFile'],
                [$this, 'getDocuments']
            );
        } else {
            $hasProcessedFiles = false;
        }

        if (!$hasProcessedFiles && !$hasProcessedPostcode && $request->isPost() && $form->isValid()) {

            $dtoData = OperatingCentre::mapFromForm($form->getData());
            if (!$resultData['canUpdateAddress']) {
                unset($dtoData['address']);
            }

            $dtoData['id'] = $this->params('child_id');

            // Only needed for variations
            $dtoData[$this->getIdentifierIndex()] = $this->getIdentifier();

            $dtoClass = $this->updateItemCommandMap[$this->lva];
            $response = $this->handleCommand($dtoClass::create($dtoData));

            if ($response->isOk()) {
                return $this->handlePostSave(null, false);
            }

            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            if ($response->isServerError()) {
                $fm->addUnknownError();
            } else {
                $translator = $this->getServiceLocator()->get('Helper\Translation');
                OperatingCentre::mapFormErrors($form, $response->getResult()['messages'], $fm, $translator);
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('add-operating-centre');

        return $this->render('edit_operating_centre', $form);
    }

    public function getDocuments()
    {
        if ($this->documents === null) {
            if ($this->params('child_id')) {
                $this->documents = $this->fetchOcItemData()['operatingCentre']['adDocuments'];
            } else {
                $this->documents = $this->fetchOcData()['documents'];
            }
        }

        return $this->documents;
    }

    protected function delete()
    {
        $id = $this->params('child_id');
        $data = [
            $this->getIdentifierIndex() => $this->getIdentifier(),
            'id' => $id,
            'ids' => explode(',', $id)
        ];

        $dtoClass = $this->deleteCommandMap[$this->lva];
        $response = $this->handleCommand($dtoClass::create($data));

        if ($response->isOk()) {
            return;
        }

        $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

        if ($response->isClientError()) {
            $messages = $response->getResult()['messages'];

            foreach ($messages as $message) {
                $fm->addErrorMessage($message);
            }

            if (empty($messages)) {
                $fm->addUnknownError();
            }

            return;
        }

        $fm->addUnknownError();
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processAdvertisementFileUpload($file)
    {
        // Reset the list, so we have to fetch it again
        $this->documents = null;

        $data = [
            'description' => 'Advertisement',
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL,
            'isExternal'  => $this->isExternal(),
            'licence' => $this->getLicenceId(),
        ];

        if ($this->lva !== 'licence') {
            $data['application'] = $this->getIdentifier();
        }

        $this->uploadFile($file, $data);
    }

    protected function getDeleteMessage()
    {
        return 'lva.section.operating_centres_delete';
    }

    protected function getDeleteTitle()
    {
        return 'delete-oc';
    }

    protected function handleCrudAction(
        $data,
        $rowsNotRequired = ['add'],
        $childIdParamName = 'child_id',
        $route = null
    ) {
        if ($data['action'] === 'Add schedule 4/1') {
            return $this->redirect()->toRouteAjax('lva-application/schedule41', [], [], true);
        }

        return $this->traitHandleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);
    }

    protected function fetchOcData()
    {
        $queryDtoClass = $this->listQueryMap[$this->lva];

        $response = $this->handleQuery($queryDtoClass::create(['id' => $this->getIdentifier()]));
        return $response->getResult();
    }

    protected function fetchOcItemData()
    {
        $dtoClass = $this->getItemCommandMap[$this->lva];
        $response = $this->handleQuery($dtoClass::create(['id' => $this->params('child_id')]));
        return $response->getResult();
    }
}

<?php

namespace Common\Controller\Lva;

use Common\Data\Mapper;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Laminas\Form\Form;
use Laminas\Mvc\MvcEvent;

/**
 * Shared logic between Addresses controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAddressesController extends AbstractController
{
    protected static $mapCmdUpdateAddress = [
        'licence' => TransferCmd\Licence\UpdateAddresses::class,
        'application' => TransferCmd\Application\UpdateAddresses::class,
        'variation' => TransferCmd\Variation\UpdateAddresses::class,
    ];

    protected $section = 'addresses';
    protected $baseRoute = 'lva-%s/addresses';

    /** @var  \Common\Service\Helper\FormHelperService */
    protected $hlpForm;
    /** @var  \Common\Service\Helper\FlashMessengerHelperService */
    protected $hlpFlashMsgr;

    /**
     * Add functionality (use like factory)
     *
     * @param MvcEvent $e Mvc Event
     *
     * @inheritdoc
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->hlpForm = $this->getServiceLocator()->get('Helper\Form');
        $this->hlpFlashMsgr = $this->getServiceLocator()->get('Helper\FlashMessenger');

        return parent::onDispatch($e);
    }

    /**
     * Process action - Index
     *
     * @return \Common\Service\Cqrs\Response|\Common\View\Model\Section
     */
    public function indexAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();

        //  prepare form data
        if ($request->isPost()) {
            $formData = (array)$request->getPost();
        } else {
            //  get api data
            $response = $this->handleQuery(
                TransferQry\Licence\Addresses::create(['id' => $this->getLicenceId()])
            );

            if (!$response->isOk()) {
                return $this->notFoundAction();
            }

            $formData = Mapper\Lva\Addresses::mapFromResult($response->getResult());
        }

        //  get phone contacts from api
        $apiPhoneContactsData = [];
        if (isset($formData['correspondence']['id'])) {
            $apiPhoneContactsData = $this->getPhoneContacts($formData['correspondence']['id']);
        }

        /** @var \Common\Form\Form $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm(
                [
                    'typeOfLicence' => $this->getTypeOfLicenceData(),
                    'corrPhoneContacts' => $apiPhoneContactsData,
                ]
            )
            ->setData($formData);

        $this->alterFormForLva($form);

        $hasProcessed = $this->hlpForm->processAddressLookupForm($form, $request);

        if (!$hasProcessed && $request->isPost()) {
            if ($this->isValid($form, $formData)) {
                $response = $this->save($formData);

                if ($response !== null) {
                    if ($response === true) {
                        return $this->completeSection('addresses');
                    }

                    return $response;
                }
            }
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['forms/addresses']);

        return $this->render('addresses', $form);
    }

    /**
     * Get Correspondence Phone contacts
     *
     * @param int $contactDetailsId Contact Details Id
     *
     * @return array
     */
    protected function getPhoneContacts($contactDetailsId = null)
    {
        return [];
    }

    /**
     * Check is form valid
     *
     * @param Form  $form     Form
     * @param array $formData Form data
     *
     * @return bool
     */
    protected function isValid(Form $form, array $formData)
    {
        $this->disableConsultantValidation($form, $formData);

        return $form->isValid();
    }

    /**
     * Save form
     *
     * @param array $formData Form Data
     *
     * @return array|bool|null
     */
    protected function save(array $formData)
    {
        $dtoData =
            [
                'id' => $this->getIdentifier(),
                'partial' => false,
            ] +
            Mapper\Lva\Addresses::mapFromForm($formData);

        $cmdClass = static::$mapCmdUpdateAddress[$this->lva];
        $response = $this->handleCommand($cmdClass::create($dtoData));

        if ($response->isOk()) {
            return true;
        }

        if ($response->isClientError() || $response->isServerError()) {
            $this->hlpFlashMsgr->addUnknownError();
        }

        return null;
    }

    /**
     * Disable consultant fields validation
     *
     * @param Form  $form Form
     * @param array $data Data
     *
     * @return void
     */
    private function disableConsultantValidation(Form $form, array $data)
    {
        if (!isset($data['consultant']) || $data['consultant']['add-transport-consultant'] !== 'N') {
            return;
        }

        $this->hlpForm->disableValidation(
            $form->getInputFilter()->get('consultant')
        );
        $this->hlpForm->disableValidation(
            $form->getInputFilter()->get('consultantAddress')
        );
    }
}

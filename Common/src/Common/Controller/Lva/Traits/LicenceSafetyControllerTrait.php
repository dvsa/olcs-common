<?php

namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Command\Licence\UpdateSafety;
use Dvsa\Olcs\Transfer\Command\Workshop\DeleteWorkshop;
use Dvsa\Olcs\Transfer\Query\Licence\Safety;
use Zend\Form\Form;

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceSafetyControllerTrait
{
    /**
     * Save
     *
     * @param array $data    Form Data
     * @param bool  $partial Is partial post
     *
     * @return \Common\Service\Cqrs\Response
     * @inheritdoc
     */
    protected function save($data, $partial)
    {
        $dtoData = $data['licence'];
        $dtoData['id'] = $this->getLicenceId();

        return $this->handleCommand(UpdateSafety::create($dtoData));
    }

    /**
     * Delete selected workshops
     *
     * @param array $ids Identifiers
     *
     * @return \Common\Service\Cqrs\Response
     * @inheritdoc
     */
    protected function deleteWorkshops($ids)
    {
        $data = [
            'ids' => $ids,
            'licence' => $this->getIdentifier()
        ];

        return $this->handleCommand(DeleteWorkshop::create($data));
    }

    /**
     * Get Safety Data
     *
     * @param bool $noCache No Cache
     *
     * @return array
     */
    protected function getSafetyData($noCache = false)
    {
        if (is_null($this->safetyData) || $noCache) {
            $response = $this->handleQuery(Safety::create(['id' => $this->getLicenceId()]));
            if (!$response->isOk()) {
                return $this->notFoundAction();
            }

            $licence = $response->getResult();

            $this->canHaveTrailers = $licence['canHaveTrailers'];
            $this->isShowTrailers = $licence['isShowTrailers'];
            $this->workshops = $licence['workshops'];

            $this->safetyData = [
                'version' => null,
                'safetyConfirmation' => null,
                'isMaintenanceSuitable' => $licence['isMaintenanceSuitable'],
                'licence' => $licence,
                'safetyDocuments' => $licence['safetyDocuments']
            ];
        }
        return $this->safetyData;
    }

    /**
     * Alter the form depending on the LVA type
     *
     * @param \Zend\Form\FormInterface $form Form
     * @param array                    $data Api/Form data
     *
     * @return void
     */
    protected function alterFormForLva(Form $form, $data = null)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'application->safetyConfirmation');
    }
}

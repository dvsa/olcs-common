<?php

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
    protected function save($data, $partial)
    {
        $dtoData = $data['licence'];
        $dtoData['id'] = $this->getLicenceId();

        return $this->handleCommand(UpdateSafety::create($dtoData));
    }

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
     * @return array
     */
    protected function getSafetyData()
    {
        $response = $this->handleQuery(Safety::create(['id' => $this->getLicenceId()]));

        if (!$response->isOk()) {
            return $this->notFoundAction();
        }

        $licence = $response->getResult();

        $this->canHaveTrailers = $licence['canHaveTrailers'];
        $this->hasTrailers = $licence['hasTrailers'];
        $this->workshops = $licence['workshops'];

        return array(
            'version' => null,
            'safetyConfirmation' => null,
            'isMaintenanceSuitable' => $licence['isMaintenanceSuitable'],
            'licence' => $licence
        );
    }

    /**
     * Alter the form depending on the LVA type
     *
     * @param \Zend\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'application->safetyConfirmation');
    }
}

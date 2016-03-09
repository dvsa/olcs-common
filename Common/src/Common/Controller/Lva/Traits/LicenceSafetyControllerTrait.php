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
use Common\Category;

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
     * @param bool $noCache
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
            $this->hasTrailers = $licence['hasTrailers'];
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
     * @param array $file
     * @param int $licenceId
     * @return array
     */
    public function getUploadMetaData($file, $licenceId)
    {
        return [
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_MAINT_OTHER_DIGITAL,
            'licence'     => $licenceId,
        ];
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

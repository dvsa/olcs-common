<?php

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Command\Application\DeleteWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\UpdateSafety;
use Dvsa\Olcs\Transfer\Query\Application\Safety;
use Zend\Form\Form;
use Common\Category;

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationSafetyControllerTrait
{
    protected function save($data, $partial)
    {
        $dtoData = $data['application'];
        $dtoData['id'] = $this->getApplicationId();
        $dtoData['partial'] = $partial;
        $dtoData['licence'] = $data['licence'];
        $dtoData['licence']['id'] = $this->getLicenceId();

        return $this->handleCommand(UpdateSafety::create($dtoData));
    }

    protected function deleteWorkshops($ids)
    {
        $data = [
            'application' => $this->getApplicationId(),
            'ids' => $ids
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
            $response = $this->handleQuery(Safety::create(['id' => $this->getApplicationId()]));

            if (!$response->isOk()) {
                return $this->notFoundAction();
            }

            $application = $response->getResult();
            $this->safetyData = $application;

            $this->canHaveTrailers = $application['canHaveTrailers'];
            $this->showTrailers = $application['showTrailers'];
            $this->workshops = $application['licence']['workshops'];
        }
        return $this->safetyData;
    }

    /**
     * @param array $file
     * @param int $applicationId
     * @return array
     */
    public function getUploadMetaData($file, $applicationId)
    {
        $licenceId = $this->getSafetyData()['licence']['id'];

        return [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_MAINT_OTHER_DIGITAL,
            'licence'     => $licenceId,
        ];
    }
}

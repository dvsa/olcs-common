<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 */
trait ApplicationOperatingCentresControllerTrait
{
    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        return $form;
    }

    /**
     * Handle file processing
     */
    protected function maybeProcessFiles($form)
    {
        return $this->processFiles(
            $form,
            'advertisements->file',
            array($this, 'processAdvertisementFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processAdvertisementFileUpload($file)
    {
        $categoryService = $this->getServiceLocator()->get('category');

        $category = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Advertisement', 'Document');

        $this->uploadFile(
            $file,
            array(
                'application' => $this->getApplicationId(),
                'description' => 'Advertisement',
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'licence' => $this->getLicenceId()
            )
        );
    }

    public function getDocuments()
    {
        if (($id = $this->params('child_id')) !== null) {
            $data = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')->getAddressData($id);
            $operatingCentreId = $data['operatingCentre']['id'];
        } else {
            $operatingCentreId = null;
        }

        $documents = $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments($this->getApplicationId(), 'Licensing', 'Advertisement');

        return array_filter(
            $documents,
            function ($d) use ($operatingCentreId) {

                // always include 'unliked' OCs
                if (empty($d['operatingCentre'])) {
                    return true;
                }

                return $d['operatingCentre']['id'] === $operatingCentreId;
            }
        );
    }
}

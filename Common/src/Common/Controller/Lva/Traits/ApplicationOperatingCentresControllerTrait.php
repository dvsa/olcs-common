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
        // @TODO re-implement
        return;

        $this->processFileUploads(array('advertisements' => array('file' => 'processAdvertisementFileUpload')), $form);

        $fileData = $this->getUnlinkedFileData()['Results'];

        if ($this->getActionName() == 'edit') {
            $fileData = array_merge(
                $fileData,
                $this->actionLoad($this->getActionId())['operatingCentre']['adDocuments']
            );
        }

        $url = $this->getServiceLocator()->get('Helper\Url');

        $form->get('advertisements')->get('file')->get('list')->setFiles($fileData, $url);

        $this->processFileDeletions(array('advertisements' => array('file' => 'deleteFile')), $form);
    }

    /**
     * Get unlinked file data
     *
     * @return array
     */
    protected function getUnlinkedFileData()
    {
        return;
        // @TODO re-implement
        $category = $this->getServiceLocator()->get('Data\Category')->getCategoryByDescription('Licensing');
        $subCategory = $this->getServiceLocator()->get('Data\Category')->getCategoryByDescription('Advertisement', 'Document');

        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
            'Document',
            'GET',
            array(
                'application' => $this->getIdentifier(),
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'operatingCentre' => 'NULL'
            ),
            $this->getDocumentBundle
        );
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    protected function processAdvertisementFileUpload($file)
    {
        $category = $this->getCategoryService()->getCategoryByDescription('Licensing');
        $subCategory = $this->getCategoryService()->getCategoryByDescription('Advertisement', 'Document');
        $licence = $this->getLicenceData();

        $this->uploadFile(
            $file,
            array(
                'description' => 'Advertisement',
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'licence' => $licence['id'],
            )
        );
    }
}

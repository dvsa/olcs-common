<?php

/**
 * Financial History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Financial History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFinancialHistoryController extends AbstractController
{
    /**
     * Map the data
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getFinancialHistoryForm()->setData($data);

        $hasProcessedFiles = $this->processFiles(
            $form,
            'data->file',
            array($this, 'processFinancialFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        if (!$hasProcessedFiles && $request->isPost() && $form->isValid()) {

            $data = $this->getServiceLocator()->get('Helper\Data')->processDataMap($data, $this->dataMap);

            $this->getServiceLocator()->get('Entity\Application')->save($data);

            $this->postSave('financial_history');

            return $this->completeSection('financial_history');
        }

        $this->getServiceLocator()->get('Script')->loadFile('financial-history');

        return $this->render('financial_history', $form);
    }

    protected function getFinancialHistoryForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\FinancialHistory');
    }

    protected function getFormData()
    {
        $data = $this->getServiceLocator()->get('Entity\Application')
            ->getFinancialHistoryData($this->getApplicationId());

        return array(
            'data' => $data
        );
    }

    public function getDocuments()
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments($this->getApplicationId(), 'Licensing', 'Insolvency History');
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processFinancialFileUpload($file)
    {
        $categoryService = $this->getServiceLocator()->get('category');

        $category = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Insolvency History', 'Document');

        $this->uploadFile(
            $file,
            array(
                'application' => $this->getApplicationId(),
                'description' => 'Insolvency document',
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'licence' => $this->getLicenceId()
            )
        );
    }
}

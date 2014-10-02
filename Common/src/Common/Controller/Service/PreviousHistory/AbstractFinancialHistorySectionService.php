<?php

/**
 * Abstract Financial History Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Service\PreviousHistory;

use Zend\Form\Form;
use Common\Controller\Service\AbstractSectionService;

/**
 * Abstract Financial History Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractFinancialHistorySectionService extends AbstractSectionService
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

    /**
     * Alter the form
     *
     * @param Form $form
     * @return Form
     */
    public function alterForm(Form $form)
    {
        $this->processFileUploads(array('data' => array('file' => 'processFinancialFileUpload')), $form);

        $options = array(
            'fieldset' => 'data',
            'data'     => $this->loadCurrent(),
        );
        $form = $this->makeFormAlterations($form, $options);

        $this->processFileDeletions(array('data' => array('file' => 'deleteFile')), $form);

        return $form;
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

        $licence = $this->getLicenceSectionService()->getLicenceData();

        $this->uploadFile(
            $file,
            array(
                'description' => 'Insolvency document',
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'licence' => $licence['id']
            )
        );
    }

    /**
     * Process loading the data
     *
     * @param type $oldData
     */
    public function processLoad($oldData)
    {
        return array('data' => $oldData);
    }

    /**
     * Make any relevant form alterations before rendering. In this case
     * we don't show the insolvency details data if it's empty and we're
     * on a review page
     */
    public function makeFormAlterations(Form $form, $options = array())
    {
        $isReview = isset($options['isReview']) && $options['isReview'];
        $data = $options['data'];
        $fieldset = $form->get($options['fieldset']);

        if ($isReview && empty($data['insolvencyDetails'])) {
            $fieldset->remove('insolvencyDetails');
        }

        $fileList = $fieldset->get('file')->get('list');

        $url = $this->getHelperService('UrlHelper');

        $fileList->setFiles($data['documents'], $url);

        return $form;
    }

    /**
     * Get licence section service. Needs to be abstract since
     * how this is fulfilled depends on whether we're a licence
     * or application
     *
     * @NOTE needs DRYing up with AbstractAuthorisationSectionService
     * as they both declare an abstract
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    abstract protected function getLicenceSectionService();
}

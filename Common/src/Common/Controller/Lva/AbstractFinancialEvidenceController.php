<?php

namespace Common\Controller\Lva;

use Common\View\Helper\ReturnToAddress;
use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialEvidence;
use Dvsa\Olcs\Utils\Helper\ValueHelper;
use Common\Data\Mapper\Lva\FinancialEvidence;

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceController extends AbstractController
{
    use Traits\AdapterAwareTrait;

    /**
     * Application create : Financial evidence section
     *
     * @return \Common\View\Model\Section|\Laminas\Http\Response
     */
    public function indexAction()
    {
        /** @var \Laminas\Http\Request $request */
        $request = $this->getRequest();
        $id      = $this->getIdentifier();
        /** @var \Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter $adapter */
        $adapter = $this->getAdapter();

        // get data
        if ($request->isPost()) {
            $formData = FinancialEvidence::mapFromPost((array)$request->getPost());
        } else {
            $formData = FinancialEvidence::mapFromResult($adapter->getData($id));
        }

        // set up form
        /** @var \Common\Form\Form $form */
        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-financial_evidence')
            ->getForm($this->getRequest())
            ->setData($formData);

        $this->alterFormForLva($form);
        $adapter->alterFormForLva($form);

        // handle files
        $hasProcessedFiles = $this->processFiles(
            $form,
            'evidence->files',
            array($this, 'processFinancialEvidenceFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments'),
            'evidence->uploadedFileCount'
        );

        if (!$hasProcessedFiles && $request->isPost() && $form->isValid()) {
            // update application record and redirect
            if ($this->saveFinancialEvidence($formData)) {
                return $this->completeSection('financial_evidence');
            }
        }

        // load scripts
        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        // render view
        $lvaData = $adapter->getData($id);

        $variables = $lvaData['financialEvidence'] +
            [
                'applicationReference' => $lvaData['applicationReference'],
                'sendToAddress' => ReturnToAddress::getAddress($this->isNi($lvaData), '</br>'),
            ];

        return $this->render('financial_evidence', $form, $variables);
    }

    /**
     * Callback to handle the file upload
     *
     * @param array $file File data
     *
     * @return void
     * @throws \Common\Exception\File\InvalidMimeException
     * @throws \Exception
     */
    public function processFinancialEvidenceFileUpload($file)
    {
        /** @var \Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter $adapter */
        $adapter = $this->getAdapter();

        $id = $this->getIdentifier();

        $data = array_merge(
            $adapter->getUploadMetaData($file, $id),
            [
                'isExternal' => $this->isExternal()
            ]
        );

        $this->uploadFile($file, $data);

        // force reload of data with new document included
        $adapter->getData($id, true);
    }

    /**
     * Callback to get list of documents
     *
     * @return array
     */
    public function getDocuments()
    {
        /** @var \Common\Controller\Lva\Adapters\AbstractFinancialEvidenceAdapter $adapter */
        $adapter = $this->getAdapter();

        return $adapter->getDocuments(
            $this->getIdentifier()
        );
    }

    /**
     * Save financial evidence
     *
     * @param array $formData Form Data
     *
     * @return bool
     */
    protected function saveFinancialEvidence($formData)
    {
        $dto = UpdateFinancialEvidence::create(FinancialEvidence::mapFromForm($formData));

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if ($response->isOk()) {
            return true;
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
        return false;
    }

    /**
     * Define is Nothern Ireland application
     *
     * @param array $lvaData LVA object data
     *
     * @return bool
     */
    private function isNi(array $lvaData)
    {
        if (isset($lvaData['niFlag'])) {
            return ValueHelper::isOn($lvaData['niFlag']);
        }

        if (isset($lvaData['trafficArea']['isNi'])) {
            return (bool)$lvaData['trafficArea']['isNi'];
        }

        if (isset($lvaData['licence']['trafficArea']['isNi'])) {
            return (bool)$lvaData['licence']['trafficArea']['isNi'];
        }

        return false;
    }
}

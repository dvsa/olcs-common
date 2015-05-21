<?php

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Common\Data\Mapper\Lva\BusinessType;
use Dvsa\Olcs\Transfer\Query\Organisation\Organisation;
use Zend\Http\Response;

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessTypeController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    /**
     * Business type section
     */
    public function indexAction()
    {
        $prg = $this->prg();

        // If have posted, and need to redirect to get
        if ($prg instanceof Response) {
            return $prg;
        }

        $orgId = $this->getCurrentOrganisationId();
        $response = $this->getBusinessType($orgId);

        if (!$response->isOk()) {

            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            return $this->notFoundAction();
        }

        $result = $response->getResult();

        var_dump($result);
        exit;

        // @todo determine this from org details
        $hasInForceLicences = true;

        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-business_type')
            ->getForm($hasInForceLicences);

        // If we haven't posted
        if ($prg === false) {
            $data = BusinessType::mapFromResult($result);

            $form->setData($data);

            return $this->render('business_type', $form);
        }

        $form->setData($prg);

        if ($form->isValid()) {
            // Save data

            return $this->completeSection('business_type');
        }
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    private function getBusinessType($orgId)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(Organisation::create(['id' => $orgId]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }

    /**
     * Format data for save
     *
     * @param int $orgId
     * @param array $data
     * @return array
     */
    private function formatDataForSave($orgId, $data)
    {
        $persist = array(
            'id' => $orgId,
            'version' => $data['version']
        );

        // the business type input might be disabled; only update
        // if we actually get it through
        if (isset($data['data']['type'])) {
            $persist['type'] = $data['data']['type'];
        }

        return $persist;
    }
}

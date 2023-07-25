<?php

namespace Common\Controller\Lva\Licence;

use Common\Controller\Lva;
use Common\FormService\FormServiceManager;
use Dvsa\Olcs\Transfer\Query\Licence\TypeOfLicence;
use Laminas\Http\Response;
use Common\Data\Mapper\Lva\TypeOfLicence as TypeOfLicenceMapper;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends Lva\AbstractTypeOfLicenceController
{
    /**
     * Licence type of licence section
     *
     * @return \Common\View\Model\Section|Response
     */
    public function indexAction()
    {
        $response = $this->handleQuery(TypeOfLicence::create(['id' => $this->getIdentifier()]));

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            return $this->notFoundAction();
        }

        $data = $response->getResult();

        $this->getServiceLocator()->get('Lva\Variation')->addVariationMessage(
            $this->getIdentifier(),
            'type_of_licence'
        );

        $params = [
            'canBecomeSpecialRestricted' => $data['canBecomeSpecialRestricted'],
            'canUpdateLicenceType' => $data['canUpdateLicenceType']
        ];

        /** @var \Laminas\Form\FormInterface $form */
        $form = $this->getServiceLocator()->get(FormServiceManager::class)
            ->get('lva-licence-type-of-licence')
            ->getForm($params);

        $form->setData(TypeOfLicenceMapper::mapFromResult($data));

        return $this->renderIndex($form);
    }
}

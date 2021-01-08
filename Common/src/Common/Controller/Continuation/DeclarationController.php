<?php

namespace Common\Controller\Continuation;

use Common\Form\Declaration;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Submit;
use Laminas\Http\Response;
use Laminas\View\Model\ViewModel;
use Common\RefData;

/**
 * DeclarationController
 */
class DeclarationController extends AbstractContinuationController
{
    protected $signatures = [
        RefData::ORG_TYPE_SOLE_TRADER => 'declaration-sig-label-st',
        RefData::ORG_TYPE_OTHER => 'declaration-sig-label-other',
        RefData::ORG_TYPE_PARTNERSHIP => 'declaration-sig-label-p',
        RefData::ORG_TYPE_REGISTERED_COMPANY => 'declaration-sig-label',
        RefData::ORG_TYPE_LLP => 'declaration-sig-label',
        RefData::ORG_TYPE_IRFO => 'declaration-sig-label',
    ];

    protected $currentStep = self::STEP_DECLARATION;

    /**
     * Index page
     *
     * @return ViewModel|Response
     */
    public function indexAction()
    {
        $continuationDetail = $this->getContinuationDetailData();

        /** @var \Common\Form\Form $form */
        $form = $this->getForm(\Common\FormService\Form\Continuation\Declaration::class, $continuationDetail);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                // If using Verify to sign
                if ($this->isButtonPressed('sign')) {
                    return $this->redirect()->toRoute(
                        'verify/initiate-request',
                        [
                            'continuationDetailId' => $continuationDetail['id'],
                        ]
                    );
                } else {
                    // Using Print to sign
                    // Submit the continuation
                    $response = $this->handleCommand(
                        Submit::create(['id' => $continuationDetail['id'], 'version' => $form->getData()['version']])
                    );
                    if ($response->isOk()) {
                        // Goto to page depenedant on whether fees need to be paid
                        if ($continuationDetail['hasOutstandingContinuationFee']) {
                            return $this->redirectToPaymentPage();
                        }
                        return $this->redirectToSuccessPage();
                    }
                    $this->addErrorMessage('unknown-error');
                }
            }
        }

        $vars = [
            'backRoute' => 'continuation/finances',
        ];
        return $this->getViewModel($continuationDetail['licence']['licNo'], $form, $vars);
    }

    /**
     * Get form
     *
     * @return Form
     */
    protected function getDeclarationForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm(
            \Common\Form\Model\Form\Continuation\Declaration::class
        );
    }

    /**
     * Print action
     *
     * @return ViewModel
     */
    public function printAction()
    {
        $continuationDetail = $this->getContinuationDetailData();
        $licence = $continuationDetail['licence'];
        $organisation = $licence['organisation'];

        if ($licence['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $title = 'continuation.declaration.print.gv_title';
        } elseif ($licence['licenceType']['id'] === RefData::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $title = 'continuation.declaration.print.psv_sr_title';
        } else {
            $title = 'continuation.declaration.print.psv_title';
        }

        $params = [
            'isNi' => $licence['niFlag'] === 'Y',
            'name' => $organisation['name'],
            'title' => $title,
            'signatureLabel' => $this->signatures[$organisation['type']['id']],
            'undertakings' => $continuationDetail['declarations'],
            'licNo' => $licence['licNo'],
        ];

        $this->layout = 'pages/continuation-declaration';

        return $this->getSimpleViewModel($params);
    }
}

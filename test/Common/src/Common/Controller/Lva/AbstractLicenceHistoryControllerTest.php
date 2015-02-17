<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Licence History Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractLicenceHistoryControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractLicenceHistoryController');

        $this->mockService('Script', 'loadFiles')->with(['lva-crud', 'licence-history']);
    }

    public function testGetIndexAction()
    {
        // @NOTE: lots of methods in here are called N times (one for
        // each prev*) but because we don't assert the 'with' in many
        // cases we just return the same empty data
        //
        // Fine for a smoke test, needs to be more granular for specific
        // checks
        $form = $this->createMockForm('Lva\LicenceHistory');

        $form->shouldReceive('get->get')
            ->andReturn(m::mock('\Zend\Form\Fieldset'));

        $form
            ->shouldReceive('setData')
            ->with(
                [
                    'current' => [
                        'question' => 'N',
                        'version' => 1
                    ],
                    'applied' => [
                        'question' => 'N',
                    ],
                    'refused' => [
                        'question' => 'N',
                    ],
                    'revoked' => [
                        'question' => 'N',
                    ],
                    'public-inquiry' => [
                        'question' => 'N',
                    ],
                    'disqualified' => [
                        'question' => 'N',
                    ],
                    'held' => [
                        'question' => 'N',
                    ]
                ]
            )
            ->andReturn($form);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn(10);

        $this->mockEntity('Application', 'getLicenceHistoryData')
            ->with(10)
            ->andReturn(
                [
                    'prevHasLicence' => 'N',
                    'prevHadLicence' => 'N',
                    'prevBeenRefused' => 'N',
                    'prevBeenRevoked' => 'N',
                    'prevBeenAtPi' => 'N',
                    'prevBeenDisqualifiedTc' => 'N',
                    'prevPurchasedAssets' => 'N',
                    'version' => 1
                ]
            );

        $this->mockEntity('PreviousLicence', 'getForApplicationAndType')
            ->andReturn([]);

        $this->mockService('Table', 'prepareTable')
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('licence_history', $this->view);
    }

    public function testBasicAddCurrentAction()
    {
        $form = $this->createMockForm('Lva\LicenceHistoryLicence');

        $form->shouldReceive('setData');

        $this->shouldRemoveElements(
            $form,
            [
                'data->disqualificationDate',
                'data->disqualificationLength',
                'data->purchaseDate'
            ]
        );

        $this->mockRender();

        $this->sut->currentAddAction();

        $this->assertEquals('add_licence_history', $this->view);
    }
}

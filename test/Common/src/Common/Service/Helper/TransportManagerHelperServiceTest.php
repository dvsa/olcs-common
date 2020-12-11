<?php

namespace CommonTest\Helper;

use Common\Service\Table\TableBuilder;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\TransportManagerHelperService;
use Common\Service\Data\CategoryDataService;

/**
 * @covers \Common\Service\Helper\TransportManagerHelperService
 */
class TransportManagerHelperServiceTest extends MockeryTestCase
{
    /** @var TransportManagerHelperService */
    protected $sut;

    /** @var \Common\Service\Helper\FormHelperService | m\MockInterface */
    private $mockFormHlp;

    protected $tab;
    protected $qs;

    /** @var \Laminas\ServiceManager\ServiceManager | m\MockInterface */
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->mockFormHlp = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->sm->setService('Helper\Form', $this->mockFormHlp);

        $this->tab = m::mock();
        $this->sm->setService('TransferAnnotationBuilder', $this->tab);

        $this->qs = m::mock();
        $this->sm->setService('QueryService', $this->qs);

        $this->sut = new TransportManagerHelperService();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->createService($this->sm);
    }

    public function testGetCertificateFileData()
    {
        $tmId = 111;
        $file = ['name' => 'foo.txt'];

        $expected = [
            'transportManager'         => 111,
            'description'              => 'foo.txt',
            'allowSpacesInDescription' => true,
            'issuedDate'               => '2015-01-01 10:10:10',
            'category'                 => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory'              => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        ];

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01 10:10:10')
            ->once()
            ->getMock()
        );
        $response = $this->sut->getCertificateFileData($tmId, $file);

        $this->assertEquals($expected, $response);
    }

    public function testRemoveTmTypeBothOption()
    {
        /** @var \Laminas\Form\Element $mockTmTypeField */
        $mockTmTypeField = m::mock(\Laminas\Form\Element::class);

        $this->mockFormHlp
            ->shouldReceive('removeOption')->once()->with($mockTmTypeField, 'tm_t_b');

        $this->sut->removeTmTypeBothOption($mockTmTypeField);
    }

    public function testPopulateOtherLicencesTable()
    {
        /** @var TableBuilder $otherLicencesTable */
        $otherLicencesTable = m::mock(TableBuilder::class);
        /** @var \Laminas\Form\Fieldset $mockOtherLicenceField */
        $mockOtherLicenceField = m::mock(\Laminas\Form\Fieldset::class);

        $this->mockFormHlp
            ->shouldReceive('populateFormTable')->once()->with($mockOtherLicenceField, $otherLicencesTable);

        $this->sut->populateOtherLicencesTable($mockOtherLicenceField, $otherLicencesTable);
    }

    public function testGetResponsibilityFileData()
    {
        $tmId = 111;

        $expected = [
            'transportManager' => 111,
            'issuedDate' => '2014-01-20 10:10:10',
            'category' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
        ];

        // Mocks
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        // Expectations
        $mockDateHelper->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2014-01-20 10:10:10');

        // Assertions
        $data = $this->sut->getResponsibilityFileData($tmId);

        $this->assertEquals($expected, $data);
    }

    public function testGetConvictionsAndPenaltiesTable()
    {
        $tmId = 111;

        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $tableData = [
            'foo' => 'bar'
        ];
        $mockTable = $this->expectedGetConvictionsAndPenaltiesTable($mockTableBuilder, $tableData);

        $this->assertSame($mockTable, $this->sut->getConvictionsAndPenaltiesTable($tmId));
    }

    public function testGetPreviousLicencesTable()
    {
        $tmId = 111;

        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $tableData = [
            'foo' => 'bar'
        ];
        $mockTable = $this->expectGetPreviousLicencesTable($mockTableBuilder, $tableData);

        $this->assertSame($mockTable, $this->sut->getPreviousLicencesTable($tmId));
    }

    public function testAlterPreviousHistoryFieldsetTm()
    {
        $fieldset = m::mock(\Laminas\Form\Fieldset::class);
        $hasConvictions = m::mock(\Laminas\Form\Fieldset::class);
        $hasConvictions->shouldReceive('unsetValueOption')->with('Y');
        $hasConvictions->shouldReceive('unsetValueOption')->with('N');
        $hasConvictions->shouldReceive('setOption')->with('hint', 'string');
        $convictions = m::mock(\Laminas\Form\Fieldset::class);
        $convictions->shouldReceive('removeAttribute')->with('class');
        $hasPreviousLicences = m::mock(\Laminas\Form\Fieldset::class);
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('Y');
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('N');
        $previousLicences = m::mock(\Laminas\Form\Fieldset::class);
        $previousLicences->shouldReceive('removeAttribute')->with('class');
        $fieldset->shouldReceive('get')->with('hasConvictions')->andReturn($hasConvictions);
        $fieldset->shouldReceive('get')->with('convictions')->andReturn($convictions);
        $fieldset->shouldReceive('get')->with('previousLicences')->andReturn($previousLicences);
        $fieldset->shouldReceive('get')->with('hasPreviousLicences')->andReturn($hasPreviousLicences);

        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);

        $mockResponse = m::mock();

        // Expectations
        $this->tab->shouldReceive('createQuery')
            ->with(\Dvsa\Olcs\Transfer\Query\Tm\TransportManager::class)
            ->andReturn('TmQuery');

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $this->qs->shouldReceive('send')
            ->with('TmQuery')
            ->andReturn($mockResponse);

        $tm = [
            'previousConvictions' => [
                'foo' => 'bar'
            ],
            'otherLicences' => [
                'foo' => 'bar'
            ]
        ];
        $convictionTable = $this->expectedGetConvictionsAndPenaltiesTable($mockTableBuilder, $tm['previousConvictions']);
        $licenceTable = $this->expectGetPreviousLicencesTable($mockTableBuilder, $tm['otherLicences']);

        $mockFormHelper = m::mock(\Common\Form\View\Helper\Form::class);
        $mockFormHelper->shouldReceive('populateFormTable')
            ->once()
            ->with($convictions, $convictionTable, 'convictions')
            ->shouldReceive('populateFormTable')
            ->once()
            ->with($previousLicences, $licenceTable, 'previousLicences');
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockTranslator = m::mock(\Common\Service\Helper\TranslationHelperService::class);
        $mockTranslator->shouldReceive('translate')->andReturn('string');
        $mockTranslator->shouldReceive('translateReplace')->andReturn('string');
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockUrl = m::mock(\Laminas\View\Helper\Url::class);
        $mockUrl->shouldReceive('fromRoute')->andReturn('string');
        $this->sm->setService('Helper\Url', $mockUrl);

        $this->sut->alterPreviousHistoryFieldsetTm($fieldset, $tm);
    }

    public function testAlterPreviousHistoryFieldset()
    {
        $fieldset = m::mock(\Laminas\Form\Fieldset::class);
        $hasConvictions = m::mock(\Laminas\Form\Fieldset::class);
        $hasConvictions->shouldReceive('unsetValueOption')->with('Y');
        $hasConvictions->shouldReceive('unsetValueOption')->with('N');
        $hasConvictions->shouldReceive('setOption')->with('hint', 'string');
        $convictions = m::mock(\Laminas\Form\Fieldset::class);
        $convictions->shouldReceive('removeAttribute')->with('class');
        $hasPreviousLicences = m::mock(\Laminas\Form\Fieldset::class);
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('Y');
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('N');
        $previousLicences = m::mock(\Laminas\Form\Fieldset::class);
        $previousLicences->shouldReceive('removeAttribute')->with('class');
        $fieldset->shouldReceive('get')->with('hasConvictions')->andReturn($hasConvictions);
        $fieldset->shouldReceive('get')->with('convictions')->andReturn($convictions);
        $fieldset->shouldReceive('get')->with('previousLicences')->andReturn($previousLicences);
        $fieldset->shouldReceive('get')->with('hasPreviousLicences')->andReturn($hasPreviousLicences);

        $tmId = 111;

        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);

        $mockResponse = m::mock();

        // Expectations
        $this->tab->shouldReceive('createQuery')
            ->with(\Dvsa\Olcs\Transfer\Query\Tm\TransportManager::class)
            ->andReturn('TmQuery');

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);
        $mockResponse->shouldReceive('getResult')
            ->andReturn(['id' => $tmId, 'removedDate' => null]);

        $this->qs->shouldReceive('send')
            ->with('TmQuery')
            ->andReturn($mockResponse);

        $tm = [
            'previousConvictions' => [
                'foo' => 'bar'
            ],
            'otherLicences' => [
                'foo' => 'bar'
            ]
        ];
        $convictionTable = $this->expectedGetConvictionsAndPenaltiesTable($mockTableBuilder, $tm['previousConvictions']);
        $licenceTable = $this->expectGetPreviousLicencesTable($mockTableBuilder, $tm['otherLicences']);

        $mockFormHelper = m::mock(\Common\Form\View\Helper\Form::class);
        $mockFormHelper->shouldReceive('populateFormTable')
            ->once()
            ->with($convictions, $convictionTable, 'convictions')
            ->shouldReceive('populateFormTable')
            ->once()
            ->with($previousLicences, $licenceTable, 'previousLicences');
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockTranslator = m::mock(\Common\Service\Helper\TranslationHelperService::class);
        $mockTranslator->shouldReceive('translate')->andReturn('string');
        $mockTranslator->shouldReceive('translateReplace')->andReturn('string');
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockUrl = m::mock(\Laminas\View\Helper\Url::class);
        $mockUrl->shouldReceive('fromRoute')->andReturn('string');
        $this->sm->setService('Helper\Url', $mockUrl);

        $this->sut->alterPreviousHistoryFieldset($fieldset, $tmId);
    }

    protected function expectedGetConvictionsAndPenaltiesTable($mockTableBuilder, $tableData)
    {

        // Mocks
        $mockTable = m::mock();

        $mockResponse = m::mock();

        // Expectations
        $this->tab->shouldReceive('createQuery')
            ->with(\Dvsa\Olcs\Transfer\Query\PreviousConviction\GetList::class)
            ->andReturn('PreviousConvictionQuery');

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);
        $mockResponse->shouldReceive('getResult')
            ->andReturn(['results' => $tableData]);

        $this->qs->shouldReceive('send')
            ->with('PreviousConvictionQuery')
            ->andReturn($mockResponse);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('tm.convictionsandpenalties', $tableData)
            ->andReturn($mockTable);

        return $mockTable;
    }

    protected function expectGetPreviousLicencesTable($mockTableBuilder, $tableData)
    {
        // Mocks
        $mockTable = m::mock();

        $mockResponse = m::mock();

        // Expectations
        $this->tab->shouldReceive('createQuery')
            ->with(\Dvsa\Olcs\Transfer\Query\OtherLicence\GetList::class)
            ->andReturn('query');

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);
        $mockResponse->shouldReceive('getResult')
            ->andReturn(['results' => $tableData]);

        $this->qs->shouldReceive('send')
            ->with('query')
            ->andReturn($mockResponse);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('tm.previouslicences', $tableData)
            ->andReturn($mockTable);

        return $mockTable;
    }
}

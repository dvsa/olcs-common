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

    /** @var \Zend\ServiceManager\ServiceManager | m\MockInterface */
    protected $sm;

    public function setUp()
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

    public function testGetCertificateFiles()
    {
        $tmId = 111;

        $mockTm = m::mock();

        $this->sm->setService('Entity\TransportManager', $mockTm);

        $mockTm->shouldReceive('getDocuments')
            ->with(
                111,
                null,
                null,
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
            )
            ->andReturn('RESPONSE');

        $response = $this->sut->getCertificateFiles($tmId);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testGetCertificateFileData()
    {
        $tmId = 111;
        $file = ['name' => 'foo.txt'];

        $expected = [
            'transportManager' => 111,
            'description' => 'foo.txt',
            'issuedDate' => '2015-01-01 10:10:10',
            'category' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
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

    public function testAlterResponsibilitiesFieldset()
    {
        // Params
        /** @var \Zend\Form\Fieldset::class $fieldset */
        $fieldset = m::mock(\Zend\Form\Fieldset::class);
        /** @var TableBuilder $otherLicencesTable */
        $otherLicencesTable = m::mock(TableBuilder::class);

        // Mocks
        $mockTmTypeField = m::mock(\Zend\Form\Element::class);
        $mockOtherLicenceField = m::mock(\Zend\Form\Fieldset::class);

        // Expectations
        $fieldset
            ->shouldReceive('get')->with('tmType')->andReturn($mockTmTypeField)
            ->shouldReceive('get')->with('otherLicences')->andReturn($mockOtherLicenceField);

        $this->mockFormHlp
            ->shouldReceive('removeOption')->once()->with($mockTmTypeField, 'tm_t_b')
            ->shouldReceive('populateFormTable')->once()->with($mockOtherLicenceField, $otherLicencesTable);

        $this->sut->alterResponsibilitiesFieldset($fieldset, $otherLicencesTable);
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
        $fieldset = m::mock(\Zend\Form\Fieldset::class);
        $hasConvictions = m::mock(\Zend\Form\Fieldset::class);
        $hasConvictions->shouldReceive('unsetValueOption')->with('Y');
        $hasConvictions->shouldReceive('unsetValueOption')->with('N');
        $hasConvictions->shouldReceive('setOption')->with('hint', 'string');
        $convictions = m::mock(\Zend\Form\Fieldset::class);
        $convictions->shouldReceive('removeAttribute')->with('class');
        $hasPreviousLicences = m::mock(\Zend\Form\Fieldset::class);
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('Y');
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('N');
        $previousLicences = m::mock(\Zend\Form\Fieldset::class);
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

        $mockUrl = m::mock(\Zend\View\Helper\Url::class);
        $mockUrl->shouldReceive('fromRoute')->andReturn('string');
        $this->sm->setService('Helper\Url', $mockUrl);

        $this->sut->alterPreviousHistoryFieldsetTm($fieldset, $tm);
    }

    public function testAlterPreviousHistoryFieldset()
    {
        $fieldset = m::mock(\Zend\Form\Fieldset::class);
        $hasConvictions = m::mock(\Zend\Form\Fieldset::class);
        $hasConvictions->shouldReceive('unsetValueOption')->with('Y');
        $hasConvictions->shouldReceive('unsetValueOption')->with('N');
        $hasConvictions->shouldReceive('setOption')->with('hint', 'string');
        $convictions = m::mock(\Zend\Form\Fieldset::class);
        $convictions->shouldReceive('removeAttribute')->with('class');
        $hasPreviousLicences = m::mock(\Zend\Form\Fieldset::class);
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('Y');
        $hasPreviousLicences->shouldReceive('unsetValueOption')->with('N');
        $previousLicences = m::mock(\Zend\Form\Fieldset::class);
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

        $mockUrl = m::mock(\Zend\View\Helper\Url::class);
        $mockUrl->shouldReceive('fromRoute')->andReturn('string');
        $this->sm->setService('Helper\Url', $mockUrl);

        $this->sut->alterPreviousHistoryFieldset($fieldset, $tmId);
    }

    public function testPrepareOtherEmploymentTable()
    {
        $element = m::mock();
        $tmId = 111;

        // Mocks
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $mockTable = $this->expectGetOtherEmploymentTable();

        // Expectations
        $mockFormHelper->shouldReceive('populateFormTable')
            ->with($element, $mockTable, 'employment');

        $this->sut->prepareOtherEmploymentTable($element, $tmId);
    }

    public function testGetOtherEmploymentTable()
    {
        $tmId = 111;

        $mockTable = $this->expectGetOtherEmploymentTable();

        $this->assertSame($mockTable, $this->sut->getOtherEmploymentTable($tmId));
    }

    /**
     * @dataProvider providerGetOtherEmploymentData
     */
    public function testGetOtherEmploymentData($stubbedData, $expectedData)
    {
        $id = 111;

        // Mocks
        $mockResponse = m::mock();

        // Expectations
        $this->tab->shouldReceive('createQuery')
            ->with(\Dvsa\Olcs\Transfer\Query\TmEmployment\GetSingle::class)
            ->andReturn('TmEmploymentQuery');

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);
        $mockResponse->shouldReceive('getResult')
            ->andReturn($stubbedData);

        $this->qs->shouldReceive('send')
            ->with('TmEmploymentQuery')
            ->andReturn($mockResponse);

        $this->assertEquals($expectedData, $this->sut->getOtherEmploymentData($id));
    }

    public function providerGetOtherEmploymentData()
    {
        return [
            [
                [
                    'id' => 111,
                    'version' => 1,
                    'position' => 'All of them',
                    'hoursPerWeek' => '24/7',
                    'employerName' => 'Foo ltd',
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Foo street'
                        ]
                    ]
                ],
                [
                    'tm-employment-details' => [
                        'id' => 111,
                        'version' => 1,
                        'position' => 'All of them',
                        'hoursPerWeek' => '24/7'
                    ],
                    'tm-employer-name-details' => [
                        'employerName' => 'Foo ltd'
                    ],
                    'address' => [
                        'addressLine1' => 'Foo street'
                    ]
                ]
            ],
            [
                [
                    'id' => 111,
                    'version' => 1,
                    'position' => 'All of them',
                    'hoursPerWeek' => '24/7',
                    'employerName' => 'Foo ltd'
                ],
                [
                    'tm-employment-details' => [
                        'id' => 111,
                        'version' => 1,
                        'position' => 'All of them',
                        'hoursPerWeek' => '24/7'
                    ],
                    'tm-employer-name-details' => [
                        'employerName' => 'Foo ltd'
                    ]
                ]
            ]
        ];
    }

    protected function expectGetOtherEmploymentTable()
    {
        $tableData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockTableBuilder = m::mock();
        $mockTable = m::mock();
        $mockTmEmployment = m::mock();

        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('Entity\TmEmployment', $mockTmEmployment);

        // Expectations
        $mockTmEmployment->shouldReceive('getAllEmploymentsForTm')
            ->once()
            ->with(111)
            ->andReturn($tableData);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('tm.employments', $tableData)
            ->andReturn($mockTable);

        return $mockTable;
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

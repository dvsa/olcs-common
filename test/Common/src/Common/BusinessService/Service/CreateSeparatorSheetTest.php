<?php

/**
 * CreateSeparatorSheetTest Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\CreateSeparatorSheet;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * CreateSeparatorSheetTest Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateSeparatorSheetTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new CreateSeparatorSheet();
        $this->sut->setServiceLocator($this->sm);
    }

    public function dataProviderMissingParams()
    {
        return [
            [['XX' => 1, 'subCategoryId' => 1, 'entityIdentifier' => 1, 'description' => 1]],
            [['categoryId' => 1, 'XX' => 1, 'entityIdentifier' => 1, 'description' => 1]],
            [['categoryId' => 1, 'subCategoryId' => 1, 'XX' => 1, 'description' => 1]],
            [['categoryId' => 1, 'subCategoryId' => 1, 'entityIdentifier' => 1, 'XX' => 1]],
        ];
    }

    /**
     * @dataProvider dataProviderMissingParams
     */
    public function testProcessMissingParams($params)
    {
        $response = $this->sut->process($params);

        $this->assertFalse($response->isOk());
        $this->assertContains('parameter is missing from the params array', $response->getMessage());
    }

    public function testProcessCannotFindEntity()
    {
        // Data
        $params = ['categoryId' => 1, 'subCategoryId' => 2, 'entityIdentifier' => 3, 'description' => 4];

        $mockProcessingService = m::mock();
        $this->sm->setService('Processing\ScanEntity', $mockProcessingService);

        $mockProcessingService->shouldReceive('findEntityForCategory')->with(1, 3)->once()->andReturn(false);

        $response = $this->sut->process($params);

        $this->assertFalse($response->isOk());
        $this->assertEquals("Cannot find entity for category '1'.", $response->getMessage());
    }

    public function testProcessWithLicNo()
    {
        // Data
        $params = ['categoryId' => 1, 'subCategoryId' => 2, 'entityIdentifier' => 3, 'description' => 4];

        $entity = [
            'licNo' => 'L0001',
            'id' => 1966,
        ];

        $this->processAssertions($entity, true);

        $knownValues = [
            'DOC_CATEGORY_ID_SCAN'       => 1,
            'DOC_CATEGORY_NAME_SCAN'     => 'CATEGORY_NAME',
            'LICENCE_NUMBER_SCAN'        => 'L0001',
            'LICENCE_NUMBER_REPEAT_SCAN' => 'L0001',
            'ENTITY_ID_TYPE_SCAN'        => 'ENTITY_NAME',
            'ENTITY_ID_SCAN'             => 1966,
            'ENTITY_ID_REPEAT_SCAN'      => 1966,
            'DOC_SUBCATEGORY_ID_SCAN'    => 2,
            'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CATEGORY_NAME',
            'DOC_DESCRIPTION_ID_SCAN'    => 2015,
            'DOC_DESCRIPTION_NAME_SCAN'  => 'DESCRIPTION'
        ];

        $mockDocumentGeneration = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGeneration);

        $mockDocumentGeneration->shouldReceive('generateFromTemplate')
            ->with('Scanning_SeparatorSheet', [], $knownValues)->once()->andReturn('CONTENT');
        $mockDocumentGeneration->shouldReceive('uploadGeneratedContent')
            ->with('CONTENT', 'documents', 'Scanning Separator Sheet')->once()->andReturn('STORED_FILE');

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOk());
    }

    public function testProcessUnknownLicNo()
    {
        // Data
        $params = ['categoryId' => 1, 'subCategoryId' => 2, 'entityIdentifier' => 3, 'description' => 4];

        $entity = [
            'id' => 1966,
        ];

        $this->processAssertions($entity, true);

        $knownValues = [
            'DOC_CATEGORY_ID_SCAN'       => 1,
            'DOC_CATEGORY_NAME_SCAN'     => 'CATEGORY_NAME',
            'LICENCE_NUMBER_SCAN'        => 'Unknown',
            'LICENCE_NUMBER_REPEAT_SCAN' => 'Unknown',
            'ENTITY_ID_TYPE_SCAN'        => 'ENTITY_NAME',
            'ENTITY_ID_SCAN'             => 1966,
            'ENTITY_ID_REPEAT_SCAN'      => 1966,
            'DOC_SUBCATEGORY_ID_SCAN'    => 2,
            'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CATEGORY_NAME',
            'DOC_DESCRIPTION_ID_SCAN'    => 2015,
            'DOC_DESCRIPTION_NAME_SCAN'  => 'DESCRIPTION'
        ];

        $mockDocumentGeneration = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGeneration);

        $mockDocumentGeneration->shouldReceive('generateFromTemplate')
            ->with('Scanning_SeparatorSheet', [], $knownValues)->once()->andReturn('CONTENT');
        $mockDocumentGeneration->shouldReceive('uploadGeneratedContent')
            ->with('CONTENT', 'documents', 'Scanning Separator Sheet')->once()->andReturn('STORED_FILE');

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOk());
    }

    public function testProcessUnknownDescripionAsString()
    {
        // Data
        $params = ['categoryId' => 1, 'subCategoryId' => 2, 'entityIdentifier' => 3, 'description' => 'DESCRIPTION'];

        $entity = [
            'id' => 1966,
        ];

        $this->processAssertions($entity, false);

        $knownValues = [
            'DOC_CATEGORY_ID_SCAN'       => 1,
            'DOC_CATEGORY_NAME_SCAN'     => 'CATEGORY_NAME',
            'LICENCE_NUMBER_SCAN'        => 'Unknown',
            'LICENCE_NUMBER_REPEAT_SCAN' => 'Unknown',
            'ENTITY_ID_TYPE_SCAN'        => 'ENTITY_NAME',
            'ENTITY_ID_SCAN'             => 1966,
            'ENTITY_ID_REPEAT_SCAN'      => 1966,
            'DOC_SUBCATEGORY_ID_SCAN'    => 2,
            'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CATEGORY_NAME',
            'DOC_DESCRIPTION_ID_SCAN'    => 2015,
            'DOC_DESCRIPTION_NAME_SCAN'  => 'DESCRIPTION'
        ];

        $mockDocumentGeneration = m::mock();
        $this->sm->setService('Helper\DocumentGeneration', $mockDocumentGeneration);

        $mockDocumentGeneration->shouldReceive('generateFromTemplate')
            ->with('Scanning_SeparatorSheet', [], $knownValues)->once()->andReturn('CONTENT');
        $mockDocumentGeneration->shouldReceive('uploadGeneratedContent')
            ->with('CONTENT', 'documents', 'Scanning Separator Sheet')->once()->andReturn('STORED_FILE');

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOk());
    }

    /**
     *
     * @param array $entity            Entity data that is used in the document
     * @param bool  $lookupDescription Whether the description should be lookup up (ie an ID) or its already a string
     */
    protected function processAssertions($entity, $lookupDescription = true)
    {
        $mockProcessingService = m::mock();
        $this->sm->setService('Processing\ScanEntity', $mockProcessingService);

        $mockProcessingService->shouldReceive('findEntityForCategory')->with(1, 3)->once()->andReturn($entity);

        $mockDataServiceManager = m::mock();
        $this->sm->setService('DataServiceManager', $mockDataServiceManager);

        $mockDataServiceCategory = m::mock();
        $mockDataServiceManager->shouldReceive('get')->with('Olcs\Service\Data\Category')->once()
            ->andReturn($mockDataServiceCategory);

        $mockDataServiceCategory->shouldReceive('getDescriptionFromId')->with(1)->once()->andReturn('CATEGORY_NAME');

        $mockDataServiceSubCategory = m::mock();
        $mockDataServiceManager->shouldReceive('get')->with('Olcs\Service\Data\SubCategory')->once()
            ->andReturn($mockDataServiceSubCategory);

        $mockDataServiceSubCategory->shouldReceive('setCategory')->with(1)->once()->andReturnSelf();
        $mockDataServiceSubCategory->shouldReceive('getDescriptionFromId')->with(2)->once()
            ->andReturn('SUB_CATEGORY_NAME');

        if ($lookupDescription) {
            $mockDataServiceSubCategoryDescription = m::mock();
            $mockDataServiceManager->shouldReceive('get')->with('Olcs\Service\Data\SubCategoryDescription')->once()
                ->andReturn($mockDataServiceSubCategoryDescription);

            $mockDataServiceSubCategoryDescription->shouldReceive('setSubCategory')->with(2)->once()->andReturnSelf();
            $mockDataServiceSubCategoryDescription->shouldReceive('getDescriptionFromId')->with(4)->once()
                ->andReturn('DESCRIPTION');
        }

        $mockProcessingService->shouldReceive('findEntityNameForCategory')->with(1)->once()->andReturn('ENTITY_NAME');
        $mockProcessingService->shouldReceive('getChildrenForCategory')->with(1, $entity)->once()
            ->andReturn(['foo' => 'ENTITY_CHILDREN']);

        $mockScanEntityService = m::mock();
        $this->sm->setService('Entity\Scan', $mockScanEntityService);

        $mockScanEntityService->shouldReceive('save')->with(
            [
                'category' => 1,
                'subCategory' => 2,
                'description' => 'DESCRIPTION',
                'foo' => 'ENTITY_CHILDREN',
            ]
        )->once()->andReturn(['id' => 2015]);

        $mockPrintScheduler = m::mock();
        $this->sm->setService('PrintScheduler', $mockPrintScheduler);

        $mockPrintScheduler->shouldReceive('enqueueFile')->with('STORED_FILE', 'Scanning Separator Sheet')->once();
    }
}

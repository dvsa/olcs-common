<?php

/**
 * Variation Section Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\VariationSectionProcessingService;
use Common\Service\Entity\VariationCompletionEntityService;

/**
 * Variation Section Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSectionProcessingServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $variationCompletion;
    protected $applicationEntity;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->variationCompletion = m::mock();
        $this->sm->setService('Entity\VariationCompletion', $this->variationCompletion);

        $this->applicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $this->applicationEntity);

        $this->sut = new VariationSectionProcessingService();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setApplicationId(3);
    }

    public function testSetApplicationId()
    {
        $response = $this->sut->setApplicationId(5);

        $this->assertSame($this->sut, $response);
        $this->assertEquals(5, $this->sut->getApplicationId());
    }

    public function testIsStatus()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertTrue($this->sut->isStatus('type_of_licence', VariationCompletionEntityService::STATUS_UPDATED));
    }

    public function testIsStatusFalse()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertFalse($this->sut->isStatus('type_of_licence', VariationCompletionEntityService::STATUS_UNCHANGED));
    }

    public function testIsUpdated()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertTrue($this->sut->isUpdated('type_of_licence'));
    }

    public function testIsUpdatedFalse()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertFalse($this->sut->isUpdated('type_of_licence'));
    }

    public function testIsUnchanged()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertTrue($this->sut->isUnchanged('type_of_licence'));
    }

    public function testIsUnchangedFalse()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertFalse($this->sut->isUnchanged('type_of_licence'));
    }

    public function testNotIsUnchanged()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertTrue($this->sut->isNotUnchanged('type_of_licence'));
    }

    public function testIsNotUnchangedFalse()
    {
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];
        $this->setStubbedCompletionStatuses($stubbedStatuses);

        $this->assertFalse($this->sut->isNotUnchanged('type_of_licence'));
    }

    public function testHasUpdatedTypeOfLicence()
    {
        $data = [
            'licenceType' => [
                'id' => 'ABC'
            ],
            'licence' => [
                'licenceType' => [
                    'id' => 'ABC'
                ]
            ]
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedTypeOfLicence());
    }

    public function testHasUpdatedTypeOfLicenceTrue()
    {
        $data = [
            'licenceType' => [
                'id' => 'ABC'
            ],
            'licence' => [
                'licenceType' => [
                    'id' => 'ABCDEF'
                ]
            ]
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedTypeOfLicence());
    }

    public function testHasUpdatedOperatingCentresEmpty()
    {
        $data = [
            'operatingCentres' => [
                'foo' => 'bar'
            ]
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedOperatingCentres());
    }

    public function testHasUpdatedOperatingCentresFalse()
    {
        $data = [
            'operatingCentres' => null,
            'totAuthVehicles' => 1,
            'totAuthTrailers' => 2,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5,
            'licence' => [
                'totAuthVehicles' => 1,
                'totAuthTrailers' => 2,
                'totAuthSmallVehicles' => 3,
                'totAuthMediumVehicles' => 4,
                'totAuthLargeVehicles' => 5
            ]
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedOperatingCentres());
    }

    public function testHasUpdatedOperatingCentresTrue()
    {
        $data = [
            'operatingCentres' => null,
            'totAuthVehicles' => 1,
            'totAuthTrailers' => 2,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5,
            'licence' => [
                'totAuthVehicles' => 2,
                'totAuthTrailers' => 2,
                'totAuthSmallVehicles' => 3,
                'totAuthMediumVehicles' => 4,
                'totAuthLargeVehicles' => 5
            ]
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedOperatingCentres());
    }

    public function testHasUpdatedTransportManagers()
    {
        $data = [
            'transportManagers' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedTransportManagers());
    }

    public function testHasUpdatedTransportManagersTrue()
    {
        $data = [
            'transportManagers' => ['foo' => 'bar']
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedTransportManagers());
    }

    public function testHasUpdatedVehicles()
    {
        $data = [
            'licenceVehicles' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedVehicles());
    }

    public function testHasUpdatedVehiclesTrue()
    {
        $data = [
            'licenceVehicles' => ['foo' => 'bar']
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedVehicles());
    }

    public function testHasUpdatedConvictionsPenalties()
    {
        $data = [
            'convictionsConfirmation' => 1
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedConvictionsPenalties());
    }

    public function testHasUpdatedConvictionsPenaltiesWithPrevConviction()
    {
        $data = [
            'convictionsConfirmation' => 0,
            'prevConviction' => 1
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedConvictionsPenalties());
    }

    public function testHasUpdatedConvictionsPenaltiesWithoutPrevConviction()
    {
        $data = [
            'convictionsConfirmation' => 0,
            'prevConviction' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedConvictionsPenalties());
    }

    public function testHasUpdatedUndertakings()
    {
        $data = [
            'declaration_confirmation' => 1
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedUndertakings());
    }

    public function testHasUpdatedUndertakingsFalse()
    {
        $data = [
            'declaration_confirmation' => 0
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedUndertakings());
    }

    public function testHasUpdatedFinancialHistory()
    {
        $data = [
            'bankrupt' => null,
            'administration' => null,
            'disqualified' => null,
            'liquidation' => null,
            'receivership' => null,
            'insolvencyConfirmation' => null,
            'insolvencyDetails' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedFinancialHistory());
    }

    public function testHasUpdatedFinancialHistoryTrue()
    {
        $data = [
            'bankrupt' => null,
            'administration' => null,
            'disqualified' => 1,
            'liquidation' => null,
            'receivership' => null,
            'insolvencyConfirmation' => null,
            'insolvencyDetails' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedFinancialHistory());
    }

    public function testHasUpdatedVehicleDeclarations()
    {
        $data = [
            'psvOperateSmallVhl' => null,
            'psvSmallVhlNotes' => null,
            'psvSmallVhlConfirmation' => null,
            'psvNoSmallVhlConfirmation' => null,
            'psvLimousines' => null,
            'psvNoLimousineConfirmation' => null,
            'psvOnlyLimousinesConfirmation' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedVehicleDeclarations());
    }

    public function testHasUpdatedVehicleDeclarationsTrue()
    {
        $data = [
            'psvOperateSmallVhl' => null,
            'psvSmallVhlNotes' => null,
            'psvSmallVhlConfirmation' => 1,
            'psvNoSmallVhlConfirmation' => null,
            'psvLimousines' => null,
            'psvNoLimousineConfirmation' => null,
            'psvOnlyLimousinesConfirmation' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedVehicleDeclarations());
    }

    public function testHasUpdatedConditionsUndertakings()
    {
        $data = [
            'conditionUndertakings' => null
        ];

        $this->setStubbedCompletionData($data);

        $this->assertFalse($this->sut->hasUpdatedConditionsUndertakings());
    }

    public function testHasUpdatedConditionsUndertakingsTrue()
    {
        $data = [
            'conditionUndertakings' => ['foo' => 'bar']
        ];

        $this->setStubbedCompletionData($data);

        $this->assertTrue($this->sut->hasUpdatedConditionsUndertakings());
    }

    public function testHasSavedSection()
    {
        $this->assertTrue($this->sut->hasSavedSection());
    }

    public function testCompleteTypeOfLicenceSectionWithoutChange()
    {
        // Params
        $section = 'type_of_licence';
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED,
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UPDATED,
            'addresses' => VariationCompletionEntityService::STATUS_UPDATED,
            'people' => VariationCompletionEntityService::STATUS_UPDATED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_UPDATED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_UPDATED,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_UPDATED,
            'undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];

        $stubbedData = [
            'licenceType' => [
                'id' => 'ABC'
            ],
            'licence' => [
                'licenceType' => [
                    'id' => 'ABC'
                ]
            ]
        ];
        $expectedCompletion = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED, // Notice this changed
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UPDATED,
            'addresses' => VariationCompletionEntityService::STATUS_UPDATED,
            'people' => VariationCompletionEntityService::STATUS_UPDATED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_UPDATED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_UPDATED,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_UPDATED,
            'undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];

        // Expectations
        $this->variationCompletion->shouldReceive('updateCompletionStatuses')
            ->with(3, $expectedCompletion);

        $this->setStubbedCompletionStatuses($stubbedStatuses);
        $this->setStubbedCompletionData($stubbedData);

        $this->sut->completeSection($section);
    }

    public function testCompleteTypeOfLicenceSectionWithChange()
    {
        // Params
        $section = 'type_of_licence';
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UPDATED,
            'addresses' => VariationCompletionEntityService::STATUS_UPDATED,
            'people' => VariationCompletionEntityService::STATUS_UPDATED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_UPDATED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_UPDATED,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_UPDATED,
            'undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];
        $stubbedData = [
            'licenceType' => [
                'id' => 'ABC'
            ],
            'licence' => [
                'licenceType' => [
                    'id' => 'ABCDEF'
                ]
            ]
        ];
        $expectedCompletion = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED, // Note this changes
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UPDATED,
            'addresses' => VariationCompletionEntityService::STATUS_UPDATED,
            'people' => VariationCompletionEntityService::STATUS_UPDATED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_UPDATED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_UPDATED,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_UPDATED,
            'undertakings'  => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION // Note that this changes
        ];

        // Expectations
        $this->variationCompletion->shouldReceive('updateCompletionStatuses')
            ->with(3, $expectedCompletion);

        $this->applicationEntity->shouldReceive('forceUpdate')
            ->with(3, ['declarationConfirmation' => 0]);

        $this->setStubbedCompletionStatuses($stubbedStatuses);
        $this->setStubbedCompletionData($stubbedData);

        $this->sut->completeSection($section);
    }

    public function testCompleteTypeOfLicenceSectionWithChangeWhenRelatedSectionsAreUnchanged()
    {
        // Params
        $section = 'type_of_licence';
        $stubbedStatuses = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'addresses' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'people' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];
        $stubbedData = [
            'licenceType' => [
                'id' => 'ABC'
            ],
            'licence' => [
                'licenceType' => [
                    'id' => 'ABCDEF'
                ]
            ]
        ];
        $expectedCompletion = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED,
            'business_type' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'business_details' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'addresses' => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION,
            'people' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'taxi_phv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_evidence' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'transport_managers' => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_psv' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'vehicles_declarations' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'discs' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'community_licences' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'safety' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'conditions_undertakings' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'financial_history' => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION,
            'licence_history' => VariationCompletionEntityService::STATUS_UNCHANGED,
            'convictions_penalties' => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION,
            'undertakings' => VariationCompletionEntityService::STATUS_REQUIRES_ATTENTION
        ];

        // Expectations
        $this->variationCompletion->shouldReceive('updateCompletionStatuses')
            ->with(3, $expectedCompletion);

        $this->applicationEntity->shouldReceive('forceUpdate')
            ->with(3, ['declarationConfirmation' => 0]);

        $this->setStubbedCompletionStatuses($stubbedStatuses);
        $this->setStubbedCompletionData($stubbedData);

        $this->sut->completeSection($section);
    }

    protected function setStubbedCompletionStatuses($data)
    {
        $this->variationCompletion->shouldReceive('getCompletionStatuses')
            ->with(3)
            ->andReturn($data);
    }

    protected function setStubbedCompletionData($data)
    {
        $this->applicationEntity->shouldReceive('getVariationCompletionStatusData')
            ->with(3)
            ->andReturn($data);
    }
}

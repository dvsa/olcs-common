<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DataRetentionAssignedTo;
use Common\View\Helper\PersonName;
use Zend\View\HelperPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers \Common\Service\Table\Formatter\DataRetentionAssignedTo
 */
class DataRetentionAssignedToTest extends MockeryTestCase
{
    /**
     * Tests empty string returned if there's no person information
     */
    public function testFormatUnassigned()
    {
        $sut = new DataRetentionAssignedTo();
        $this->assertEquals('', $sut::format([]));
    }

    /**
     * Tests the formatter calls the person helper correctly
     */
    public function testFormat()
    {
        $sut = new DataRetentionAssignedTo();

        $person = [
            'forename' => 'forename',
            'familyName' => 'familyName'
        ];

        $personFormatted = 'forename familyName';

        $data = [
            'assignedTo' => [
                'contactDetails' => [
                    'person' => $person
                ]
            ]
        ];

        $sm = m::mock(ServiceLocatorInterface::class);

        $personHelper = m::mock(PersonName::class);
        $personHelper->shouldReceive('__invoke')
            ->once()
            ->with(
                $person,
                [
                    'forename',
                    'familyName'
                ]
            )
            ->andReturn($personFormatted);

        $viewHelperManager = m::mock(HelperPluginManager::class);
        $viewHelperManager->shouldReceive('get')->with('personName')->once()->andReturn($personHelper);

        $sm->shouldReceive('get')->with('ViewHelperManager')->once()->andReturn($viewHelperManager);

        $this->assertEquals($personFormatted, $sut::format($data, [], $sm));
    }
}

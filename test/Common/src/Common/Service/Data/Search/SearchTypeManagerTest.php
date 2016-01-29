<?php

namespace OlcsTest\Service\Data\Search;

use Common\Data\Object\Search\SearchAbstract;
use Common\Service\Data\Search\SearchTypeManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SearchTypeManagerTest
 * @package OlcsTest\Service\Data\Search
 */
class SearchTypeManagerTest extends MockeryTestCase
{
    public function testValidatePlugin()
    {
        $valid = m::mock(SearchAbstract::class);
        $invalid = new \stdClass();

        $sut = new SearchTypeManager();

        $sut->validatePlugin($valid);

        $passed = false;

        try {
            $sut->validatePlugin($invalid);
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Invalid class') {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception no thrown or message didn\'t match');
    }
}

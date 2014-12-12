<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\Clean;
use Common\Data\Object\Publication;

/**
 * Class CleanTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CleanTest extends \PHPUnit_Framework_TestCase
{

    public function testFilter()
    {
        $data = [
            'hearingData' => 'test',
            'licenceData' => 'test',
            'publicationSectionConst' => 'test'
        ];

        $input = new Publication($data);
        $output = new Publication();

        $sut = new Clean();
        $this->assertEquals($output, $sut->filter($input));
    }
}

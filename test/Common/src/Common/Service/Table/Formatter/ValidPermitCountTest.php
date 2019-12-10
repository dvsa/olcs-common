<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\ValidPermitCount;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Valid permit count test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ValidPermitCountTest extends MockeryTestCase
{
    /**
     * @dataProvider dpFormat
     */
    public function testFormat($irhpPermitTypeId, $expectedValidPermitCount)
    {
        $row = [
            'typeId' => $irhpPermitTypeId,
            'validPermitCount' => 7
        ];

        $this->assertEquals(
            $expectedValidPermitCount,
            ValidPermitCount::format($row)
        );
    }

    public function dpFormat()
    {
        return [
            [RefData::ECMT_PERMIT_TYPE_ID, 7],
            [RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID, 7],
            [RefData::ECMT_REMOVAL_PERMIT_TYPE_ID, 7],
            [RefData::IRHP_BILATERAL_PERMIT_TYPE_ID, 7],
            [RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID, 7],
            [RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID, 1],
            [RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID, 1],
        ];
    }
}

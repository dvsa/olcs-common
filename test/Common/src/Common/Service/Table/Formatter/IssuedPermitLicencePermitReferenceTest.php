<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IssuedPermitLicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * IssuedPermitLicencePermitReference test
 */
class IssuedPermitLicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-permits', ['permitid' => $row['id'], 'licence' => $row['licenceId'], 'permitTypeId' => $row['typeId']])
            ->andReturn('http://selfserve/licence/'.$row['licenceId'].'/permits/'.$row['id'].'/'.$row['typeId'].'/irhp-permits/');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);

        $sut = new IssuedPermitLicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            [
                [
                    'id' => 3,
                    'licenceId' => 200,
                    'applicationRef' => 'ECMT>1234567',
                    'typeId' => 1
                ],
                '<a href="http://selfserve/licence/200/permits/3/1/irhp-permits/">ECMT&gt;1234567</a>'
            ],
            [
                [
                    'id' => 44,
                    'licenceId' => 202,
                    'applicationRef' => 'IRHP>7654321',
                    'typeId' => 4
                ],
                '<a href="http://selfserve/licence/202/permits/44/4/irhp-permits/">IRHP&gt;7654321</a>'
            ]
        ];
    }
}

<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\StandardConditions;
use Common\Service\Entity\LicenceEntityService;

/**
 * TA Name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class StandardConditionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new StandardConditions();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($niFlag, $licenceType, $path)
    {
        $bookmark = $this->getMock('Common\Service\Document\Bookmark\StandardConditions', ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->with($path)
            ->willReturn('snippet');

        $bookmark->setData(
            [
                'niFlag' => $niFlag,
                'licenceType' => [
                    'id' => $licenceType
                ]
            ]
        );

        $this->assertEquals('snippet', $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                'N',
                LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                'GB_RESTRICTED_LICENCE_CONDITIONS'
            ], [
                'N',
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'GB_STANDARD_LICENCE_CONDITIONS'
            ], [
                'N',
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'GB_STANDARD_INT_LICENCE_CONDITIONS'
            ], [
                'Y',
                LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                'NI_RESTRICTED_LICENCE_CONDITIONS'
            ], [
                'Y',
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'NI_STANDARD_LICENCE_CONDITIONS'
            ], [
                'Y',
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'NI_STANDARD_INT_LICENCE_CONDITIONS'
            ]
        ];
    }
}

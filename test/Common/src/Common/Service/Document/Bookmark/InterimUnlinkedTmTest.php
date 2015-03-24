<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\InterimUnlinkedTm;
use Common\Service\Entity\LicenceEntityService;

/**
 * Interim Unlinked TM test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimUnlinkedTmTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new InterimUnlinkedTm();
        $query = $bookmark->getQuery(['application' => 123]);

        $this->assertEquals('Application', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRenderWithRestrictedLicence()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                ]
            ]
        );

        $this->assertEquals('N/A', $bookmark->render());
    }

    public function testRenderWithNoTms()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                'transportManagers' => []
            ]
        );

        $this->assertEquals('None added as part of this application', $bookmark->render());
    }

    public function testRenderWithTms()
    {
        $bookmark = new InterimUnlinkedTm();
        $bookmark->setData(
            [
                'licenceType' => [
                    'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                'transportManagers' => [
                    [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'forename' => 'A',
                                    'familyName' => 'Person'
                                ]
                            ]
                        ]
                    ], [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'forename' => 'B',
                                    'familyName' => 'Person'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals("A Person\nB Person", $bookmark->render());
    }
}

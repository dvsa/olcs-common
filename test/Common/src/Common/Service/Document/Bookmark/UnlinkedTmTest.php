<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\UnlinkedTm;

/**
 * Licence holder name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UnlinkedTmTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new UnlinkedTm();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRenderValidDataProvider()
    {
        return array(
            array(
                'Testy McTest\n',
                array(
                    'tmLicences' => array(
                        0 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'forename' => 'Testy',
                                    'familyName' => 'McTest'
                                )
                            )
                        )
                    )
                )
            ),
            array(
                'Lorem Ipsum\nTesty McTest\n',
                array(
                    'tmLicences' => array(
                        0 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'forename' => 'Lorem',
                                    'familyName' => 'Ipsum'
                                )
                            )
                        ),
                        1 => array(
                            'transportManager' => array(
                                'homeCd' => array(
                                    'forename' => 'Testy',
                                    'familyName' => 'McTest'
                                )
                            )
                        )
                    )
                )
            ),
            array(
                'To be nominated.',
                array(
                    'tmLicences' => array()
                )
            )
        );
    }

    /**
     * @dataProvider testRenderValidDataProvider
     */
    public function testRender($expected, $results)
    {
        $bookmark = new UnlinkedTm();
        $bookmark->setData($results);

        $this->assertEquals($expected, $bookmark->render());
    }
}

<?php

/**
 * Test PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace CommonTest\View\Helper;

use PHPUnit_Framework_TestCase;
use \Common\View\Helper\PersonName;

/**
 * Test PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class PersonNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->viewHelper = new PersonName();
    }

    /**
     * Test invoke
     * @dataProvider personNameDataProvider
     */
    public function testInvokeDefaultFields($input, $expected)
    {
        if (!empty($input['fields'])) {
            // specified fields to return
            $this->assertEquals($expected, $this->viewHelper->__invoke($input['person'], $input['fields']));
        } else {
            // use default
            $this->assertEquals($expected, $this->viewHelper->__invoke($input['person']));
        }
    }

    public function personNameDataProvider()
    {
        return [
            [ // include title
                [
                    'person' => [
                        'title' => 't',
                        'forename' => 'f',
                        'familyName' => 's'
                    ]
                ],
                't f s'
            ],
            [ // no title
                [
                    'person' => [
                        'forename' => 'f',
                        'familyName' => 's'
                    ],
                    'fields' => null
                ],
                'f s'
            ],
            [ // include select fields
                [
                    'person' => [
                        'title' => 't',
                        'forename' => 'f',
                        'familyName' => 's'
                    ],
                    'fields' => [
                        'title',
                        'familyName'
                    ]
                ],
                't s'
            ],
        ];
    }
}

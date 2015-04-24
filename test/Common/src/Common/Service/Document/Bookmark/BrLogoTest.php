<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\BrLogo;

/**
 * Br Logo test
 */
class BrLogoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new BrLogo();

        $this->assertTrue(is_array($bookmark->getQuery(['busRegId' => 123])));
        $this->assertTrue(is_null($bookmark->getQuery([])));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $image)
    {
        $bookmark = new BrLogo();
        $bookmark->setData($data);

        $fileMock = $this->getMock('\stdClass', ['getContent']);
        $fileMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('getContent')
            ->willReturn('content');

        $fileStoreMock = $this->getMock('\stdClass', ['read']);
        $fileStoreMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('read')
            ->with('/templates/Image/' . $image . '.jpg')
            ->willReturn($fileMock);

        $parserMock = $this->getMock('\stdClass', ['renderImage']);
        $parserMock->expects($this->exactly(!empty($image) ? 1 : 0))
            ->method('renderImage')
            ->with('content', $bookmark::CONTAINER_WIDTH, $bookmark::CONTAINER_HEIGHT, 'jpeg')
            ->willReturn('an image');

        $bookmark->setFileStore($fileStoreMock);
        $bookmark->setParser($parserMock);

        $this->assertEquals(
            !empty($image) ? 'an image' : '',
            $bookmark->render()
        );
    }

    public function renderDataProvider()
    {
        return [
            [
                // Scotland
                [
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => true
                        ]
                    ]
                ],
                'TC_LOGO_SCOTTISH'
            ],
            [
                // Other
                [
                    'licence' => [
                        'trafficArea' => [
                            'isScotland' => false
                        ]
                    ]
                ],
                'TC_LOGO_OTHER'
            ],
            [
                // no data
                [],
                null
            ],
        ];
    }
}

<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TcSignature;

/**
 * TC Signature test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TcSignatureTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TcSignature();
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
    public function testRender($id, $image)
    {
        $bookmark = new TcSignature();
        $bookmark->setData(
            [
                'trafficArea' => [
                    'id' => $id
                ]
            ]
        );

        $fileMock = $this->getMock('\stdClass', ['getContent']);
        $fileMock->expects($this->once())
            ->method('getContent')
            ->willReturn('content');

        $fileStoreMock = $this->getMock('\stdClass', ['read']);
        $fileStoreMock->expects($this->once())
            ->method('read')
            ->with('/templates/Image/' . $image . '.jpg')
            ->willReturn($fileMock);

        $parserMock = $this->getMock('\stdClass', ['renderImage']);
        $parserMock->expects($this->once())
            ->method('renderImage')
            ->with('content', $bookmark::CONTAINER_WIDTH, $bookmark::CONTAINER_HEIGHT, 'jpeg')
            ->willReturn('an image');

        $bookmark->setFileStore($fileStoreMock);
        $bookmark->setParser($parserMock);

        $this->assertEquals(
            'an image',
            $bookmark->render()
        );
    }

    public function renderDataProvider()
    {
        return [
            ['B', 'TC_SIG_NORTHEASTERN'],
            ['C', 'TC_SIG_NORTHWESTERN'],
            ['D', 'TC_SIG_WESTMIDLANDS'],
            ['F', 'TC_SIG_EASTERN'],
            ['G', 'TC_SIG_WELSH'],
            ['H', 'TC_SIG_WESTERN'],
            ['K', 'TC_SIG_SE_MET'],
            ['M', 'TC_SIG_SCOTTISH'],
            ['N', 'TC_SIG_NORTHERNIRELAND']
        ];
    }
}

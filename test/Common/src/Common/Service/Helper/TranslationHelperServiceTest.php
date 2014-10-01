<?php

/**
 * Translation Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Service\Helper\TranslationHelperService;

/**
 * Translation Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslationHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\TranslationHelperService
     */
    private $sut;

    private $mockTranslator;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->mockTranslator = $this->getMock('\stdClass', array('translate'));
        $this->mockTranslator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translate')));

        $serviceManager = Bootstrap::getServiceManager();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('translator', $this->mockTranslator);

        $this->sut = new TranslationHelperService();
        $this->sut->setServiceLocator($serviceManager);
    }

    /**
     * Mock translate method
     */
    public function translate($message)
    {
        return '*' . $message . '*';
    }

    /**
     * @group helper_service
     * @group translation_helper_service
     */
    public function testGetTranslator()
    {
        $this->assertSame($this->mockTranslator, $this->sut->getTranslator());
    }

    /**
     * @group helper_service
     * @group translation_helper_service
     */
    public function testTranslate()
    {
        $this->assertEquals('*foo*', $this->sut->translate('foo'));
    }

    /**
     * @group helper_service
     * @group translation_helper_service
     */
    public function testWrapTranslation()
    {
        $format = 'This is a wrapped <div>%s</div>';
        $translation = 'translation';
        $expected = 'This is a wrapped <div>*translation*</div>';

        $this->assertEquals($expected, $this->sut->wrapTranslation($format, $translation));
    }

    /**
     * @group helper_service
     * @group translation_helper_service
     */
    public function testFormatTranslation()
    {
        $format = 'This is a formatted <div>%s</div> message to %s multiple %s';
        $translations = array(
            'translation',
            'demonstrate',
            'replacements'
        );
        $expected = 'This is a formatted <div>*translation*</div> message to *demonstrate* multiple *replacements*';

        $this->assertEquals($expected, $this->sut->formatTranslation($format, $translations));
    }

    /**
     * @group helper_service
     * @group translation_helper_service
     */
    public function testFormatTranslationWithSingleMessage()
    {
        $format = 'This is a formatted <div>%s</div>';
        $translations = 'translation';
        $expected = 'This is a formatted <div>*translation*</div>';

        $this->assertEquals($expected, $this->sut->formatTranslation($format, $translations));
    }
}

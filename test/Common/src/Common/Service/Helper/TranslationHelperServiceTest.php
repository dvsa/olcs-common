<?php

/**
 * Translation Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use Common\Service\Helper\TranslationHelperService;
use Laminas\I18n\Translator\Translator;

/**
 * Translation Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslationHelperServiceTest extends \PHPUnit\Framework\TestCase
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
    protected function setUp(): void
    {
        $this->mockTranslator = $this->createPartialMock(Translator::class, array('translate'));
        $this->mockTranslator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translate')));

        $this->sut = new TranslationHelperService($this->mockTranslator);
    }

    /**
     * Mock translate method
     */
    public function translate($message, $domain, $locale)
    {
        $translation = '';
        if ($locale === 'cy_GB') {
            $translation .= 'WELSH';
        }
        $translation .= '*' . $message . '*';
        return $translation;
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

    public function testFormatReplace()
    {
        $index = 'this %s is %sing %ssome';
        $arguments = ['foo', 'bar', 'awe'];

        $response = $this->sut->translateReplace($index, $arguments);

        $this->assertEquals('*this foo is baring awesome*', $response);
    }

    public function testTranslateWelsh()
    {
        $this->assertEquals('WELSH*foo*', $this->sut->translate('foo', 'Y'));
    }

    public function testFormatReplaceWelsh()
    {
        $index = 'this %s is %sing %ssome';
        $arguments = ['foo', 'bar', 'awe'];

        $response = $this->sut->translateReplace($index, $arguments, 'Y');

        $this->assertEquals('WELSH*this foo is baring awesome*', $response);
    }
}

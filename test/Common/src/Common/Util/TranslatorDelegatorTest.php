<?php

/**
 * Translator Delegator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Util;

use Common\Util\TranslatorDelegator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegatorTest extends MockeryTestCase
{
    protected $sut;
    protected $mockTranslator;

    public function setUp()
    {
        $this->mockTranslator = m::mock(Translator::class);
        $this->mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($message, $textDomain, $locale) {
                    return 'translated-' . $message;
                }
            );

        $translations = [
            '{{foo}}' => 'bar',
            '{{bar}}' => 'foo'
        ];

        $this->sut = new TranslatorDelegator($this->mockTranslator, $translations);
    }

    public function testTranslate()
    {
        $this->assertEquals('translated-no-replacements', $this->sut->translate('no-replacements'));

        $this->assertEquals('translated-replace-bar', $this->sut->translate('replace-{{foo}}'));
        $this->assertEquals('translated-replace-foo-bar', $this->sut->translate('replace-{{bar}}-{{foo}}'));
    }

    public function testTranslateNull()
    {
        $this->assertEquals('', $this->sut->translate(null));
    }

    public function testCall()
    {
        $this->mockTranslator->shouldReceive('setLocale')->once();

        $this->sut->setLocale();
    }
}

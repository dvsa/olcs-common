<?php

namespace CommonTest\Form\View\Helper;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\TranslateableTextHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TranslateableTextHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TranslateableTextHandlerTest extends MockeryTestCase
{
    public function testTranslate()
    {
        $translateableTextKey = 'textKey';

        $translateableTextParameters = [
            'textParam1',
            'textParam2'
        ];

        $translateableText = [
            'key' => $translateableTextKey,
            'parameters' => $translateableTextParameters
        ];

        $translated = 'testTranslateableText';

        $translationHelper = m::mock(TranslationHelperService::class);
        $translationHelper->shouldReceive('translateReplace')
            ->with($translateableTextKey, $translateableTextParameters)
            ->andReturn($translated);

        $sut = new TranslateableTextHandler($translationHelper);

        $this->assertEquals(
            $translated,
            $sut->translate($translateableText)
        );
    }
}

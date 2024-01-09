<?php

/**
 * ActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Common\Util\Escape;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\ActionLinks;
use Laminas\Mvc\I18n\Translator;

/**
 * ActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ActionLinksTest extends MockeryTestCase
{
    protected $sut;
    protected $table;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $this->sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

        $this->table = m::mock();
        $this->table->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);

        $this->sut = new ActionLinks($this->table);
    }

    public function testRender(): void
    {
        $mockTranslate = $this->getTranslator();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'deleteInputName' => 'table[action][delete][%d]',
            'replaceInputName' => 'table[action][replace][%d]',
            'isRemoveVisible' => function ($data) {
                return true;
            },
            'isReplaceVisible' => function ($data) {
                return true;
            },
        ];
        $data = [
            'id' => 123
        ];

        $classes = Escape::htmlAttr('right-aligned govuk-button govuk-button--secondary trigger-modal');
        $nameRemove = Escape::htmlAttr('table[action][delete][123]');
        $ariaRemove = Escape::htmlAttr('Remove Aria (id 123)');
        $nameReplace = Escape::htmlAttr('table[action][replace][123]');
        $ariaReplace = Escape::htmlAttr('Replace Aria (id 123)');

        $expected = '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="' . $classes . '" '.
            'name="' . $nameRemove . '" ' .
            'aria-label="' . $ariaRemove . '">Remove</button> <button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="' . $classes . '" ' .
            'name="' . $nameReplace . '" aria-label="' . $ariaReplace . '">Replace</button>';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    public function testRenderDefault(): void
    {
        $mockTranslate = $this->getTranslator();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'replaceInputName' => 'table[action][replace][%d]',
        ];
        $data = [
            'id' => 123
        ];

        $classes = Escape::htmlAttr('right-aligned govuk-button govuk-button--secondary trigger-modal');
        $name = Escape::htmlAttr('table[action][delete][123]');
        $aria = Escape::htmlAttr('Remove Aria (id 123)');

        $expected = '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="' . $classes . '" '.
            'name="' . $name . '" aria-label="' . $aria . '">Remove</button>';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    public function testRenderNoModal(): void
    {
        $mockTranslate = $this->getTranslator();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'replaceInputName' => 'table[action][replace][%d]',
            'dontUseModal' => true,
        ];
        $data = [
            'id' => 123
        ];

        $classes = Escape::htmlAttr('right-aligned govuk-button govuk-button--secondary');
        $name = Escape::htmlAttr('table[action][delete][123]');
        $aria = Escape::htmlAttr('Remove Aria (id 123)');

        $expected = '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="' . $classes . '" '.
            'name="' . $name . '" aria-label="' . $aria . '">Remove</button>';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    public function testRenderWithCustomActionClasses(): void
    {
        $mockTranslate = $this->getTranslator();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'replaceInputName' => 'table[action][replace][%d]',
            'dontUseModal' => true,
            'actionClasses' => 'my-custom-class'
        ];
        $data = [
            'id' => 123
        ];

        $classes = Escape::htmlAttr('my-custom-class');
        $name = Escape::htmlAttr('table[action][delete][123]');
        $aria = Escape::htmlAttr('Remove Aria (id 123)');

        $expected = '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="' . $classes . '" '.
            'name="' . $name . '" aria-label="' . $aria . '">Remove</button>';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    private function getTranslator(): m\MockInterface
    {
        $translator = m::mock(Translator::class);
        $translator->expects('translate')->with(ActionLinks::KEY_ACTION_LINKS_REMOVE)->andReturn('Remove');
        $translator->expects('translate')->with(ActionLinks::KEY_ACTION_LINKS_REMOVE_ARIA)->andReturn('Remove Aria');
        $translator->expects('translate')->with(ActionLinks::KEY_ACTION_LINKS_REPLACE)->andReturn('Replace');
        $translator->expects('translate')->with(ActionLinks::KEY_ACTION_LINKS_REPLACE_ARIA)->andReturn('Replace Aria');

        return $translator;
    }
}

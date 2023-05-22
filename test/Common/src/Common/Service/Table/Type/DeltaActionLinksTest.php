<?php

/**
 * DeltaActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Common\Util\Escape;
use Laminas\Mvc\I18n\Translator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\DeltaActionLinks;
use CommonTest\Bootstrap;

/**
 * DeltaActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DeltaActionLinksTest extends MockeryTestCase
{
    protected $sut;
    protected $table;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->table = m::mock();
        $this->table->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);

        $this->sut = new DeltaActionLinks($this->table);
    }

    /**
     * @dataProvider tableDataProvider
     */
    public function testRender($data, $expected)
    {
        $mockTranslate = $this->getTranslator($data['action'] ?? null);

        $this->sm->setService('translator', $mockTranslate);

        $this->assertEquals($expected, $this->sut->render($data, []));
    }

    public function tableDataProvider()
    {
        $escapedAriaRemove = Escape::htmlAttr('Remove Aria (id 123)');
        $escapedAriaRestore = Escape::htmlAttr('Restore Aria (id 456)');

        return [
            [
                [
                    'id' => 123,
                    'action' => 'A'
                ],
                '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="right-aligned govuk-button govuk-button--secondary trigger-modal" '.
                    'name="table[action][delete][123]" aria-label="' . $escapedAriaRemove . '">Remove</button>'
            ],
            [
                [
                    'id' => 456,
                    'action' => 'D'
                ],
                '<button data-prevent-double-click="true" data-module="govuk-button" type="submit" class="right-aligned govuk-button govuk-button--secondary" '.
                    'name="table[action][restore][456]" aria-label="' . $escapedAriaRestore . '">Restore</button>'
            ],
            [
                [
                    'id' => 789
                ],
                ''
            ]
        ];
    }

    private function getTranslator(?string $action): m\MockInterface
    {
        if ($action === null) {
            $removeTimes = 0;
            $restoreTimes = 0;
        } elseif ($action === 'A') {
            $removeTimes = 1;
            $restoreTimes = 0;
        } else {
            $removeTimes = 0;
            $restoreTimes = 1;
        }

        $translator = m::mock(Translator::class);
        $translator->expects('translate')->with(DeltaActionLinks::KEY_ACTION_LINKS_REMOVE)->andReturn('Remove')->times($removeTimes);
        $translator->expects('translate')->with(DeltaActionLinks::KEY_ACTION_LINKS_REMOVE_ARIA)->andReturn('Remove Aria')->times($removeTimes);;
        $translator->expects('translate')->with(DeltaActionLinks::KEY_ACTION_LINKS_RESTORE)->andReturn('Restore')->times($restoreTimes);;
        $translator->expects('translate')->with(DeltaActionLinks::KEY_ACTION_LINKS_RESTORE_ARIA)->andReturn('Restore Aria')->times($restoreTimes);;

        return $translator;
    }
}

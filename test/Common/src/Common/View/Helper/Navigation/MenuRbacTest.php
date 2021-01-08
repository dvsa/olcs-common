<?php

namespace CommonTest\View\Helper\Navigation;

use Common\View\Helper\Navigation\MenuRbac;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Navigation\AbstractContainer;
use Laminas\Navigation\Page\AbstractPage;

/**
 * @covers \Common\View\Helper\Navigation\MenuRbac
 */
class MenuRbacTest extends MockeryTestCase
{
    public function testFilter()
    {
        $mockPage1 = m::mock(AbstractPage::class)->makePartial();
        $mockPage2 = m::mock(AbstractPage::class)->makePartial();
        $mockPage3 = m::mock(AbstractPage::class)->makePartial();

        /** @var AbstractContainer | m\MockInterface $mockCntr */
        $mockCntr = m::mock(AbstractContainer::class)->makePartial();
        $mockCntr->setPages(
            [
                $mockPage1,
                $mockPage2,
                $mockPage3,
            ]
        );

        //  @ - need because Mock::__call have different declaration vs AbstractHandler::__call
        /** @var MenuRbac | m\MockInterface $sut */
        $sut = @m::mock(MenuRbac::class)->makePartial();

        $sut->setContainer($mockCntr);
        $sut->shouldReceive('accept')->once()->with($mockPage1, false)->andReturn(false)
            ->shouldReceive('accept')->once()->with($mockPage2, false)->andReturn(true)
            ->shouldReceive('accept')->once()->with($mockPage3, false)->andReturn(false);

        $actual = $sut();

        static::assertSame($sut, $actual);

        $pages = $actual->getContainer()->getPages();
        static::assertCount(1, $actual->getContainer()->getPages());
        static::assertSame($mockPage2, current($pages));
    }
}

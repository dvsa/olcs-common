<?php

namespace CommonTest\Form\View\Helper\Readonly;

use Common\Form\Elements\Types\Address;
use Common\Form\Elements\Types\FileUploadList;
use Common\Form\Elements\Types\FileUploadListItem;
use Common\Form\View\Helper;
use Common\Form\View\Helper\Readonly\FormFileUploadList;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;

/**
 * @covers \Common\Form\View\Helper\Readonly\FormFileUploadList
 */
class FormFileUploadListTest extends MockeryTestCase
{
    public function testRenderInvalidElement()
    {
        $this->setExpectedException(\Exception::class, 'Parameter must be instance of ' . FileUploadList::class);

        $sut = new FormFileUploadList();
        $sut->render(m::mock(FieldsetInterface::class));
    }

    public function testRenderNotItems()
    {
        $sut = new FormFileUploadList();

        static::assertEquals('', $sut(new FileUploadList()));
    }

    public function testRender()
    {
        $mockUplElmChildItem = m::mock(ElementInterface::class)
            ->shouldReceive('getName')->withAnyArgs()->andReturn('unit_elm1')
            ->shouldReceive('setOption')->with('disable_html_escape', true)->times(4)
            ->getMock();

        $mockFileItem = (new FileUploadListItem('unit_UplItem'))
            ->add($mockUplElmChildItem)
            ->add(clone $mockUplElmChildItem);

        $mockOtherElm = m::mock(Address::class);
        $mockOtherElm->shouldReceive('getName')->withAnyArgs()->andReturn('unit_Address');

        $list = (new FileUploadList())
            ->add($mockFileItem)
            ->add($mockOtherElm)
            ->add($mockFileItem);

        $mockFormItem = m::mock(Helper\Readonly\FormItem::class);
        $mockFormItem->shouldReceive('render')->andReturn('_FORM_ITEM_RENDER_RESULT_');

        $mockView = m::mock(\Zend\View\Renderer\PhpRenderer::class);
        $mockView
            ->shouldReceive('plugin')->with('readonlyformitem')->andReturn($mockFormItem)
            ->shouldReceive('translate')->andReturnUsing(
                function ($arg) {
                    return '_TRANSL_' . $arg;
                }
            );

        $sut = new FormFileUploadList();
        $sut->setView($mockView);

        static::assertEquals(
            '<div class="help__text">' .
            '<h3 class="file__heading">_TRANSL_common.file-upload.table.col.FileName</h3>' .
            '<ul class="js-upload-list">' .
            '<li class="file">_FORM_ITEM_RENDER_RESULT__FORM_ITEM_RENDER_RESULT_</li>' .
            '<li class="file">_FORM_ITEM_RENDER_RESULT__FORM_ITEM_RENDER_RESULT_</li>' .
            '</ul>' .
            '</div>',
            $sut($list)
        );
    }
}

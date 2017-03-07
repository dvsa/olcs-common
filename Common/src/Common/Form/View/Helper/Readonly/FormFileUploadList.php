<?php

namespace Common\Form\View\Helper\Readonly;

use Common\Form\Elements\Types\FileUploadList;
use Common\Form\Elements\Types\FileUploadListItem;
use Zend\Form\Element as ZendElement;
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class FormFileUploadList extends AbstractHelper
{
    /**
     * Invoke helper as function. Proxies to {@link render()}.
     *
     * @param FieldsetInterface|null $element Element
     *
     * @return string
     */
    public function __invoke(FieldsetInterface $element = null)
    {
        return $this->render($element);
    }

    /**
     * Render element
     *
     * @param FileUploadList $fs File Upload List fieldset element
     *
     * @return string
     */
    public function render(FieldsetInterface $fs)
    {
        if (!($fs instanceof FileUploadList)) {
            throw new \Exception('Parameter must be instance of ' . FileUploadList::class);
        }

        $fsHtml = [];
        foreach ($fs->getIterator() as $fieldset) {
            if (!($fieldset instanceof FileUploadListItem)) {
                continue;
            }

            $elmHtml = [];
            foreach ($fieldset->getIterator() as $elm) {
                $elm->setOption('disable_html_escape', true);

                $elmHtml[] = $this->view->plugin('readonlyformitem')->render($elm);
            }

            $fsHtml[] = '<li class="file">' . implode('', $elmHtml) . '</li>';
        }

        return '<ul class="js-upload-list">' . implode('', $fsHtml) . '</ul>';
    }
}

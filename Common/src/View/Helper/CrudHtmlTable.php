<?php
namespace Common\View\Helper;

use Zend\View\Helper\Partial as ZendPartial;

class CrudHtmlTable extends ZendPartial
{
    /**
     * Generates a 'Table' element.
     *
     * @return string The table XHTML.
     */
    public function __invoke($name = null, $values = null)
    {
        if (empty($name)) {
            $name = __DIR__ . '/crud-table.phtml';
        }

        return parent::__invoke($name, ['records' => $values]);
    }
}
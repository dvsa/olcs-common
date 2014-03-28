<?php
namespace Common\View\Helper;

use Zend\View\Helper\AbstractHtmlElement as ZendAbstractHtmlElement;

class HtmlTable extends ZendAbstractHtmlElement
{
    /**
     * Generates a 'Table' element.
     *
     * @param  array $records Indexed array of associative arrays containing records.
     * @param  array $attribs Attributes for the table tag.
     *
     * @return string The table XHTML.
     */
    public function __invoke(array $records, $attribs = false)
    {
        $list = '';

        $fieldsMarkup = '';

        foreach ($records as $record) {

            if (!is_array($records)) {
                throw new \Exception(
                    '$records should be an indexed array of associative arrays containing records'
                );
            }

            if (!isset($headers)) { // only do this once.
                $headerNames = array_keys($record);
                $headerMarkup = '<thead><tr>';
                foreach ($headerNames as $headerName) {
                    $headerMarkup .= '<th>' . $headerName . '</th>';
                }
                $headerMarkup .= '</tr></thead>';
            }

            $fieldsMarkup .= '<tbody><tr>';
            foreach ($record as $field) {

                $fieldsMarkup .= '<td>' . $field . '</td>';
            }
            $fieldsMarkup .= '</tr></tbody>';
        }

        $cont = $headerMarkup . PHP_EOL . $fieldsMarkup;

        if ($attribs) {
            $attribs = $this->htmlAttribs($attribs);
        } else {
            $attribs = '';
        }

        $tag = 'table';

        return '<div class="table__wrapper"><' . $tag . $attribs . '>' . PHP_EOL . $cont . '</' . $tag . '></div>' . PHP_EOL;
    }
}
<?php

/**
 * Table Builder
 *
 * Builds a table from config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table;

/**
 * Table Builder
 *
 * Builds a table from config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableBuilder
{
    const TYPE_DEFAULT = 1;

    const TYPE_PAGINATE = 2;

    const TYPE_CRUD = 3;

    /**
     * Inject the application config from Zend
     *
     * @var array
     */
    private $applicationConfig = array();

    /**
     * Table settings
     *
     * @var array
     */
    private $settings = array();

    /**
     * Table variables
     *
     * @var array
     */
    private $variables = array();

    /**
     * Table attributes
     *
     * @var array
     */
    private $attributes = array();

    /**
     * Table column settings
     *
     * @var array
     */
    private $columns = array();

    /**
     * Cached partials
     *
     * @var array
     */
    private $partials = array();

    /**
     * Pre-defined widths
     *
     * @var array
     */
    private $widths = array(
        'checkbox' => '16px'
    );

    /**
     * Pre-defined formatters
     *
     * @var array
     */
    private $formatters = array(
        '_date' => 'formatterDate'
    );

    /**
     * Total count of results
     *
     * @var int
     */
    private $total;

    /**
     * Data rows
     *
     * @var array
     */
    private $rows = array();

    /**
     * Table type
     *
     * @var int
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * Inject the application config
     *
     * @param array $applicationConfig
     */
    public function __construct($applicationConfig = array())
    {
        $this->applicationConfig = $applicationConfig;
    }

    /**
     * Build a table from a config file
     *
     * @param array $config
     * @return string
     */
    public function buildTable($name, $data = array())
    {
        $this->loadConfig($name);

        $this->loadData($data);

        return $this->replaceContent($this->renderTable(), $this->variables);
    }

    /**
     * Decide the view and begin the render
     *
     * @return string
     */
    public function renderTable()
    {
        if (isset($this->settings['crud'])) {

            $this->type = self::TYPE_CRUD;
            return $this->renderLayout('crud');
        }

        if (isset($this->settings['paginate'])) {

            $this->type = self::TYPE_PAGINATE;
        }

        return $this->renderLayout('default');
    }

    /**
     * Render partial
     *
     * @param string $name
     * @return string
     */
    public function renderLayout($name)
    {
        $partialFile = $this->applicationConfig['tables']['partials'] . 'layouts/' . $name . '.phtml';

        if (!file_exists($partialFile)) {

            throw new \Exception('Table partial not found');
        }

        ob_start();
            require($partialFile);
            $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Render the total if we have a paginated table
     *
     * @return string
     */
    public function renderTotal()
    {
        if ($this->type !== self::TYPE_PAGINATE) {

            return '';
        }

        $total = $this->total . ' result' . ($this->total !== 1 ? 's' : '');

        return $this->replaceContent(' {{[elements/]}}', array('total' => $total));
    }

    /**
     * Render actions
     *
     * @return string
     */
    public function renderActions()
    {
        if ($this->type !== self::TYPE_CRUD) {
            return '';
        }

        $actions = isset($this->settings['crud']['actions']) ? $this->settings['crud']['actions'] : array();

        if (count($this->rows) === 0) {
            foreach ($actions as $key => $details) {
                if (isset($details['requireRows']) && $details['requireRows']) {
                    unset($actions[$key]);
                }
            }
        }

        if (empty($actions)) {
            return '';
        }

        $newActions = array();

        foreach ($actions as $name => $details) {
            $value = isset($details['value']) ? $details['value'] : ucwords($name);

            $class = isset($details['class']) ? $details['class'] : 'action--secondary';

            $newActions[] = array(
                'name' => $name,
                'label' => $value,
                'class' => $class
            );
        }

        if (count($actions) > 3) {
            return $this->renderDropdownActions($actions);
        }

        return $this->renderButtonActions($actions);
    }

    /**
     * Render the dropdown version of the actions
     *
     * @param array $actions
     * @return string
     */
    public function renderDropdownActions($actions = array())
    {
        $options = '';

        foreach ($actions as $details) {
            $options .= $this->replaceContent('{{[elements/actionOption]}}', $details);
        }

        return $this->replaceContent('{{[elements/actionSelect]}}', array('option' => $options));
    }

    /**
     * Render the button version of the actions
     *
     * @param array $actions
     * @return string
     */
    public function renderButtonActions($actions = array())
    {
        $content = '';

        foreach ($actions as $details) {
            $content .= $this->replaceContent('{{[elements/actionButton]}}', $details);
        }

        return $content;
    }

    /**
     * Render footer
     *
     * @return string
     */
    public function renderFooter()
    {
        if ($this->type !== self::TYPE_PAGINATE) {
            return '';
        }

        if ($this->total <= min($this->settings['paginate']['limit']['options'])) {
            return '';
        }

        return $this->renderPartial('pagination');
    }

    /**
     * Render the limit options
     *
     * @string
     */
    public function renderLimitOptions()
    {
        if (empty($this->settings['paginate']['limit']['options'])) {
            return '';
        }

        $content .= '';

        foreach ($this->settings['paginate']['limit']['options'] as $option) {

            $currentOption = filter_input(INPUT_GET, 'limit');

            if (empty($currentOption)) {
                $currentOption = $this->settings['paginate']['limit']['default'];
            }

            $class = '';

            if ($option == $currentOption) {
                $class = 'current';
            } else {
                $option = $this->replaceContent('{{[elements/limitLink]}}', array('option' => $option));
            }

            $content .= $this->replaceContent('{{[elements/limitOption]}}', array('class' => $class, 'option' => $option));
        }

        return $content;
    }

    /**
     * Return a setting or the default
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get the data rows
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Render a header column
     *
     * @todo Add checks for sorting
     *
     * @param array $column
     * @param string $wrapper
     * @return string
     */
    public function renderHeaderColumn($column, $wrapper = '{{[elements/th]}}')
    {
        if (isset($column['sort'])) {

            $column['class'] = 'sortable';

            $column['order'] = 'ASC';

            if ($column['sort'] === filter_input(INPUT_GET, 'sort')) {

                if (filter_input(INPUT_GET, 'order') === 'ASC') {

                    $column['order'] = 'DESC';

                    $column['class'] .= ' ascending';
                } else {

                    $column['class'] .= ' descending';
                }
            }

            $column['title'] = $this->replaceContent('{{[elements/sortColumn]}}', $column);
        }

        if (isset($column['width']) && isset($this->widths[$column['width']])) {

            $column['width'] = $this->widths[$column['width']];
        }

        return $this->replaceContent($wrapper, $column);
    }

    /**
     * Render a body column
     *
     * @param array $row
     * @param array $column
     * @param string $wrapper
     * @return string
     */
    public function renderBodyColumn($row, $column, $wrapper = '{{[elements/td]}}')
    {
        if (isset($column['formatter'])) {

            if (is_callable($column['formatter'])) {

                $column['callback'] = $column['formatter'];

            } elseif (is_string($column['formatter']) && isset($this->formatters[$column['formatter']])) {

                $column['callback'] = array($this, $this->formatters[$column['formatter']]);
            }
        }

        if (isset($column['callback'])) {
            $return = call_user_func($column['callback'], $row, $column);

            if (is_array($return)) {

                $row = array_merge($row, $return);

            } else {

                $content = $return;
                $row['content'] = $content;
            }
        }

        if (isset($column['format'])) {

            $content = $this->replaceContent($column['format'], $row);

        }

        if (!isset($content) || empty($content)) {

            $content = isset($column['name']) && isset($row[$column['name']]) ? $row[$column['name']] : '';
        }

        return $this->replaceContent($wrapper, array('content' => $content));
    }

    public function renderPagination()
    {
        if ($this->getSetting('paginate', false) !== false) {

            return '';
        } else {

            return $this->renderLayout('pagination');
        }
    }

    /**
     * Render an attribute string
     *
     * @param array $attrs
     * @return string
     */
    public function renderAttributes($attrs = array())
    {
        $attributes = array();

        foreach ($attrs as $name => $value) {
            $attributes[] = $name .= '"' . $value . '"';
        }

        return implode(' ', $attributes);
    }

    /**
     * Render header
     *
     * @param string $wrapper
     * @return string
     */
    /**public function renderHeader($wrapper = '{{[elements/title]}}')
    {
        return $this->replaceContent($wrapper, array('title' => $this->getSetting('title', '')));
    }*/

    /**
     * Replace vars into content
     *
     * @param string $content
     * @param array $vars
     * @return string
     */
    private function replaceContent($content, $vars = array())
    {
        $content = $this->replacePartials($content);

        foreach ($vars as $key => $val) {
            if (is_string($val)) {
                $content = str_replace('{{' . $key . '}}', $val, $content);
            }
        }

        $content = preg_replace('/(\{\{[a-zA-Z0-9\/\[\]]+\}\})/', '', $content);

        return $content;
    }

    /**
     * Replace partials in the content
     *
     * @param string $content
     * @return string
     */
    private function replacePartials($content)
    {
        if (preg_match_all('/(\{\{\[([a-zA-Z\/]+)\]\}\})/', $content, $matches)) {

            $partials = array();

            foreach ($matches[2] as $match) {

                $partials[$match] = $match;
            }

            foreach ($partials as $partial) {

                $content = str_replace('{{[' . $partial .']}}', $this->getPartial($partial), $content);
            }
        }

        return $content;
    }

    /**
     * Get a partials content
     *
     * @param string $partial
     * @return string
     */
    private function getPartial($partial)
    {
        if (!isset($this->partials[$partial])) {

            $this->partials[$partial] = '';

            $filename = $this->applicationConfig['tables']['partials'] . $partial . '.phtml';

            if (file_exists($this->applicationConfig['tables']['partials'] . $partial . '.phtml')) {
                $this->partials[$partial] = file_get_contents($filename);
            }
        }

        return $this->partials[$partial];
    }

    /**
     * Load the configuration if it exists
     *
     * @param string $name
     * @throws \Exception
     */
    private function loadConfig($name)
    {
        $configFile = $this->applicationConfig['tables']['config'] . $name . '.table.php';

        if (!file_exists($configFile)) {

            throw new \Exception('Table configuration not found');
        }

        $config = include($configFile);

        $this->settings = isset($config['settings']) ? $config['settings'] : array();

        $this->attributes = isset($config['attributes']) ? $config['attributes'] : array();
        $this->columns = isset($config['columns']) ? $config['columns'] : array();
        $this->variables = isset($config['variables']) ? $config['variables'] : array();
    }

    /**
     * Load data, set the rows and the total count for pagination
     *
     * @param array $data
     */
    private function loadData($data = array())
    {
        $this->rows = isset($data['Results']) ? $data['Results'] : $data;
        $this->total = isset($data['Count']) ? $data['Count'] : count($this->rows);
    }

    /**
     * Format dates
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public function formatterDate($data, $column)
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = 'd/m/Y';
        }

        return date($column['dateformat'], strtotime($data[$column['name']]));
    }
}

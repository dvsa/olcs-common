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

    private $applicationConfig = array();

    private $defaultSettings = array(
        'view' => 'default'
    );

    private $settings = array();

    private $attributes = array();

    private $columns = array();

    private $partials = array();

    private $widths = array(
        'checkbox' => '16px'
    );

    /**
     * Pass in the application config
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

        return $this->renderPartial($this->getSetting('view'));
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
        if (isset($column['format'])) {

            $content = $this->replaceContent($column['format'], $row);

        } else {

            $content = isset($row[$column['name']]) ? $row[$column['name']] : '';
        }

        return $this->replaceContent($wrapper, array('content' => $content));
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
    public function renderHeader($wrapper = '{{[elements/title]}}')
    {
        return $this->replaceContent($wrapper, array('title' => $this->getSetting('title', '')));
    }

    /**
     * Render actions
     *
     * @param string $wrapper
     * @return string
     */
    public function renderActions($wrapper = '')
    {
        $actions = $this->getSetting('actions', array());

        $content = '';

        foreach ($actions as $name => $details) {
            $value = isset($details['value']) ? $details['value'] : ucwords($name);

            $class = isset($details['class']) ? $details['class'] : 'action--secondary';

            $content .= $this->replaceContent(
                $wrapper,
                array(
                    'action_class' => $class,
                    'action_name' => $name,
                    'action_value' => $value
                )
            );
        }

        return $content;
    }

    /**
     * Render partial
     *
     * @param string $name
     * @return string
     */
    public function renderPartial($name)
    {
        $partialFile = $this->applicationConfig['tables']['partials'] . $name . '.phtml';

        if (!file_exists($partialFile)) {

            throw new \Exception('Table partial not found');
        }

        ob_start();
            include($partialFile);
            $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

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
            $content = str_replace('{{' . $key . '}}', $val, $content);
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

        $this->settings = array_merge(
            $this->defaultSettings,
            isset($config['settings']) ? $config['settings'] : array()
        );

        $this->attributes = isset($config['attributes']) ? $config['attributes'] : array();
        $this->columns = isset($config['columns']) ? $config['columns'] : array();
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
}

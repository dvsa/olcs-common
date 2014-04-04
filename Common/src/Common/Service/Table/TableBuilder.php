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

    const DEFAULT_LIMIT = 10;
    const DEFAULT_PAGE = 1;

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
     * Current limit
     *
     * @var int
     */
    private $limit = self::DEFAULT_LIMIT;

    /**
     * Current page
     *
     * @var int
     */
    private $page = 1;

    /**
     * Current page deviation
     *
     * @var int
     */
    private $pageDeviation = 2;

    /**
     * Url plugin
     *
     * @var object
     */
    private $url;

    /**
     * Current sort column
     *
     * @var string
     */
    private $sort;

    /**
     * Current sort order
     *
     * @var string
     */
    private $order = 'ASC';

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
    public function buildTable($name, $data = array(), $params = array())
    {
        $this->loadConfig($name);

        $this->loadData($data);

        $this->loadParams($params);

        $this->variables['action'] = isset($this->variables['action']) ? $this->variables['action'] : $this->generateUrl();

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

        return $this->replaceContent(' {{[elements/total]}}', array('total' => $total));
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

            $class = isset($details['class']) ? $details['class'] : 'secondary';

            $newActions[] = array(
                'name' => $name,
                'label' => $value,
                'class' => $class
            );
        }

        if (count($newActions) > 3) {
            $content = $this->renderDropdownActions($newActions);
        } else {
            $content = $this->renderButtonActions($newActions);
        }

        return $this->replaceContent('{{[elements/actionContainer]}}', array('content' => $content));
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

        if (!in_array($this->limit, $this->settings['paginate']['limit']['options'])) {
            $this->settings['paginate']['limit']['options'][] = $this->limit;
            sort($this->settings['paginate']['limit']['options']);
        }

        if ($this->total <= min($this->settings['paginate']['limit']['options'])) {
            return '';
        }

        return $this->renderLayout('pagination');
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

        $content = '';

        foreach ($this->settings['paginate']['limit']['options'] as $option) {

            $class = '';

            $option = (string)$option;

            if ($option == $this->limit) {
                $class = 'current';
            } else {
                $details = array(
                    'option' => $option,
                    'link' => $this->generateUrl(array('page' => 1, 'limit' => $option))
                );
                $option = $this->replaceContent('{{[elements/limitLink]}}', $details);
            }

            $content .= $this->replaceContent('{{[elements/limitOption]}}', array('class' => $class, 'option' => $option));
        }

        return $content;
    }

    /**
     * Render pagination options
     *
     * @return string
     */
    public function renderPageOptions()
    {
        $options = array();

        $totalPages = ceil($this->total / $this->limit);

        // Show previous
        if ($this->page > 1) {

            $options[] = array(
                'page' => (string)($this->page - 1),
                'label' => 'Previous'
            );
        }

        // We will always have a page 1
        $options[] = array(
            'page' => '1',
            'label' => '1',
            'class' => ($this->page == 1 ? 'current' : null)
        );

        // If we have more than 1 page
        if ($totalPages > 1) {

            // Total pages that will be displayed e.g. 1 ... 34567 ... 9
            $totalPagesToDisplay = ($this->pageDeviation * 2) + 3;

            // If we don't have too many pages, just show them all
            if ($totalPages <= $totalPagesToDisplay) {

                for ($i = 2; $i <= $totalPages; $i++) {
                    $options[] = array(
                        'page' => (string)$i,
                        'label' => (string)$i,
                        'class' => ($this->page == $i ? 'current' : null)
                    );
                }
            } else {

                $lowerRange = max(
                    array(
                        2,
                        ($this->page - $this->pageDeviation) - ($this->page >= ($totalPages - 2) ? (2 - ($totalPages - $this->page)) : 0)
                    )
                );

                $upperRange  = min(
                    array(
                        ($totalPages - 1),
                        ($this->page + $this->pageDeviation) + ($this->page <= 2 ? (3 - $this->page) : 0)
                    )
                );

                if ($lowerRange > 2) {

                    $options[] = array(
                        'page' => null,
                        'label' => '...'
                    );
                }

                $i = $lowerRange;

                while ($i <= $upperRange) {

                    $options[] = array(
                        'page' => (string)$i,
                        'label' => (string)$i,
                        'class' => ($this->page == $i ? 'current' : null)
                    );
                    $i++;
                }

                if ($upperRange <= ($totalPages - 2)) {

                    $options[] = array(
                        'page' => null,
                        'label' => '...'
                    );
                }

                $options[] = array(
                    'page' => (string)$totalPages,
                    'label' => (string)$totalPages,
                    'class' => ($this->page == (string)$totalPages ? 'current' : null)
                );
            }

            if ($this->page < $totalPages) {

                $options[] = array(
                    'page' => (string)($this->page + 1),
                    'label' => 'Next'
                );
            }
        }

        $content = '';

        foreach ($options as $details) {

            if (is_null($details['page']) || (string)$this->page == $details['page']) {
                $details['option'] = $details['label'];
            } else {
                $details['link'] = $this->generateUrl(array('page' => $details['page']));
                $details['option'] = $this->replaceContent('{{[elements/paginationLink]}}', $details);
            }

            $details = array_merge(array('class' => ''), $details);

            $content .= $this->replaceContent('{{[elements/paginationItem]}}', $details);
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

            if ($column['sort'] === $this->sort) {

                if ($this->order === 'ASC') {

                    $column['order'] = 'DESC';

                    $column['class'] .= ' ascending';
                } else {

                    $column['class'] .= ' descending';
                }
            }

            $column['link'] = $this->generateUrl(array('sort' => $column['sort'], 'order' => $column['order']));

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

    /**
     * Render pagination
     *
     * Render actions
     *
     * @return string
     */
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

            $attributes[] = $name .= '="' . $value . '"';
        }

        return implode(' ', $attributes);
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

            if (is_string($val) || is_numeric($val)) {

                $content = str_replace('{{' . $key . '}}', (string)$val, $content);
            }
        }

        return preg_replace('/(\{\{[a-zA-Z0-9\/\[\]]+\}\})/', '', $content);
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
     * Load params
     *
     * @param array $array
     */
    private function loadParams($array = array())
    {
        if (isset($array['limit'])) {

            $this->limit = $array['limit'];

        } elseif(isset($this->settings['paginate']['limit']['default'])) {

            $this->limit = (int)$this->settings['paginate']['limit']['default'];
        }

        $this->page = isset($array['page']) ? $array['page'] : self::DEFAULT_PAGE;

        if (!isset($array['url'])) {

            throw new \Exception('Table helper requires the URL helper');
        }

        $this->url = $array['url'];

        $this->sort = isset($array['sort']) ? $array['sort'] : '';

        $this->order = isset($array['order']) ? $array['order'] : 'ASC';
    }

    /**
     * Generate url
     *
     * @param array $data
     * @return string
     */
    private function generateUrl($data = array())
    {
        return $this->url->fromRoute(null, $data, array(), true);
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

<?php

/**
 * Table Builder
 *
 * Builds a table from config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table;

use Zend\ServiceManager\ServiceManager;

/**
 * Table Builder
 *
 * Builds a table from config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TableBuilder
{
    const TYPE_DEFAULT = 1;
    const TYPE_PAGINATE = 2;
    const TYPE_CRUD = 3;
    const TYPE_HYBRID = 4;
    const TYPE_FORM_TABLE = 5;
    const DEFAULT_LIMIT = 10;
    const DEFAULT_PAGE = 1;

    /**
     * Hold the pagination helper
     *
     * @var object
     */
    private $paginationHelper;

    /**
     * Hold the contentHelper
     *
     * @var object
     */
    private $contentHelper;

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
     * Footer settings
     *
     * @var array
     */
    private $footer = array();

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
     * Pre-defined widths
     *
     * @var array
     */
    private $widths = array('checkbox' => '16px');

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
     * Holds the actionFieldName
     *
     * @var string
     */
    private $actionFieldName = 'action';

    /**
     * Holds the fieldset name
     *
     * @var null
     */
    private $fieldset = null;

    /**
     * @var
     */
    private $sm;

    /**
     * Setter for actionFieldName
     *
     * @param string $name
     */
    public function setActionFieldName($name)
    {
        $this->actionFieldName = $name;
    }

    /**
     * Return the actionFieldName
     *
     * @return string
     */
    public function getActionFieldName()
    {
        if (!empty($this->fieldset)) {
            return $this->fieldset . '[' . $this->actionFieldName . ']';
        }

        return $this->actionFieldName;
    }

    /**
     * Setter for Fieldset
     *
     * @param string $name
     */
    public function setFieldset($name)
    {
        $this->fieldset = $name;
    }

    /**
     * Getter for fieldset
     *
     * @return string
     */
    public function getFieldset()
    {
        return $this->fieldset;
    }

    /**
     * Inject the service locator
     *
     * @param $sm
     */
    public function __construct($sm)
    {
        $this->setServiceLocator($sm);
        $this->applicationConfig = $sm->get('Config');
    }

    /**
     * Setter for type
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Set Service Manager
     *
     * @param ServiceManager $sm
     */
    public function setServiceLocator($sm)
    {
        $this->sm = $sm;
    }

    /**
     * Get Service Manager
     *
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }

    /**
     * Set settings
     *
     * @param array $settings
     */
    public function setSettings($settings = array())
    {
        $this->settings = $settings;
    }

    /**
     * Setter for total
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Setter for rows
     *
     * @param array $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
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
     * Setter for footer
     *
     * @param array $footer
     */
    public function setFooter($footer = array())
    {
        $this->footer = $footer;
    }

    /**
     * Setter for footer
     *
     * @param array $footer
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Get the content helper
     *
     * @return object
     * @throws \Exception
     */
    public function getContentHelper()
    {
        if (empty($this->contentHelper)) {
            if (!isset($this->applicationConfig['tables']['partials'])) {

                throw new \Exception('Table partial location not defined in config');
            }

            $this->contentHelper = new ContentHelper($this->applicationConfig['tables']['partials'], $this);
        }

        return $this->contentHelper;
    }

    /**
     * Get pagination helper
     *
     * @return PaginationHelper
     */
    public function getPaginationHelper()
    {
        if (empty($this->paginationHelper)) {
            $this->paginationHelper = new PaginationHelper($this->getPage(), $this->getTotal(), $this->getLimit());
        }

        return $this->paginationHelper;
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
     * Set variables
     *
     * @param array $variables
     */
    public function setVariables($variables = array())
    {
        $this->variables = $variables;
    }

    /**
     * Set a single variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Get variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Get a single variable
     *
     * @param string $name
     * @return mixed
     */
    public function getVariable($name)
    {
        return (isset($this->variables[$name]) ? $this->variables[$name] : '');
    }

    /**
     * Set the columns
     *
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
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
     * Setter for limit
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Getter for limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Setter for page
     *
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Getter for page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Getter for url
     *
     * @return object
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Setter for sort
     *
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Getter for sort
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Setter for order
     *
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Getter for order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Build a table from a config file
     *
     * @param array $config
     * @return string
     */
    public function buildTable($name, $data = array(), $params = array(), $render = true)
    {
        $this->loadConfig($name);

        $this->loadData($data);

        $this->loadParams($params);

        $this->setupAction();

        if ($render) {
            return $this->render();
        } else {
            return $this;
        }
    }

    /**
     * Load the configuration if it exists
     *
     * @param string $name
     * @throws \Exception
     */
    public function loadConfig($name)
    {
        if (!isset($this->applicationConfig['tables']['config'])
            || empty($this->applicationConfig['tables']['config'])) {
            throw new \Exception('Table config location not defined');
        }

        $config = $this->getConfigFromFile($name);

        $this->setSettings(isset($config['settings']) ? $config['settings'] : array());

        if (isset($this->settings['paginate']) && !isset($this->settings['paginate']['limit'])) {
            $this->settings['paginate']['limit'] = array(
                'default' => 10,
                'options' => array(10, 25, 50)
            );
        }

        if (isset($this->settings['crud']['action_field_name'])) {
            $this->setActionFieldName($this->settings['crud']['action_field_name']);
        }

        if (isset($this->settings['crud']['formName'])) {
            $config['variables']['hidden'] = isset($this->settings['crud']['formName'])
                ? $this->settings['crud']['formName']
                : 'default';
        }

        if (isset($config['variables']['title'])) {
            $config['variables']['title'] = $this->getServiceLocator()
                ->get('translator')
                ->translate($config['variables']['title']);
        }

        $this->attributes = isset($config['attributes']) ? $config['attributes'] : array();
        $this->setColumns(isset($config['columns']) ? $config['columns'] : array());
        $this->setVariables(isset($config['variables']) ? $config['variables'] : array());
        $this->setFooter(isset($config['footer']) ? $config['footer'] : array());

        return true;
    }

    /**
     * Load data, set the rows and the total count for pagination
     *
     * @param array $data
     */
    public function loadData($data = array())
    {
        $this->setRows(isset($data['Results']) ? $data['Results'] : $data);
        $this->setTotal(isset($data['Count']) ? $data['Count'] : count($this->rows));
    }

    /**
     * Load params
     *
     * @param array $array
     */
    public function loadParams($array = array())
    {
        if (isset($array['limit'])) {

            $this->setLimit($array['limit']);

        } elseif (isset($this->settings['paginate']['limit']['default'])) {

            $this->setLimit((int)$this->settings['paginate']['limit']['default']);
        }

        $this->setPage(isset($array['page']) ? $array['page'] : self::DEFAULT_PAGE);

        if (!isset($array['url'])) {

            throw new \Exception('Table helper requires the URL helper');
        }

        $this->url = $array['url'];
        $this->setSort(isset($array['sort']) ? $array['sort'] : '');
        $this->setOrder(isset($array['order']) ? $array['order'] : 'ASC');

        $this->setVariables(array_merge($this->getVariables(), $array));
    }

    /**
     * Setup the action
     */
    public function setupAction()
    {
        if (!isset($this->getVariables()['action'])) {
            $this->variables['action'] = $this->generateUrl();
        }
    }

    /**
     * Render the table
     *
     * @return type
     */
    public function render()
    {
        return $this->replaceContent($this->renderTable(), $this->getVariables());
    }

    /**
     * Get config from file
     *  Useful for unit testing
     *
     * @param string $file
     * @return array
     */
    public function getConfigFromFile($name)
    {
        $found = false;

        foreach ($this->applicationConfig['tables']['config'] as $location) {

            $configFile = $location . $name . '.table.php';

            if (file_exists($configFile)) {

                $found = true;
                break;
            }
        }

        if (!$found) {

            throw new \Exception('Table configuration not found');
        }

        return include($configFile);
    }

    /**
     * Render table footer
     *
     * return string
     */
    public function renderTableFooter()
    {
        if (empty($this->footer)) {
            return '';
        }

        $columns = array();

        foreach ($this->footer as $column) {

            $columns[] = $this->renderTableFooterColumn($column);
        }

        $content = $this->renderTableFooterColumns($columns);

        return $this->replaceContent('{{[elements/tableFooter]}}', array('content' => $content));
    }

    /**
     * Render a single table footer column
     *
     * @param array $column
     * @return array
     */
    private function renderTableFooterColumn($column)
    {
        $details = array('content' => '');

        if (isset($column['content'])) {
            $details['content'] = $column['content'];
        }

        $details['type'] = (isset($column['type']) && $column['type'] == 'th' ? 'th' : 'td');

        $details['colspan'] = (isset($column['colspan']) ? $column['colspan'] : '');

        if (isset($column['formatter'])) {

            $column['format'] = $this->callFormatter($column, $this->getRows());
        }

        if (isset($column['format'])) {

            $details['content'] = $this->replaceContent($column['format'], $this->getVariables());
        }

        return $details;
    }

    /**
     * Render table footer columns
     *
     * @param array $columns
     * @return string
     */
    private function renderTableFooterColumns($columns)
    {
        $content = '';

        foreach ($columns as $details) {
            $content .= $this->replaceContent('{{[elements/footerColumn]}}', $details);
        }

        return $content;
    }

    /**
     * Decide the view and begin the render
     *
     * @return string
     */
    public function renderTable()
    {
        $this->setType($this->whichType());

        if ((!isset($this->variables['within_form']) || $this->variables['within_form'] == false)
            && isset($this->settings['crud'])) {

            return $this->renderLayout('crud');
        }

        return $this->renderLayout('default');
    }

    /**
     * Determine which table type we have
     *
     * @return int
     */
    private function whichType()
    {
        if (isset($this->variables['within_form']) && $this->variables['within_form'] == true) {

            return self::TYPE_FORM_TABLE;
        }

        if (isset($this->settings['crud']) && isset($this->settings['paginate'])) {

            return self::TYPE_HYBRID;
        }

        if (isset($this->settings['crud'])) {

            return self::TYPE_CRUD;
        }

        if (isset($this->settings['paginate'])) {

            return self::TYPE_PAGINATE;
        }

        return self::TYPE_DEFAULT;
    }

    /**
     * Wrapper for Content Helper renderLayout
     *
     * @param string $name
     * @return string
     */
    public function renderLayout($name)
    {
        return $this->getContentHelper()->renderLayout($name);
    }

    /**
     * Render the total if we have a paginated table
     *
     * @return string
     */
    public function renderTotal()
    {
        if ($this->type !== self::TYPE_PAGINATE && $this->type !== self::TYPE_HYBRID) {

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
        if ($this->type !== self::TYPE_CRUD
            && $this->type !== self::TYPE_HYBRID
            && $this->type !== self::TYPE_FORM_TABLE) {
            return '';
        }

        $actions = $this->trimActions(
            isset($this->settings['crud']['actions']) ? $this->settings['crud']['actions'] : array()
        );

        if (empty($actions)) {
            return '';
        }

        $newActions = $this->formatActions($actions);

        $content = $this->formatActionContent($newActions);

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

        return $this->replaceContent(
            '{{[elements/actionSelect]}}',
            array('option' => $options, 'action_field_name' => $this->getActionFieldName())
        );
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
        if ($this->type !== self::TYPE_PAGINATE && $this->type !== self::TYPE_HYBRID) {
            return '';
        }

        if (!in_array($this->getLimit(), $this->settings['paginate']['limit']['options'])) {
            $this->settings['paginate']['limit']['options'][] = $this->getLimit();
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

            if ($option == $this->getLimit()) {
                $class = 'current';
            } else {
                $details = array(
                    'option' => $option,
                    'link' => $this->generatePaginationUrl(array('page' => 1, 'limit' => $option))
                );
                $option = $this->replaceContent('{{[elements/limitLink]}}', $details);
            }

            $limitDetails = array('class' => $class, 'option' => $option);

            $content .= $this->replaceContent('{{[elements/limitOption]}}', $limitDetails);
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
        $options = $this->getPaginationHelper()->getOptions();

        $content = '';

        foreach ($options as $details) {

            if (is_null($details['page']) || (string)$this->getPage() == $details['page']) {
                $details['option'] = $details['label'];
            } else {
                $details['link'] = $this->generatePaginationUrl(array('page' => $details['page']));
                $details['option'] = $this->replaceContent('{{[elements/paginationLink]}}', $details);
            }

            $details = array_merge(array('class' => ''), $details);

            $content .= $this->replaceContent('{{[elements/paginationItem]}}', $details);
        }

        return $content;
    }

    /**
     * Render a header column
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

            if ($column['sort'] === $this->getSort()) {

                if ($this->getOrder() === 'ASC') {

                    $column['order'] = 'DESC';

                    $column['class'] .= ' ascending';
                } else {

                    $column['class'] .= ' descending';
                }
            }

            $column['link'] = $this->generatePaginationUrl(
                array(
                    'sort' => $column['sort'],
                    'order' => $column['order']
                )
            );

            $column['title'] = $this->replaceContent('{{[elements/sortColumn]}}', $column);
        }

        if (isset($column['width']) && isset($this->widths[$column['width']])) {

            $column['width'] = $this->widths[$column['width']];
        }

        if (isset($column['title'])) {
            $translator = $this->getServiceLocator()->get('translator');
            $column['title'] = $translator->translate($column['title']);
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
        if (isset($column['type']) && class_exists(__NAMESPACE__ . '\\Type\\' . $column['type'])) {

            $typeClass = __NAMESPACE__ . '\\Type\\' . $column['type'];
            $type = new $typeClass($this);
            $content = $type->render($row, $column);
        }

        if (isset($column['formatter'])) {

            $return = $this->callFormatter($column, $row);

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
     * Render extra rows
     */
    public function renderExtraRows()
    {
        $content = '';

        if (count($this->getRows()) === 0) {

            $columns = $this->getColumns();

            $message = isset($this->variables['empty_message'])
                ? $this->replaceContent($this->variables['empty_message'], $this->getVariables())
                : 'The table is empty';

            $vars = array(
                'colspan' => count($columns),
                'message' => $this->getServiceLocator()->get('translator')->translate($message)
            );

            $content .= $this->replaceContent('{{[elements/emptyRow]}}', $vars);
        }

        return $content;
    }

    /**
     * Process the formatter
     *
     * @param array $column
     * @param array $data
     *
     * @return mixed
     */
    private function callFormatter($column, $data)
    {
        if (is_string($column['formatter'])
                && class_exists(__NAMESPACE__ . '\\Formatter\\' . $column['formatter'])) {

            $className =  '\\' . __NAMESPACE__ . '\\Formatter\\' . $column['formatter'] . '::format';

            $column['formatter'] = $className;
        }

        if (is_callable($column['formatter'])) {

            return call_user_func($column['formatter'], $data, $column, $this->getServiceLocator());
        }

        return '';
    }

    /**
     * Render an attribute string
     *
     * @param array $attrs
     * @return string
     */
    public function renderAttributes($attrs = array())
    {
        return $this->getContentHelper()->renderAttributes($attrs);
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
        return $this->getContentHelper()->replaceContent($content, $vars);
    }

    /**
     * Generate url
     *
     * @param array $data
     * @return string
     */
    private function generateUrl($data = array(), $route = null, $extendParams = true)
    {
        return $this->getUrl()->fromRoute($route, $data, array(), $extendParams);
    }

    /**
     * Generate pagination url. Strips the controller and action params from
     * the URL
     *
     * @param array $data
     * @param string $route
     * @param array $extendParams
     * @return string
     */
    private function generatePaginationUrl($data = array(), $route = null, $extendParams = true)
    {
        $returnUrl = $this->generateUrl($data, $route, $extendParams);

        // strip out controller and action params
        $returnUrl = preg_replace('/\/controller\/[a-zA-Z0-9\-_]+\/action\/[a-zA-Z0-9\-_]+/', '', $returnUrl);

        return $returnUrl;
    }

    /**
     * Format action content
     *
     * @param array $actions
     * @return string
     */
    private function formatActionContent($actions)
    {
        if (count($actions) > 3) {
            return $this->renderDropdownActions($actions);
        }

        return $this->renderButtonActions($actions);
    }

    /**
     * Format actions
     *
     * @param array $actions
     * @return array
     */
    private function formatActions($actions)
    {
        $newActions = array();

        foreach ($actions as $name => $details) {

            $value = isset($details['value']) ? $details['value'] : ucwords($name);

            $class = isset($details['class']) ? $details['class'] : 'secondary';

            $actionFieldName = $this->getActionFieldName();

            $newActions[] = array(
                'name' => $name,
                'label' => $value,
                'class' => $class,
                'action_field_name' => $actionFieldName
            );
        }

        return $newActions;
    }

    /**
     * Trim actions
     *
     * @param array $actions
     * @return array
     */
    private function trimActions($actions)
    {
        if (count($this->rows) === 0) {
            foreach ($actions as $key => $details) {
                if (isset($details['requireRows']) && $details['requireRows']) {
                    unset($actions[$key]);
                }
            }
        }

        return $actions;
    }
    
    /**
     * Remove column on the fly
     *
     * @param string $name
     */
    public function removeColumn($name = '')
    {
        if ($name) {
            $columns = $this->getColumns();
            $newColumns = array();
            foreach($columns as $column) {
                if (array_key_exists('name', $column) && $column['name'] !== $name) {
                    $newColumns[] = $column;
                } elseif (array_key_exists('name', $column) === false) {
                    $newColumns[] = $column;
                }
            }
            $this->setColumns($newColumns);
        }
    }
}

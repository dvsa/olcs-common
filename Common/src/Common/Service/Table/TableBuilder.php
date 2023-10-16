<?php

namespace Common\Service\Table;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Exception\MissingFormatterException;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

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
    public const TYPE_DEFAULT = 1;
    public const TYPE_PAGINATE = 2;
    public const TYPE_CRUD = 3;
    public const TYPE_HYBRID = 4;
    public const TYPE_FORM_TABLE = 5;
    public const DEFAULT_LIMIT = 10;
    public const DEFAULT_PAGE = 1;

    public const MAX_FORM_ACTIONS = 6;

    public const ACTION_FORMAT_BUTTONS = 'buttons';
    public const ACTION_FORMAT_DROPDOWN = 'dropdown';

    public const CONTENT_TYPE_HTML = 'html';
    public const CONTENT_TYPE_CSV = 'csv';

    public const ARIA_SORT_ASC = 'sort-in-ascending-order';
    public const ARIA_SORT_DESC = 'sort-in-descending-order';
    public const CLASS_TABLE = 'govuk-table';
    public const CLASS_TABLE_CELL = 'govuk-table__cell';
    public const CLASS_TABLE_HEADER = 'govuk-table__header';
    public const CLASS_TABLE_HEADER_NUMERIC = 'govuk-table__header--numeric';
    public const CLASS_TABLE_CELL_NUMERIC = 'govuk-table__cell--numeric';

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
     * Hold the contentType
     *
     * @var string
     */
    private $contentType = self::CONTENT_TYPE_HTML;

    /**
     * Inject the application config from Laminas
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
    private $widths = array('checkbox' => '20px');

    /**
     * Total count of results
     *
     * @var int
     */
    private $total;

    /**
     * Total of the unfiltered results
     *
     * @var int
     */
    private $unfilteredTotal;

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
     * @var \Laminas\Mvc\Controller\Plugin\Url
     */
    private $url;

    /**
     * Query object
     *
     * @var object
     */
    private $query = [];

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
     * Is this builder inside a disabled table element?
     *
     * @var bool
     */
    private $isDisabled = false;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Authorisation service to allow columns/rows to be hidden depending on permission model
     * @var AuthorizationService
     */
    private $authService;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var UrlHelperService
     */
    private $urlHelper;

    /** @var  \Laminas\Form\Element\Csrf */
    private $elmCsrf;

    /**
     * @var array<string,string>
     */
    private $urlParameterNameMap = [];

    private FormatterPluginManager $formatterPluginManager;

    /**
     * @return array<string,string>
     */
    public function getUrlParameterNameMap(): array
    {
        return $this->urlParameterNameMap;
    }

    /**
     * @param array<string,string> $urlParamNameMap
     * @return $this
     */
    public function setUrlParameterNameMap(array $urlParamNameMap): self
    {
        assert(array_reduce($urlParamNameMap, function ($carry, $mappedValue) {
            return $carry && is_string($mappedValue);
        }, true), 'Expected all mapped values to be strings');
        $this->urlParameterNameMap = $urlParamNameMap;
        return $this;
    }

    /**
     * @param string $urlParam
     * @return string
     */
    protected function mapUrlParameterName(string $urlParam): string
    {
        return $this->getUrlParameterNameMap()[$urlParam] ?? $urlParam;
    }

    /**
     * Create service instance
     *
     * @param ServiceLocatorInterface $sm
     * @param AuthorizationService $authService
     * @param Translator $translator
     * @param UrlHelperService $urlHelper
     *
     * @return TableBuilder
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        AuthorizationService $authService,
        Translator $translator,
        UrlHelperService $urlHelper,
        array $applicationConfig,
        FormatterPluginManager $formatterPluginManager
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->authService = $authService;
        $this->translator = $translator;
        $this->urlHelper = $urlHelper;
        $this->applicationConfig = $applicationConfig;
        $this->formatterPluginManager = $formatterPluginManager;
    }

    /**
     * Set whether this table appears inside a disabled element
     *
     * @param bool $disabled
     */
    public function setDisabled($disabled)
    {
        $this->isDisabled = $disabled;
    }

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
     * Setter for type
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
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
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
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
     * Setter for unfilteredTotal
     *
     * @param int $unfilteredTotal
     */
    public function setUnfilteredTotal($unfilteredTotal)
    {
        $this->unfilteredTotal = $unfilteredTotal;
    }

    /**
     * Setter for rows
     *
     * @param array $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
        return $this;
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
     * Check if a table has an action
     *
     * @param string $name
     * @return boolean
     */
    public function hasAction($name)
    {
        return isset($this->settings['crud']['actions'][$name]);
    }

    /**
     * Remove an action
     *
     * @param string $name
     */
    public function removeAction($name)
    {
        if ($this->hasAction($name)) {
            unset($this->settings['crud']['actions'][$name]);
        }
    }

    public function removeActions()
    {
        foreach ($this->settings['crud']['actions'] as $key => $config) {
            $this->removeAction($key);
        }
        // remove any actions that are in the table
        $this->removeColumn('actionLinks');
    }

    public function addAction($key, $settings = [])
    {
        $this->settings['crud']['actions'][$key] = $settings;
    }

    /**
     * Get action
     *
     * @param string $key key
     *
     * @return string
     */
    public function getAction($key)
    {
        return $this->settings['crud']['actions'][$key];
    }

    /**
     * Disable an action
     *
     * @param string $name
     */
    public function disableAction($name)
    {
        if ($this->hasAction($name)) {
            $this->settings['crud']['actions'][$name]['disabled'] = 'disabled';
        }
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
            if (!isset($this->applicationConfig['tables']['partials'][$this->contentType])) {
                throw new \Exception('Table partial location not defined in config');
            }

            $this->contentHelper = new ContentHelper(
                $this->applicationConfig['tables']['partials'][$this->contentType],
                $this
            );
        }

        return $this->contentHelper;
    }

    public function setContentType($type)
    {
        $this->contentType = $type;
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
            $this->paginationHelper->setTranslator($this->translator);
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
        $this->columns = array();

        foreach ($columns as $key => $column) {
            if (!is_string($key)) {
                if (isset($column['name'])) {
                    $key = $column['name'];
                } else {
                    $key = null;
                }
            }

            if ($key == null) {
                $this->columns[] = $column;
            } else {
                $this->columns[$key] = $column;
            }
        }
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
     * Whether the table has any rows
     *
     * @return bool
     */
    public function hasRows()
    {
        return count($this->rows) > 0;
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
     * @return \Laminas\Mvc\Controller\Plugin\Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Getter for query
     *
     * @return object
     */
    public function getQuery()
    {
        return $this->query;
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


    public function prepareTable($config, array $data = array(), array $params = array())
    {
        $this->loadConfig($config);

        $this->loadData($data);

        $this->loadParams($params);

        $this->setupAction();

        $this->setupDataAttributes();

        return $this;
    }

    /**
     * Build a table from a config file
     *
     * @param string|array $config
     * @param array $data
     * @param array $params
     * @param boolean $render
     * @return string
     */
    public function buildTable($config, $data = array(), $params = array(), $render = true)
    {
        $this->prepareTable($config, $data, $params);

        if ($render) {
            return $this->render();
        } else {
            return $this;
        }
    }

    /**
     * Load the configuration if it exists
     *
     * @param $config
     * @return bool
     */
    public function loadConfig($config)
    {
        if (!is_array($config)) {
            $config = $this->getConfigFromFile($config);
        }

        $config = array_merge(
            array(
                'settings' => array(),
                'attributes' => array(),
                'columns' => array(),
                'footer' => array()
            ),
            $config
        );

        $this->setSettings($config['settings']);

        $this->setPaginationDefaults();

        $this->maybeSetActionFieldName();

        $config['variables']['hidden'] = isset($this->settings['crud']['formName'])
            ? $this->settings['crud']['formName']
            : 'default';

        $this->translateTitle($config);

        $this->attributes = $config['attributes'];

        if (isset($this->attributes['class'])) {
            $this->attributes['class'] .= ' ' . self::CLASS_TABLE;
        } else {
            $this->attributes['class'] = self::CLASS_TABLE;
        }

        $this->setColumns($config['columns']);
        $this->setVariables($config['variables']);
        $this->setFooter($config['footer']);

        $this->checkForActionLinks();

        return true;
    }

    /**
     * Set Pagination Defaults
     */
    private function setPaginationDefaults()
    {
        if ($this->shouldPaginate() && !isset($this->settings['paginate']['limit'])) {
            $this->settings['paginate']['limit'] = array(
                'default' => 10,
                'options' => array(10, 25, 50)
            );
        }
    }

    /**
     * Translate title
     *
     * @param array $config Config
     *
     * @return void
     */
    private function translateTitle(&$config)
    {
        if (isset($config['variables']['title'])) {
            $config['variables']['title'] = $this->translator->translate($config['variables']['title']);
        }
    }

    /**
     * Maybe set the action field name
     */
    private function maybeSetActionFieldName()
    {
        if (isset($this->settings['crud']['action_field_name'])) {
            $this->setActionFieldName($this->settings['crud']['action_field_name']);
        }
    }
    /**
     * Load data, set the rows and the total count for pagination
     *
     * @param array $data
     */
    public function loadData($data = array())
    {
        if (isset($data['Results'])) {
            $data['results'] = $data['Results'];
            unset($data['Results']);
        }

        if (isset($data['Count'])) {
            $data['count'] = $data['Count'];
            unset($data['Count']);
        }

        $this->setRows(isset($data['results']) ? $data['results'] : $data);
        $this->setTotal(isset($data['count']) ? $data['count'] : count($this->rows));
        $this->setUnfilteredTotal(isset($data['count-unfiltered']) ? $data['count-unfiltered'] : $this->getTotal());

        // if there's only one row and we have a singular title, use it
        if ($this->getTotal() == 1) {
            if ($this->getVariable('titleSingular')) {
                $this->setVariable('title', $this->translator->translate($this->getVariable('titleSingular')));
            }
        }
    }

    /**
     * Load params
     *
     * @param array $array
     */
    public function loadParams($array = array())
    {
        if (!isset($array['url'])) {
            $array['url'] = $this->urlHelper;
        }

        $defaults = array(
            'limit' => isset($this->settings['paginate']['limit']['default'])
                ? $this->settings['paginate']['limit']['default']
                : 10,
            'page' => self::DEFAULT_PAGE,
            'sort' => '',
            'order' => 'ASC'
        );

        $array = array_merge(
            $defaults,
            $array
        );

        $this->setLimit($array['limit']);
        $this->setPage($array['page']);

        $this->url = $array['url'];
        $this->setSort($array['sort']);
        $this->setOrder($array['order']);

        if (isset($array['query'])) {
            $this->query = $array['query'];
        }

        $this->setVariables(array_merge($this->getVariables(), $array));
    }

    /**
     * Setup the action
     */
    public function setupAction()
    {
        $variables = $this->getVariables();
        if (!isset($variables['action'])) {
            if (isset($variables['action_route'])) {
                $route = $variables['action_route']['route'];
                $params = $variables['action_route']['params'];
                $this->variables['action'] = $this->generateUrl(
                    $params,
                    $route,
                    [],
                    true
                );
            } else {
                $this->variables['action'] = $this->generateUrl();
            }
        }
    }

    /**
     * To string method which calls render
     *
     * @NOTE added this for backwards compat, so we can start passing a table object around without affecting the
     * outcome
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $ex) {
            $content = $ex->getMessage();
            $content .= $ex->getTraceAsString();

            return $content;
        }
    }

    /**
     * Render the table
     *
     * @return string
     */
    public function render()
    {
        return $this->replaceContent($this->renderTable(), $this->getVariables());
    }

    /**
     * Get config from file
     *  Useful for unit testing
     *
     * @param string $name
     * @return array
     */
    public function getConfigFromFile($name)
    {
        if (
            !isset($this->applicationConfig['tables']['config'])
            || empty($this->applicationConfig['tables']['config'])
        ) {
            throw new \Exception('Table config location not defined');
        }

        $found = false;

        // @NOTE Reverse the array so the internal/selfserve config locations are checked before common
        $locations = array_reverse($this->applicationConfig['tables']['config']);

        foreach ($locations as $location) {
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
        $column = array_merge(
            array(
                'type' => 'td',
                'colspan' => '',
                'align' => '',
            ),
            $column
        );

        $details = array('content' => '');

        if (isset($column['content'])) {
            $details['content'] = $column['content'];
        }

        $details['type'] = $column['type'];

        $details['colspan'] = $column['colspan'];

        $details['class'] = self::CLASS_TABLE_CELL;

        if ($column['align']) {
            $details['class'] .= ' ' . $column['align'];
        }

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

        $this->elmCsrf = new \Laminas\Form\Element\Csrf(
            'security',
            [
                'csrf_options' => [
                    'timeout' => $this->applicationConfig['csrf']['timeout'],
                ],
            ]
        );

        if (isset($this->settings['submission_section'])) {
            return $this->renderLayout('submission-section');
        }

        if (
            (!isset($this->variables['within_form']) || $this->variables['within_form'] == false)
            && isset($this->settings['crud'])
        ) {
            return $this->renderLayout('crud');
        }

        if (isset($this->settings['layout'])) {
            return $this->renderLayout($this->settings['layout']);
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

        if (isset($this->settings['crud']) && $this->shouldPaginate()) {
            return self::TYPE_HYBRID;
        }

        if (isset($this->settings['crud'])) {
            return self::TYPE_CRUD;
        }

        if ($this->shouldPaginate()) {
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
        if ($name === 'default' && (empty($this->unfilteredTotal) && empty($this->rows))) {
            return $this->renderLayout('default_empty');
        }

        return $this->getContentHelper()->renderLayout($name);
    }

    /**
     * Render the total if we have a paginated table
     *
     * @return string
     */
    public function renderTotal()
    {
        if (
            $this->getSetting('overrideTotal', false)
            || !$this->shouldPaginate()
            && !$this->getSetting('showTotal', false)
        ) {
            return '';
        }

        $total = $this->total;

        return $this->replaceContent(' {{[elements/total]}}', array('total' => $total));
    }

    public function renderCaption()
    {
        return trim($this->renderTotal() . ' ' . $this->getVariable('title'));
    }

    /**
     * Render actions
     *
     * @return string
     */
    public function renderActions()
    {
        $hasActions = in_array(
            $this->type,
            array(
                self::TYPE_CRUD,
                self::TYPE_HYBRID,
                self::TYPE_FORM_TABLE
            )
        );

        if ($this->isDisabled || !$hasActions) {
            return '';
        }

        $crud = $this->getSetting('crud');

        $actions = $this->trimActions(isset($crud['actions']) ? $crud['actions'] : []);
        $links = $this->trimLinks(isset($crud['links']) ? $crud['links'] : []);

        if (empty($actions) && empty($links)) {
            return '';
        }

        $content = $this->formatActionContent(
            $this->formatActions($actions),
            $this->getSetting('actionFormat'),
            $this->getSetting('collapseAt'),
            $this->formatLinks($links)
        );

        return $this->replaceContent('{{[elements/actionContainer]}}', ['content' => $content]);
    }

    /**
     * Render the dropdown version of the actions
     *
     * @param array $actions
     * @return string
     */
    public function renderDropdownActions($actions = array(), $links = [])
    {
        $options = '';

        foreach ($actions as $details) {
            $options .= $this->replaceContent('{{[elements/actionOption]}}', $details);
        }

        $content = '';

        if (!empty($links)) {
            $content .= $this->renderLinks($links);
        }

        $content .= $this->replaceContent(
            '{{[elements/actionSelect]}}',
            array('option' => $options, 'action_field_name' => $this->getActionFieldName())
        );

        return $content;
    }

    /**
     * Render the button version of the actions
     *
     * @param array $actions
     * @param int $collapseAt number of buttons to show before they are 'collapsed' into
     * a 'more actions' dropdown
     * @return string
     */
    public function renderButtonActions($actions = array(), $collapseAt = 0, $links = [])
    {
        $content = '';

        if (!empty($links)) {
            $content .= $this->renderLinks($links);
        }

        if ($collapseAt) {
            $i = 0;
            $max = count($actions);
            while ($i < $max && $i < $collapseAt) {
                $content .= $this->replaceContent('{{[elements/actionButton]}}', array_shift($actions));
                $i++;
            }
            $content .= $this->renderMoreActions($actions);
            return $content;
        }

        foreach ($actions as $details) {
            $content .= $this->replaceContent('{{[elements/actionButton]}}', $details);
        }

        return $content;
    }

    public function renderLinks(array $links = [])
    {
        $content = '';

        foreach ($links as $details) {
            $content .= $this->replaceContent('{{[elements/link]}}', $details);
        }

        return $content;
    }

    private function renderMoreActions($actions)
    {
        $content = '';
        if (!empty($actions)) {
            $moreActions = [];
            foreach ($actions as $details) {
                //  add css class to items
                $cssClasses = (isset($details['class']) ? $details['class'] : '');
                if (0 == preg_match('/(\s|^)more-actions__item($|\s)/', $cssClasses)) {
                    $details['class'] = $cssClasses . ' more-actions__item';
                }

                $moreActions[] = $this->replaceContent('{{[elements/actionButton]}}', $details);
            }

            $content .= $this->replaceContent(
                '{{[elements/moreActions]}}',
                [
                    'content' => implode('', $moreActions),
                    'label' => $this->translator->translate('table_button_more_actions'),
                ]
            );
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
        if (!$this->shouldPaginate()) {
            return '';
        }

        /**
        Temporarily removed this, as if someone has set the limit to be more than the total, they would no longer see
         the limit options to reduce
        if (!in_array($this->getLimit(), $this->settings['paginate']['limit']['options'])) {
            $this->settings['paginate']['limit']['options'][] = $this->getLimit();
            sort($this->settings['paginate']['limit']['options']);
        }


        if ($this->total <= min($this->settings['paginate']['limit']['options'])) {
            return '';
        }
        */

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
                $class = PaginationHelper::CLASS_PAGINATION_ITEM_CURRENT;
            }
                $details = array(
                    'option' => $option,
                    'link' => $this->generatePaginationUrl([
                        $this->mapUrlParameterName('page') => 1,
                        $this->mapUrlParameterName('limit') => $option
                    ]),
                );
                $option = $this->replaceContent('{{[elements/limitLink]}}', $details);

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

        $previousContent = '';
        $content = '';
        $nextContent = '';

        if (!empty($options['previous'])) {
            $options['previous']['link'] = $this->getPageLink($options['previous']['page']);
            $previousContent = $this->replaceContent('{{[elements/paginationPrevious]}}', $options['previous']);
        }

        foreach ($options['links'] as $details) {
            if (is_null($details['page'])) {
                $content .= $this->replaceContent('{{[elements/paginationEllipses]}}', $details);
                continue;
            }

            $details['link'] = $this->getPageLink($details['page']);
            $details['option'] = $this->replaceContent('{{[elements/paginationLink]}}', $details);

            $details = array_merge(array('class' => ''), $details);

            $content .= $this->replaceContent('{{[elements/paginationItem]}}', $details);
        }

        if (!empty($content)) {
            $content = $this->replaceContent('{{[elements/paginationList]}}', ['items' => $content]);
        }

        if (!empty($options['next'])) {
            $options['next']['link'] = $this->getPageLink($options['next']['page']);
            $nextContent = $this->replaceContent('{{[elements/paginationNext]}}', $options['next']);
        }

        return $previousContent . $content . $nextContent;
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
        if ($this->shouldHide($column) || $this->getVariable('hide_column_headers')) {
            return;
        }

        if (!isset($column['scope'])) {
            $column['scope'] = 'col';
        }

        $column['class'] = self::CLASS_TABLE_HEADER;

        if (isset($column['isNumeric']) && $column['isNumeric']) {
            $column['class'] .= ' ' . self::CLASS_TABLE_HEADER_NUMERIC;
        }

        if (isset($column['align'])) {
            $column['class'] .= ' ' . $column['align'];
            unset($column['align']);
        }

        if (isset($column['title'])) {
            $column['title'] = $this->translator->translate($column['title']);
        }

        if (isset($column['sort'])) {
            $column['class'] .= ' sortable';
            $column['order'] = 'ASC';
            $sortAria = self::ARIA_SORT_ASC;

            if ($column['sort'] === $this->getSort()) {
                if ($this->getOrder() === 'ASC') {
                    $column['order'] = 'DESC';
                    $sortAria = self::ARIA_SORT_DESC;

                    $column['class'] .= ' ascending';
                } else {
                    $column['class'] .= ' descending';
                }
            }

            $column['aria'] = $this->translator->translate($sortAria);

            $column['link'] = $this->generatePaginationUrl(
                array(
                    $this->mapUrlParameterName('sort') => $column['sort'],
                    $this->mapUrlParameterName('order') => $column['order']
                )
            );

            $column['title'] = $this->replaceContent('{{[elements/sortColumn]}}', $column);
        }

        if (isset($column['width']) && isset($this->widths[$column['width']])) {
            $column['width'] = $this->widths[$column['width']];
        }

        if (isset($column['type']) && $column['type'] == 'Checkbox' && ($column['selectAll'] ?? true)) {
            $column['title'] = $this->replaceContent('{{[elements/checkall]}}');
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
    public function renderBodyColumn($row, $column, $wrapper = '{{[elements/td]}}', $customAttributes = [])
    {
        if ($this->shouldHide($column)) {
            return;
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

        if (
            $this->contentType === self::CONTENT_TYPE_HTML
            && isset($column['type'])
            && class_exists(__NAMESPACE__ . '\\Type\\' . $column['type'])
        ) {
            /** @var \Common\Service\Table\Type\AbstractType $typeClass */
            $typeClass = __NAMESPACE__ . '\\Type\\' . $column['type'];
            $type = new $typeClass($this);

            // allow for the fact a formatter may have already set some content
            // which the type should respect
            $formattedContent = isset($row['content']) ? $row['content'] : null;
            $content = $type->render($row, $column, $formattedContent);
        }

        if (isset($column['format'])) {
            $content = $this->replaceContent($column['format'], $row);
        }

        if (!isset($content) || (empty($content) && !in_array($content, [0, 0.0, '0']))) {
            $content =  isset($column['name']) && isset($row[$column['name']]) ?
                $row[$column['name']] : '';
        }

        $replacements = [
            'content' => $content,
            'attrs' => $this->processBodyColumnAttributes($column, $customAttributes)
        ];

        return $this->replaceContent($wrapper, $replacements);
    }

    private function processBodyColumnAttributes($column, $customAttributes): string
    {
        $plainAttributes = '';

        $columnAttributes = [
            'class' => self::CLASS_TABLE_CELL
        ];

        if (isset($column['isNumeric']) && $column['isNumeric']) {
            $columnAttributes['class'] .= ' ' . self::CLASS_TABLE_CELL_NUMERIC;
        }

        if (isset($column['align'])) {
            $columnAttributes['class'] .= ' ' . $column['align'];
        }

        if ($this->hasAnyTitle()) {
            $dataHeading = isset($column['title'])
                ? $this->translator->translate($column['title'])
                : '';
            $columnAttributes['data-heading'] = strip_tags($dataHeading);
        }

        foreach ($customAttributes as $attribute => $value) {
            if (isset($columnAttributes[$attribute]) && !empty($columnAttributes[$attribute])) {
                $columnAttributes[$attribute] .= ' ' . $value;
            } else {
                $columnAttributes[$attribute] = $value;
            }
        }

        foreach ($columnAttributes as $attribute => $value) {
            if (empty(trim($value))) {
                continue;
            }
            $plainAttributes .= ' ' . $attribute . '="' . $value . '"';
        }

        return $plainAttributes;
    }

    /**
     * Does any of table columns has the title
     *
     * @return bool
     */
    public function hasAnyTitle()
    {
        $columns = $this->getColumns();
        foreach ($columns as $column) {
            if (isset($column['title'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render extra rows
     */
    public function renderExtraRows()
    {
        $content = '';

        if (count($this->getRows()) === 0) {
            $columns = $this->getColumns();

            if ($this->unfilteredTotal > 0) {
                $message = 'There are no results matching your search';
            } else {
                $message = $this->getEmptyMessage();
            }

            $vars = array(
                'colspan' => count($columns),
                'message' => $message
            );

            $content .= $this->replaceContent('{{[elements/emptyRow]}}', $vars);
        }

        return $content;
    }

    public function setEmptyMessage($message)
    {
        $this->variables['empty_message'] = $message;
    }

    public function getEmptyMessage()
    {
        $message = isset($this->variables['empty_message'])
            ? $this->replaceContent($this->variables['empty_message'], $this->getVariables())
            : 'The table is empty';

        return $this->translator->translate($message);
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
        if (is_string($column['formatter'])) {
            // Remove the leading namespace separator if exists
            $formatterClass = ltrim($column['formatter'], '\\');

            // Check if the formatter class contains a namespace
            if (strpos($formatterClass, '\\') === false) {
                @trigger_error(sprintf('Table formatter "%s" should be using the FQCN.', $column['formatter']), \E_USER_DEPRECATED);

                // Append the namespace if it's missing
                $formatterClass = '\\' . __NAMESPACE__ . '\\Formatter\\' . $formatterClass;
            }

            if (!class_exists($formatterClass) || !$this->formatterPluginManager->has($formatterClass)) {
                throw new MissingFormatterException('Missing table formatter: ' . $column['formatter']);
            }

            $column['formatter'] = $this->formatterPluginManager->get($formatterClass);
        }

        if (is_object($column['formatter'])) {
            if (method_exists($column['formatter'], 'format')) {
                return $column['formatter']->format($data, $column);
            } elseif ($column['formatter'] instanceof \Closure) {
                return $column['formatter']($data, $column);
            }
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
    public function replaceContent($content, $vars = array())
    {
        return $this->getContentHelper()->replaceContent($content, $vars);
    }

    /**
     * Generate url
     *
     * @param array $data
     * @return string
     */
    private function generateUrl($data = array(), $route = null, $options = [], $reuseMatchedParams = true)
    {
        if (is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = [];
        }

        return $this->getUrl()->fromRoute($route, $data, $options, $reuseMatchedParams);
    }

    private function getPageLink($page)
    {
        return $this->generatePaginationUrl(
            [
                $this->mapUrlParameterName('page') => $page,
                $this->mapUrlParameterName('limit') => $this->getLimit(),
            ]
        );
    }

    /**
     * Generate pagination url. Strips the controller and action params from
     * the URL
     *
     * @param array $data
     * @param string $route
     * @return string
     */
    private function generatePaginationUrl($data = array(), $route = null)
    {

        /** @var \Laminas\Mvc\Controller\Plugin\Url $url */
        $url = $this->getUrl();

        /**
         * This is the query information to add to the existing route/url.
         */
        $query = $this->getQuery();
        if ($query && !is_array($query)) {
            $query = $query->toArray();
        }

        $params = array_merge($query, $data);

        $params = array_diff_key($params, array_flip(['controller', 'action']));

        $options = [];
        $options['query'] = $params;

        $returnUrl = $url->fromRoute($route, [], $options, true);

        // strip out controller and action params - not sure if this is still needed.
        $returnUrl = preg_replace('/\/controller\/[a-zA-Z0-9\-_]+\/action\/[a-zA-Z0-9\-_]+/', '', $returnUrl);

        return $returnUrl;
    }

    /**
     * Format action content
     *
     * @param array $actions
     * @param string $overrideFormat
     * @return string
     */
    private function formatActionContent($actions, $overrideFormat, $collapseAt = 0, $newLinks = [])
    {
        switch ($overrideFormat) {
            case self::ACTION_FORMAT_DROPDOWN:
                return $this->renderDropdownActions($actions, $newLinks);
            case self::ACTION_FORMAT_BUTTONS:
                return $this->renderButtonActions($actions, $collapseAt, $newLinks);
        }

        if (count($actions) > self::MAX_FORM_ACTIONS) {
            return $this->renderDropdownActions($actions, $newLinks);
        }

        return $this->renderButtonActions($actions, $collapseAt, $newLinks);
    }

    /**
     * Format actions
     *
     * @param array $actions Actions
     *
     * @return array
     */
    private function formatActions($actions)
    {
        $newActions = array();

        foreach ($actions as $name => $details) {
            $value = isset($details['value']) ? $details['value'] : ucwords($name);

            $label = isset($details['label']) ? $this->translator->translate($details['label']) : $value;

            $class = isset($details['class']) ? $details['class'] : 'govuk-button govuk-button--secondary';

            $id = isset($details['id']) ? $details['id'] : $name;

            $disabled = isset($details['disabled']) ? $details['disabled'] : '';
            if ($disabled) {
                $class .= ' js-force-disable';
            }

            $actionFieldName = $this->getActionFieldName();

            $newActions[] = array(
                'name' => $name,
                'id' => $id,
                'value' => $value,
                'label' => $label,
                'class' => $class,
                'action_field_name' => $actionFieldName,
                'disabled' => $disabled,
            );
        }

        return $newActions;
    }

    /**
     * Format links
     *
     * @param array $links Links
     *
     * @return array
     */
    private function formatLinks($links)
    {
        $newLinks = array();

        foreach ($links as $name => $details) {
            $value = isset($details['value']) ? $details['value'] : ucwords($name);

            $label = isset($details['label']) ? $this->translator->translate($details['label']) : $value;

            $class = isset($details['class']) ? $details['class'] : 'govuk-button govuk-button--secondary';

            $route = isset($details['route']['route']) ? $details['route']['route'] : null;
            $params = isset($details['route']['params']) ? $details['route']['params'] : [];
            $options = isset($details['route']['options']) ? $details['route']['options'] : [];
            $reuse = isset($details['route']['reuse']) ? $details['route']['reuse'] : false;

            $newLinks[] = array(
                'href' => $this->getUrl()->fromRoute($route, $params, $options, $reuse),
                'value' => $value,
                'label' => $label,
                'class' => $class
            );
        }

        return $newLinks;
    }

    /**
     * Trim actions
     *
     * @param array $items Actions settings
     *
     * @return array
     */
    private function trimActions(array $items)
    {
        $items = $this->filterByRequireRows($items);
        return $this->filterByInternalReadOnly($items);
    }

    /**
     * Trim(filter) links
     *
     * @param array $items Links settings
     *
     * @return array
     */
    private function trimLinks(array $items)
    {
        return $this->filterByInternalReadOnly($items);
    }

    /**
     * Remove items which require rows
     *
     * @param array $items Items (actions)
     *
     * @return array
     */
    private function filterByRequireRows(array $items)
    {
        if (count($this->rows) !== 0) {
            return $items;
        }

        return array_filter(
            $items,
            function (array $item) {
                return !isset($item['requireRows']) || (bool)$item['requireRows'] === false;
            }
        );
    }

    /**
     * Remove items not available for ReadOnly
     *
     * @param array $items Items (actions)
     *
     * @return array
     */
    private function filterByInternalReadOnly(array $items)
    {
        if (!$this->isInternalReadOnly()) {
            return $items;
        }

        return array_filter(
            $items,
            function (array $item) {
                return isset($item['keepForReadOnly']) && (bool)$item['keepForReadOnly'] === true;
            }
        );
    }


    public function getColumn($name)
    {
        return ($this->hasColumn($name) ? $this->columns[$name] : null);
    }

    public function setColumn($name, $column)
    {
        $this->columns[$name] = $column;
    }

    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * Remove column on the fly
     *
     * @param string $name
     */
    public function removeColumn($name = '')
    {
        if ($this->hasColumn($name)) {
            unset($this->columns[$name]);
        }
    }

    private function authorisedToView($column)
    {
        if (isset($column['permissionRequisites'])) {
            foreach ((array) $column['permissionRequisites'] as $permission) {
                if ($this->authService->isGranted($permission)) {
                    return true;
                }
            }
            return false;
        }

        // if option not set then default to visible
        return true;
    }

    private function shouldHide($column)
    {
        return !($this->authorisedToView($column)) ||
        ($this->isDisabled && isset($column['hideWhenDisabled']) && $column['hideWhenDisabled']);
    }

    public function isRowDisabled($row)
    {
        if (!isset($this->settings['row-disabled-callback'])) {
            return false;
        }

        $callback = $this->settings['row-disabled-callback'];

        return $callback($row);
    }

    /**
     * Should Paginate
     *
     * @return bool
     */
    protected function shouldPaginate()
    {
        return isset($this->settings['paginate']);
    }

    /**
     * Setup Data Attributes
     *
     * @return void
     */
    protected function setupDataAttributes()
    {
        if (isset($this->variables['dataAttributes']) && is_array($this->variables['dataAttributes'])) {
            $attrs = [];
            foreach ($this->variables['dataAttributes'] as $attribute => $value) {
                $attrs[] = $attribute . '="' . (string)$value . '"';
            }

            $this->variables['dataAttributes'] = implode(' ', $attrs);
            return;
        }

        $this->variables['dataAttributes'] = '';
    }

    /**
     * Return true if the current internal user has read only permissions
     *
     * @return bool
     */
    protected function isInternalReadOnly()
    {
        return (
            $this->authService->isGranted('internal-user')
            && !$this->authService->isGranted('internal-edit')
        );
    }

    /**
     * If internal user has read only permissions remove columns with particular types
     *
     * @return void
     */
    protected function checkForActionLinks()
    {
        if ($this->isInternalReadOnly()) {
            $typesToRemove = ['ActionLinks', 'DeltaActionLinks'];

            $updatedColumns = [];

            foreach ($this->getColumns() as $column) {
                if (
                    isset($column['type'])
                    && in_array($column['type'], $typesToRemove)
                    && !(
                        isset($column['keepForReadOnly'])
                        && $column['keepForReadOnly']
                    )
                ) {
                    continue;
                }
                $updatedColumns[] = $column;
            }
            $this->setColumns($updatedColumns);
        }
    }

    /**
     * Set setting
     *
     * @param string $key   key
     * @param string $value value
     *
     * @return TableBuilder
     */
    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * Get Csrf Element
     *
     * @return \Laminas\Form\Element\Csrf
     */
    public function getCsrfElement()
    {
        return $this->elmCsrf;
    }

    /**
     * Get authorization service
     *
     * @return AuthorizationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Get translator service
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}

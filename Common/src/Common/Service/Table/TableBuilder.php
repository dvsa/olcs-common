<?php

namespace Common\Service\Table;

use Common\Rbac\Service\Permission;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Exception\MissingFormatterException;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Exception;
use Laminas\Form\Element\Csrf;
use Laminas\Mvc\Controller\Plugin\Url;
use Psr\Container\ContainerInterface;
use Laminas\Mvc\I18n\Translator;

class TableBuilder implements \Stringable
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

    private PaginationHelper $paginationHelper;
    private ContentHelper $contentHelper;
    private string $contentType = self::CONTENT_TYPE_HTML;
    private array $settings = [];
    private array $footer = [];
    private array $variables = [];
    private array $attributes = [];
    private array $columns = [];
    private array $widths = ['checkbox' => '20px'];
    private int $total;
    private int $unfilteredTotal;
    private array $rows = [];
    private int $type = self::TYPE_DEFAULT;
    private int $limit = self::DEFAULT_LIMIT;
    private int $page = 1;
    private Url $url;
    private array|QueryInterface $query = [];
    private string $sort;
    private string $order = 'ASC';
    private string $actionFieldName = 'action';
    private ?string $fieldset;
    private bool $isDisabled = false;
    private Csrf $elmCsrf;
    private array $urlParameterNameMap = [];

    public function getUrlParameterNameMap(): array
    {
        return $this->urlParameterNameMap;
    }

    public function setUrlParameterNameMap(array $urlParamNameMap): self
    {
        assert(array_reduce($urlParamNameMap, static fn($carry, $mappedValue) => $carry && is_string($mappedValue), true), 'Expected all mapped values to be strings');
        $this->urlParameterNameMap = $urlParamNameMap;
        return $this;
    }

    protected function mapUrlParameterName(string $urlParam): string
    {
        return $this->getUrlParameterNameMap()[$urlParam] ?? $urlParam;
    }

    public function __construct(
        private ContainerInterface $serviceLocator,
        private Permission $permissionService,
        private Translator $translator,
        private UrlHelperService $urlHelper,
        private array $applicationConfig,
        private FormatterPluginManager $formatterPluginManager
    ) {
    }

    public function setDisabled(bool $disabled): void
    {
        $this->isDisabled = $disabled;
    }

    public function setActionFieldName(string $name): void
    {
        $this->actionFieldName = $name;
    }

    public function getActionFieldName(): string
    {
        if (!empty($this->fieldset)) {
            return $this->fieldset . '[' . $this->actionFieldName . ']';
        }

        return $this->actionFieldName;
    }

    public function setFieldset(string $name): void
    {
        $this->fieldset = $name;
    }

    public function getFieldset(): ?string
    {
        return $this->fieldset;
    }

    public function setType(int $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function setSettings(array $settings = []): void
    {
        $this->settings = $settings;
    }

    public function getSetting(string $name, string|false|null $default = null): mixed
    {
        return $this->settings[$name] ?? $default;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function setUnfilteredTotal(int $unfilteredTotal): void
    {
        $this->unfilteredTotal = $unfilteredTotal;
    }

    public function setRows(array $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function setFooter(array $footer = []): void
    {
        $this->footer = $footer;
    }

    public function getFooter(): array
    {
        return $this->footer;
    }

    public function hasAction(string $name): bool
    {
        return isset($this->settings['crud']['actions'][$name]);
    }

    public function removeAction(string $name): void
    {
        if ($this->hasAction($name)) {
            unset($this->settings['crud']['actions'][$name]);
        }
    }

    public function removeActions(): void
    {
        foreach ($this->settings['crud']['actions'] as $key => $config) {
            $this->removeAction($key);
        }

        // remove any actions that are in the table
        $this->removeColumn('actionLinks');
    }

    /**
     * @param (bool|mixed|null|string)[]|string $settings
     *
     * @psalm-param array{class?: string, value?: 'Annul'|'Reprint'|'Stop', label?: mixed|null|string, requireRows?: bool, keepForReadOnly?: true, key?: 'value'}|string $settings
     */
    public function addAction(string $key, array|string $settings = []): void
    {
        $this->settings['crud']['actions'][$key] = $settings;
    }

    public function getAction(string $key): string
    {
        return $this->settings['crud']['actions'][$key];
    }

    public function disableAction(string $name): void
    {
        if ($this->hasAction($name)) {
            $this->settings['crud']['actions'][$name]['disabled'] = 'disabled';
        }
    }

    public function getContentHelper(): ContentHelper
    {
        if (empty($this->contentHelper)) {
            if (!isset($this->applicationConfig['tables']['partials'][$this->contentType])) {
                throw new Exception('Table partial location not defined in config');
            }

            $this->contentHelper = new ContentHelper(
                $this->applicationConfig['tables']['partials'][$this->contentType],
                $this
            );
        }

        return $this->contentHelper;
    }

    /**
     * @psalm-param 'csv' $type
     */
    public function setContentType(string $type): void
    {
        $this->contentType = $type;
    }

    public function getPaginationHelper(): PaginationHelper
    {
        if (empty($this->paginationHelper)) {
            $this->paginationHelper = new PaginationHelper($this->getPage(), $this->getTotal(), $this->getLimit());
            $this->paginationHelper->setTranslator($this->translator);
        }

        return $this->paginationHelper;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setVariables(array $variables = []): void
    {
        $this->variables = $variables;
    }

    public function setVariable(string $name, string $value): void
    {
        $this->variables[$name] = $value;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getVariable(string $name): mixed
    {
        return ($this->variables[$name] ?? '');
    }

    public function setColumns(array $columns): void
    {
        $this->columns = [];

        foreach ($columns as $key => $column) {
            if (!is_string($key)) {
                $key = $column['name'] ?? null;
            }

            if ($key == null) {
                $this->columns[] = $column;
            } else {
                $this->columns[$key] = $column;
            }
        }
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function shouldHideTitle(): bool
    {
        return $this->settings['hide_title'] ?? false;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function hasRows(): bool
    {
        return count($this->rows) > 0;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getQuery(): object|array
    {
        return $this->query;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function prepareTable(array|string $config, array $data = [], array $params = []): static
    {
        $this->loadConfig($config);
        $this->loadData($data);
        $this->loadParams($params);
        $this->setupAction();
        $this->setupDataAttributes();

        return $this;
    }

    public function buildTable(array|string $config, array $data = [], array $params = [], bool $render = true): string
    {
        $this->prepareTable($config, $data, $params);

        if ($render) {
            return $this->render();
        }
        return $this;
    }

    /**
     * @param (array|string|true)[][][]|string $config
     *
     * @psalm-param 'test'|array{settings: array{paginate: array, crud: array{actions: array}}, columns: list{list{'bar'}, array{type: 'ActionLinks', keepForReadOnly: true}, array{type: 'ActionLinks'}, array{type: 'DeltaActionLinks'}}} $config
     * @throws Exception
     */
    public function loadConfig(array|string $config): bool
    {
        if (!is_array($config)) {
            $config = $this->getConfigFromFile($config);
        }

        $config = array_merge(
            [
                'settings' => [],
                'attributes' => [],
                'columns' => [],
                'footer' => []
            ],
            $config
        );

        $this->setSettings($config['settings']);

        $this->setPaginationDefaults();

        $this->maybeSetActionFieldName();

        $config['variables']['hidden'] = $this->settings['crud']['formName'] ?? 'default';

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

    private function setPaginationDefaults(): void
    {
        if (!$this->shouldPaginate()) {
            return;
        }
        if (isset($this->settings['paginate']['limit'])) {
            return;
        }
        $this->settings['paginate']['limit'] = [
            'default' => 10,
            'options' => [10, 25, 50]
        ];
    }

    private function translateTitle(array &$config): void
    {
        if (isset($config['variables']['title'])) {
            $config['variables']['title'] = $this->translator->translate($config['variables']['title']);
        }
    }

    private function maybeSetActionFieldName(): void
    {
        if (isset($this->settings['crud']['action_field_name'])) {
            $this->setActionFieldName($this->settings['crud']['action_field_name']);
        }
    }

    public function loadData(array $data = []): void
    {
        if (isset($data['Results'])) {
            $data['results'] = $data['Results'];
            unset($data['Results']);
        }

        if (isset($data['Count'])) {
            $data['count'] = $data['Count'];
            unset($data['Count']);
        }

        $this->setRows($data['results'] ?? $data);
        $this->setTotal($data['count'] ?? count($this->rows));
        $this->setUnfilteredTotal($data['count-unfiltered'] ?? $this->getTotal());
        // if there's only one row and we have a singular title, use it
        if ($this->getTotal() != 1) {
            return;
        }
        if (!$this->getVariable('titleSingular')) {
            return;
        }
        $this->setVariable('title', $this->translator->translate($this->getVariable('titleSingular')));
    }

    public function loadParams(array $array = []): void
    {
        if (!isset($array['url'])) {
            $array['url'] = $this->urlHelper;
        }

        $defaults = [
            'limit' => $this->settings['paginate']['limit']['default'] ?? 10,
            'page' => self::DEFAULT_PAGE,
            'sort' => '',
            'order' => 'ASC'
        ];

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

    public function setupAction(): void
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
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        } catch (Exception $exception) {
            $content = $exception->getMessage();

            return $content . $exception->getTraceAsString();
        }
    }

    public function render(): string
    {
        return $this->replaceContent($this->renderTable(), $this->getVariables());
    }

    public function getConfigFromFile(string $name): array
    {
        if (
            !isset($this->applicationConfig['tables']['config'])
            || empty($this->applicationConfig['tables']['config'])
        ) {
            throw new Exception('Table config location not defined');
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
            throw new Exception('Table configuration not found');
        }

        return include($configFile);
    }

    public function renderTableFooter(): string
    {
        if ($this->footer === []) {
            return '';
        }

        $columns = [];

        foreach ($this->footer as $column) {
            $columns[] = $this->renderTableFooterColumn($column);
        }

        $content = $this->renderTableFooterColumns($columns);

        return $this->replaceContent('{{[elements/tableFooter]}}', ['content' => $content]);
    }

    private function renderTableFooterColumn(array $column): array
    {
        $column = array_merge(
            [
                'type' => 'td',
                'colspan' => '',
                'align' => '',
            ],
            $column
        );

        $details = ['content' => ''];

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

    private function renderTableFooterColumns(array $columns): string
    {
        $content = '';

        foreach ($columns as $details) {
            $content .= $this->replaceContent('{{[elements/footerColumn]}}', $details);
        }

        return $content;
    }

    public function renderTable(): string
    {
        $this->setType($this->whichType());

        $this->elmCsrf = new Csrf(
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

    private function whichType(): int
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

    public function renderLayout(string $name): string
    {
        if ($name === 'default' && (empty($this->unfilteredTotal) && $this->rows === [])) {
            return $this->renderLayout('default_empty');
        }

        return $this->getContentHelper()->renderLayout($name);
    }

    public function renderTotal(): string
    {
        if (
            $this->getSetting('overrideTotal', false)
            || !$this->shouldPaginate()
            && !$this->getSetting('showTotal', false)
        ) {
            return '';
        }

        $total = $this->total;

        return $this->replaceContent(' {{[elements/total]}}', ['total' => $total]);
    }

    public function renderCaption(): string
    {
        return trim($this->renderTotal() . ' ' . $this->getVariable('title'));
    }

    public function renderActions(): string
    {
        $hasActions = in_array(
            $this->type,
            [
                self::TYPE_CRUD,
                self::TYPE_HYBRID,
                self::TYPE_FORM_TABLE
            ]
        );

        if ($this->isDisabled || !$hasActions) {
            return '';
        }

        $crud = $this->getSetting('crud');

        $actions = $this->trimActions($crud['actions'] ?? []);
        $links = $this->trimLinks($crud['links'] ?? []);

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

    public function renderDropdownActions(array $actions = [], array $links = []): string
    {
        $options = '';

        foreach ($actions as $details) {
            $options .= $this->replaceContent('{{[elements/actionOption]}}', $details);
        }

        $content = '';

        if (!empty($links)) {
            $content .= $this->renderLinks($links);
        }

        return $content . $this->replaceContent(
            '{{[elements/actionSelect]}}',
            ['option' => $options, 'action_field_name' => $this->getActionFieldName()]
        );
    }

    public function renderButtonActions(array $actions = [], int $collapseAt = 0, array $links = []): string
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
                ++$i;
            }
            return $content . $this->renderMoreActions($actions);
        }

        foreach ($actions as $details) {
            $content .= $this->replaceContent('{{[elements/actionButton]}}', $details);
        }

        return $content;
    }

    public function renderLinks(array $links = []): string
    {
        $content = '';

        foreach ($links as $details) {
            $content .= $this->replaceContent('{{[elements/link]}}', $details);
        }

        return $content;
    }

    private function renderMoreActions(array $actions): string
    {
        $content = '';
        if (!empty($actions)) {
            $moreActions = [];
            foreach ($actions as $details) {
                //  add css class to items
                $cssClasses = ($details['class'] ?? '');
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

    public function renderFooter(): string
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

    public function renderLimitOptions(): string
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

                $details = [
                    'option' => $option,
                    'link' => $this->generatePaginationUrl([
                        $this->mapUrlParameterName('page') => 1,
                        $this->mapUrlParameterName('limit') => $option
                    ]),
                ];
                $option = $this->replaceContent('{{[elements/limitLink]}}', $details);

                $limitDetails = ['class' => $class, 'option' => $option];

                $content .= $this->replaceContent('{{[elements/limitOption]}}', $limitDetails);
        }

        return $content;
    }

    public function renderPageOptions(): string
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

            $details = array_merge(['class' => ''], $details);

            $content .= $this->replaceContent('{{[elements/paginationItem]}}', $details);
        }

        if ($content !== '' && $content !== '0') {
            $content = $this->replaceContent('{{[elements/paginationList]}}', ['items' => $content]);
        }

        if (!empty($options['next'])) {
            $options['next']['link'] = $this->getPageLink($options['next']['page']);
            $nextContent = $this->replaceContent('{{[elements/paginationNext]}}', $options['next']);
        }

        return $previousContent . $content . $nextContent;
    }

    public function renderHeaderColumn(array $column, string $wrapper = '{{[elements/th]}}'): ?string
    {
        if ($this->shouldHide($column) || $this->getVariable('hide_column_headers')) {
            return null;
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
                [
                    $this->mapUrlParameterName('sort') => $column['sort'],
                    $this->mapUrlParameterName('order') => $column['order']
                ]
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
     * @psalm-param array{colspan?: '2', class?: 'a-class', 'data-empty'?: ' '} $customAttributes
     */
    public function renderBodyColumn(array $row, array $column, string $wrapper = '{{[elements/td]}}', array $customAttributes = []): ?string
    {
        if ($this->shouldHide($column)) {
            return null;
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
            $formattedContent = $row['content'] ?? null;
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

    private function processBodyColumnAttributes(array $column, $customAttributes): string
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
            if (trim($value) === '') {
                continue;
            }
            if (trim($value) === '0') {
                continue;
            }

            $plainAttributes .= ' ' . $attribute . '="' . $value . '"';
        }

        return $plainAttributes;
    }

    public function hasAnyTitle(): bool
    {
        $columns = $this->getColumns();
        foreach ($columns as $column) {
            if (isset($column['title'])) {
                return true;
            }
        }

        return false;
    }

    public function renderExtraRows(): string
    {
        $content = '';

        if (count($this->getRows()) === 0) {
            $columns = $this->getColumns();

            $message = $this->unfilteredTotal > 0 ? 'There are no results matching your search' : $this->getEmptyMessage();

            $vars = [
                'colspan' => count($columns),
                'message' => $message
            ];

            $content .= $this->replaceContent('{{[elements/emptyRow]}}', $vars);
        }

        return $content;
    }

    /**
     * @psalm-param ''|'foo'|'selfserve-app-subSection-your-business-people-ltd.table.empty-message' $message
     */
    public function setEmptyMessage(string $message): void
    {
        $this->variables['empty_message'] = $message;
    }

    public function getEmptyMessage(): string
    {
        $message = isset($this->variables['empty_message'])
            ? $this->replaceContent($this->variables['empty_message'], $this->getVariables())
            : 'The table is empty';

        return $this->translator->translate($message);
    }

    private function callFormatter(array $column, array $data): mixed
    {
        if (is_string($column['formatter'])) {
            // Remove the leading namespace separator if exists
            $formatterClass = ltrim($column['formatter'], '\\');

            // Check if formatterClass exists
            if (!class_exists($formatterClass) || !$this->formatterPluginManager->has($formatterClass)) {
                throw new MissingFormatterException('Missing table formatter: ' . $column['formatter']);
            }

            $column['formatter'] = $this->formatterPluginManager->get($formatterClass);
        }

        if (is_object($column['formatter'])) {
            if (method_exists($column['formatter'], 'format')) {
                return $column['formatter']->format($data, $column);
            }
            if ($column['formatter'] instanceof \Closure) {
                return $column['formatter']($data, $column);
            }
        }

        return '';
    }


    public function renderAttributes(array $attrs = []): string
    {
        return $this->getContentHelper()->renderAttributes($attrs);
    }

    public function replaceContent(string $content, array $vars = []): string
    {
        return $this->getContentHelper()->replaceContent($content, $vars);
    }

    /**
     * @psalm-param 'licence_case_action'|'licence_case_list/pagination'|null $route
     */
    private function generateUrl(array $data = [], string|null $route = null, array|false $options = [], bool $reuseMatchedParams = true): string
    {
        if (is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = [];
        }

        return $this->getUrl()->fromRoute($route, $data, $options, $reuseMatchedParams);
    }

    private function getPageLink($page): string
    {
        return $this->generatePaginationUrl(
            [
                $this->mapUrlParameterName('page') => $page,
                $this->mapUrlParameterName('limit') => $this->getLimit(),
            ]
        );
    }

    private function generatePaginationUrl(array $data = [], string $route = null): string
    {

        /** @var Url $url */
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

    private function formatActionContent(array $actions, string $overrideFormat, int $collapseAt = 0, array $newLinks = []): string
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

    private function formatActions(array $actions): array
    {
        $newActions = [];

        foreach ($actions as $name => $details) {
            $value = $details['value'] ?? ucwords($name);

            $label = isset($details['label']) ? $this->translator->translate($details['label']) : $value;

            $class = $details['class'] ?? 'govuk-button govuk-button--secondary';

            $id = $details['id'] ?? $name;

            $disabled = $details['disabled'] ?? '';
            if ($disabled) {
                $class .= ' js-force-disable';
            }

            $actionFieldName = $this->getActionFieldName();

            $newActions[] = [
                'name' => $name,
                'id' => $id,
                'value' => $value,
                'label' => $label,
                'class' => $class,
                'action_field_name' => $actionFieldName,
                'disabled' => $disabled,
            ];
        }

        return $newActions;
    }

    private function formatLinks(array $links): array
    {
        $newLinks = [];

        foreach ($links as $name => $details) {
            $value = $details['value'] ?? ucwords($name);

            $label = isset($details['label']) ? $this->translator->translate($details['label']) : $value;

            $class = $details['class'] ?? 'govuk-button govuk-button--secondary';

            $route = $details['route']['route'] ?? null;
            $params = $details['route']['params'] ?? [];
            $options = $details['route']['options'] ?? [];
            $reuse = $details['route']['reuse'] ?? false;

            $newLinks[] = [
                'href' => $this->getUrl()->fromRoute($route, $params, $options, $reuse),
                'value' => $value,
                'label' => $label,
                'class' => $class
            ];
        }

        return $newLinks;
    }

    private function trimActions(array $items): array
    {
        $items = $this->filterByRequireRows($items);
        return $this->filterByInternalReadOnly($items);
    }

    private function trimLinks(array $items): array
    {
        return $this->filterByInternalReadOnly($items);
    }

    private function filterByRequireRows(array $items): array
    {
        if (count($this->rows) !== 0) {
            return $items;
        }

        return array_filter(
            $items,
            static fn(array $item) => !isset($item['requireRows']) || (bool)$item['requireRows'] === false
        );
    }

    private function filterByInternalReadOnly(array $items): array
    {
        if (!$this->isInternalReadOnly()) {
            return $items;
        }

        return array_filter(
            $items,
            static fn(array $item) => isset($item['keepForReadOnly']) && (bool)$item['keepForReadOnly']
        );
    }


    public function getColumn(string $name): ?array
    {
        return ($this->hasColumn($name) ? $this->columns[$name] : null);
    }

    public function setColumn(string $name, array $column): void
    {
        $this->columns[$name] = $column;
    }

    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    public function removeColumn(string $name = ''): void
    {
        if ($this->hasColumn($name)) {
            unset($this->columns[$name]);
        }
    }

    private function authorisedToView(array $column): bool
    {
        if (isset($column['permissionRequisites'])) {
            foreach ((array) $column['permissionRequisites'] as $permission) {
                if ($this->permissionService->isGranted($permission)) {
                    return true;
                }
            }

            return false;
        }

        // if option not set then default to visible
        return true;
    }

    private function shouldHide(array $column): bool
    {
        if (!($this->authorisedToView($column))) {
            return true;
        }
        return $this->isDisabled && isset($column['hideWhenDisabled']) && $column['hideWhenDisabled'];
    }

    public function isRowDisabled(array $row): bool
    {
        if (!isset($this->settings['row-disabled-callback'])) {
            return false;
        }

        $callback = $this->settings['row-disabled-callback'];

        return $callback($row);
    }

    protected function shouldPaginate(): bool
    {
        return isset($this->settings['paginate']);
    }

    protected function setupDataAttributes(): void
    {
        if (isset($this->variables['dataAttributes']) && is_array($this->variables['dataAttributes'])) {
            $attrs = [];
            foreach ($this->variables['dataAttributes'] as $attribute => $value) {
                $attrs[] = $attribute . '="' . $value . '"';
            }

            $this->variables['dataAttributes'] = implode(' ', $attrs);
            return;
        }

        $this->variables['dataAttributes'] = '';
    }

    /**
     * If internal user has read only permissions remove columns with particular types
     */
    protected function checkForActionLinks(): void
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

    public function setSetting(string $key, mixed $value): TableBuilder
    {
        $this->settings[$key] = $value;
        return $this;
    }

    public function getCsrfElement(): Csrf
    {
        return $this->elmCsrf;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getServiceLocator(): ContainerInterface
    {
        return $this->serviceLocator;
    }

    public function isInternalReadOnly(): bool
    {
        return $this->permissionService->isInternalReadOnly();
    }
}

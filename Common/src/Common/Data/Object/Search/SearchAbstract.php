<?php

namespace Common\Data\Object\Search;

/**
 * Class SearchAbstract
 * @package Common\Data\Object\Search
 */
abstract class SearchAbstract
{
    /**
     * @var
     */
    protected $title;
    /**
     * @var
     */
    protected $key;

    /**
     * @var
     */
    protected $searchIndices;

    /**
     * @var string
     */
    protected $displayGroup = 'all';

    /**
     * @return mixed
     */
    public function getSearchIndices()
    {
        return $this->searchIndices;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return ['title' => $this->getTitle()];
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getDateRanges()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * @return array
     */
    abstract public function getColumns();

    /**
     * @return array
     */
    public function getTableConfig()
    {
        return [
            'variables' => $this->getVariables(),
            'settings' => $this->getSettings(),
            'attributes' => $this->getAttributes(),
            'columns' => $this->getColumns()
        ];
    }

    /**
     *
     * @param array $queryParams
     * @return array
     */
    public function getNavigation(array $queryParams = [])
    {
        return [
            'label' => $this->getTitle(),
            'route' => 'search',
            'params' => ['index' => $this->getKey(), 'action' => 'reset'],
            'query' => $queryParams
        ];
    }

    /**
     * @return string
     */
    public function getDisplayGroup()
    {
        return $this->displayGroup;
    }
}

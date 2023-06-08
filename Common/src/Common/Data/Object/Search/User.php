<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

/**
 * Class User
 * @package Common\Data\Object\Search
 */
class User extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Users';
    /**
     * @var string
     */
    protected $key = 'user';

    /**
     * @var string
     */
    protected $searchIndices = 'user';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {

            $this->filters = [
                new Filter\UserType(),
                new Filter\Partner(),
                new Filter\Team(),
                new Filter\LocalAuthority(),
            ];
        }

        return $this->filters;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return [
            'title' => $this->getTitle(),
            'empty_message' => 'search-no-results-internal',
            'action_route' => [
                'route' => 'admin-dashboard/admin-user-management',
                'params' => ['action' => null]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            'crud' => [
                'actions' => [
                    'add' => [
                        'class' => 'govuk-button', 
                        'requireRows' => false
                    ],
                    'edit' => [
                        'requireRows' => true, 
                        'class' => 'govuk-button govuk-button--secondary js-require--one'
                    ],
                    'delete' => [
                        'label' => 'action_links.remove',
                        'requireRows' => true, 
                        'class' => 'govuk-button govuk-button--secondary js-require--one'
                    ]
                ]
            ],
            'paginate' => [
                'limit' => [
                    'default' => 25,
                    'options' => array(10, 25, 50)
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getColumns()
    {
         return array(
            array(
                'title' => 'Username',
                'formatter' => function ($data) {
                    return '<a class="govuk-link" href="/admin/user-management/users/edit/' . $data['userId']
                     . '">' . $data['loginId'] . '</a>';
                }
            ),
            array(
                'title' => 'Name',
                'formatter' => function ($data) {
                    return $data['forename'] . ' ' .
                    $data['familyName'];
                }
            ),
            array(
                'title' => 'Email address',
                'name' => 'emailAddress'
            ),
            array(
                'title' => 'Entity',
                'name' => 'entity'
            ),
             array(
                 'title' => 'Licences',
                 'name' => 'licNos'
             ),
            array(
                'title' => '',
                'width' => 'checkbox',
                'data-field' => 'userId',
                'type' => 'CustomSelector',
            )
        );
    }
}

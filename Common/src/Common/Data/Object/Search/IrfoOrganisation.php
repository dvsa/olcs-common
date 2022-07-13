<?php
namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;

/**
 * Class IrfoOrganisation
 * @package Common\Data\Object\Search
 */
class IrfoOrganisation extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'IRFO';
    /**
     * @var string
     */
    protected $key = 'irfo';

    /**
     * @var string
     */
    protected $searchIndices = 'irfo';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

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
    public function getColumns()
    {
        return [
            [
                'title' => 'Operator no',
                'formatter' => function ($data, $column, $serviceLocator) {
                    $url = $serviceLocator->get('Helper\Url')->fromRoute(
                        'operator/business-details',
                        ['organisation' => $data['orgId']]
                    );
                    return sprintf('<a class="govuk-link" href="%s">%d</a>', $url, $data['orgId']);
                }
            ],
            ['title' => 'Operator name', 'name'=> 'orgName'],
        ];
    }
}

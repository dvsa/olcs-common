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
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {

            $this->filters = [
                new Filter\IrfoAuthStatus(),
            ];
        }

        return $this->filters;
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
    public function getColumns()
    {
        return [
            [
                'title' => 'Operator no',
                'formatter' => function ($data, $column, $serviceLocator) {
                    $urlHelper  = $serviceLocator->get('Helper\Url');
                    $url =  $urlHelper->fromRoute('operator', ['organisation' => $data['orgId']]);
                    return sprintf('<a href="%s">%d</a>', $url, $data['orgId']);
                }
            ],
            ['title' => 'Operator name', 'name'=> 'orgName'],
            ['title' => 'Irfo auth status', 'name'=> 'irfoStatusDesc'],
            ['title' => 'Related licence number', 'formatter' => function ($data, $column, $serviceLocator) {
                $urlHelper  = $serviceLocator->get('Helper\Url');
                if (trim($data['relatedLicNum']) === '') {
                    return '';
                }
                $licNos = explode(',', $data['relatedLicNum']);
                foreach ($licNos as $licNo) {
                    $licNo = trim($licNo);
                    $url =
                    $links[] = sprintf(
                        '<a href="%s">%s</a>',
                        $urlHelper->fromRoute('licence-no', ['licNo' => $licNo]),
                        $licNo
                    );
                }
                return implode(', ', $links);
            }],
            ['title' => 'Service route from', 'name'=> 'serviceRouteFrom'],
            ['title' => 'Service route to', 'name'=> 'serviceRouteTo'],
        ];
    }
}

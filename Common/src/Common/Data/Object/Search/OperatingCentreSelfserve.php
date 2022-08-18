<?php

namespace Common\Data\Object\Search;

use Common\Data\Object\Search\Aggregations\Terms as Filter;
use Common\Util\Escape;

/**
 * Class Address
 * @package Common\Data\Object\Search
 */
class OperatingCentreSelfserve extends InternalSearchAbstract
{
    /**
     * @var string
     */
    protected $title = 'Operating centres';

    /**
     * @var string
     */
    protected $key = 'address';

    /**
     * @var string
     */
    protected $searchIndices = 'address';

    /**
     * Contains an array of the instantiated filters classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            'paginate' => [
                'limit' => [
                    'options' => [10, 25, 50, 100]
                ]
            ],
            'layout' => 'headless'
        ];
    }

    /**
     * Returns an array of filters for this index
     *
     * @return array
     */
    public function getFilters()
    {
        if (empty($this->filters)) {
            $this->filters = [
                new Filter\LicenceStatus(),
                new Filter\GoodsOrPsv(),
            ];
        }

        return $this->filters;
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            [
                'title' => 'Licence number',
                'name'=> 'licNo',
                'formatter' => function ($data, $col, $sl) {
                    /** @var \Laminas\I18n\Translator\TranslatorInterface $translator */
                    $translator = $sl->get('translator');

                    return sprintf(
                        '<a class="govuk-link" href="%s">%s</a><br/>%s',
                        '/view-details/licence/' . $data['licId'],
                        Escape::html($data['licNo']),
                        $translator->translate($data['licStatusDesc'])
                    );
                }
            ],
            [
                'title' => 'Operator name',
                'name'=> 'orgName'
            ],
            [
                'title' => 'Address',
                'formatter' => 'Address',
                'addressFields' => [
                    'street', 'locality', 'town', 'postcode'
                ]
            ],
        ];
    }
}

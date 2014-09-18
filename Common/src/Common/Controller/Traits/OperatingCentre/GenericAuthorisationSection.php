<?php

/**
 * Generic Authorisation Section
 *
 * Internal/External - Application/Licence Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

use Common\Controller\Traits;

/**
 * Generic Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericAuthorisationSection
{
    use Traits\TrafficAreaTrait;

    /**
     * Holds the table data
     *
     * @var array
     */
    protected $tableData = null;

    /**
     * Table data bundle
     *
     * @var array
     */
    public static $tableDataBundle = array(
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    /**
     * Data map
     *
     * @var array
     */
    protected $sharedDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data',
                'dataTrafficArea'
            ),
        ),
    );

    /**
     * Form tables name
     *
     * @var string
     */
    protected $sharedFormTables = array(
        'table' => 'authorisation_in_form',
    );

    /**
     * Get data map
     *
     * @return array
     */
    protected function getDataMap()
    {
        return $this->sharedDataMap;
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        return $this->sharedFormTables;
    }

    /**
     * Get data bundle
     *
     * @return array
     */
    protected function getDataBundle()
    {
        return $this->sharedDataBundle;
    }

    /**
     * Get service
     *
     * @return type
     */
    protected function getService()
    {
        return $this->sharedService;
    }

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit operating centre
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete sub action
     *
     * @return Response
     */
    public function deleteAction()
    {
        $this->maybeClearTrafficAreaId();
        return $this->delete();
    }

    /**
     * Get data for table
     *
     * @param string $id
     */
    protected function getFormTableData($id, $table)
    {
        if (is_null($this->tableData)) {
            $this->tableData = $this->getSummaryTableData($id, $this, '');
        }

        return $this->tableData;
    }

    /**
     * Format summary table data
     *
     * @param array $data
     * @return array
     */
    protected static function formatSummaryTableData($data)
    {
        $newData = array();

        foreach ($data['Results'] as $row) {

            $newRow = $row;

            if (isset($row['operatingCentre']['address'])) {

                unset($row['operatingCentre']['address']['id']);
                unset($row['operatingCentre']['address']['version']);

                $newRow = array_merge($newRow, $row['operatingCentre']['address']);
            }

            unset($newRow['operatingCentre']);

            $newData[] = $newRow;
        }

        return $newData;
    }
}

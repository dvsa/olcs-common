<?php

declare(strict_types=1);

namespace Common\Traits;

use Common\Service\Table\TableBuilder;

trait OperatingCentresTableLgvModifierTrait
{
    /**
     * Alter the form table in accordance with lgv requirements
     *
     * @param TableBuilder $tableBuilder
     */
    private function alterFormTableForLgv(TableBuilder $tableBuilder)
    {
        $rows = $tableBuilder->getRows();
        $rowsContainLgvContent = $this->rowsContainLgvContent($rows);

        if (!$rowsContainLgvContent) {
            $columns = $tableBuilder->getColumns();
            $columns['noOfHgvVehiclesRequired']['title'] = str_replace(
                '-hgv',
                '',
                $columns['noOfHgvVehiclesRequired']['title']
            );
            $tableBuilder->setColumns($columns);

            $tableBuilder->removeColumn('noOfLgvVehiclesRequired');
    
            $footer = $tableBuilder->getFooter();
            if (isset($footer['noOfLgvVehiclesRequired'])) {
                unset($footer['noOfLgvVehiclesRequired']);
                $tableBuilder->setFooter($footer);
            }
        }
    }

    /**
     * Whether any of the rows in the provided row data contain values for noOfLgvVehiclesRequired
     *
     * @param array $rows
     *
     * @return bool
     */
    private function rowsContainLgvContent(array $rows)
    {
        foreach ($rows as $row) {
            if ($row['noOfLgvVehiclesRequired']) {
                return true;
            }
        }
            
        return false;
    }
}

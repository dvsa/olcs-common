<?php

/**
 * Shared logic between Business Details controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Shared logic between Business Details controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait BusinessDetailsTrait
{
    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    private function formatDataForSave($data)
    {
        return array(
            'version' => $data['version'],
            'companyOrLlpNo' => $data['data']['companyNumber']['company_number'],
            'name' => $data['data']['name'],
        );
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        $tradingNames = array();
        foreach ($data['tradingNames'] as $tradingName) {
            $tradingNames[] = array('text' => $tradingName['name']);
        }
        return array(
            'version' => $data['version'],
            'data' => array(
                'companyNumber' => array(
                    'company_number' => $data['companyOrLlpNo']
                ),
                'tradingNames' => array(
                    'trading_name' => $tradingNames
                ),
                'name' => $data['name'],
                'type' => $data['type']['id']
            )
        );
    }
}

<?php

/**
 * TxcInbox Entity Service
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Service\Entity;

/**
 * TxcInbox Service
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class TxcInboxEntityService extends AbstractEntityService
{
    const SUBCATEGORY_EBSR = 36; // to-do sub category is 'EBSR' TBC
    const SUBCATEGORY_TRANSXCHANGE_FILE = 107;
    const SUBCATEGORY_TRANSXCHANGE_PDF = 108;

    protected $entity = 'TxcInbox';

    /**
     * Bundle for standard list
     *
     * @var array
     */
    protected $listBundle = [
        'children' => [
            'pdfDocument' => [
                'criteria' => [
                    'subCategory' => self::SUBCATEGORY_TRANSXCHANGE_PDF,
                ]
            ],
            'routeDocument' => [
                'criteria' => [
                    'subCategory' => self::SUBCATEGORY_EBSR,
                ]
            ],
            'zipDocument' => [
                'criteria' => [
                    'subCategory' => self::SUBCATEGORY_TRANSXCHANGE_FILE,
                ]
            ],
            'busReg'
        ]
    ];

    public function fetchBusRegDocuments($id)
    {
        $documents = [];

        $params = [
            'busReg' => $id,
            'localAuthority' => 'NULL'
        ];

        $txcInboxEntries =  $this->getList($params, $this->listBundle);

        if (isset($txcInboxEntries['Results'][0]['routeDocument'])) {
            array_push($documents, $txcInboxEntries['Results'][0]['routeDocument']);
        }
        if (isset($txcInboxEntries['Results'][0]['pdfDocument'])) {
            array_push($documents, $txcInboxEntries['Results'][0]['pdfDocument']);
        }
        if (isset($txcInboxEntries['Results'][0]['zipDocument'])) {
            array_push($documents, $txcInboxEntries['Results'][0]['zipDocument']);
        }
        return $documents;
    }
}

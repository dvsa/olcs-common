<?php

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Form;
use Common\RefData;

/**
 * Financial Evidence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FinancialEvidence implements MapperInterface
{
    /**
     * Map from result
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromResult(array $data)
    {
        $uploadNow = null;
        $uploadLater = null;
        $sendByPost = null;

        // switch / case do not distinguishes 0 and null so need to use this trick
        switch (true)
        {
            case $data['financialEvidenceUploaded'] === RefData::AD_UPLOAD_NOW:
                $uploadNow = RefData::AD_UPLOAD_NOW;
                break;
            case $data['financialEvidenceUploaded'] === RefData::AD_POST:
                $sendByPost = RefData::AD_POST;
                break;
            case $data['financialEvidenceUploaded'] === RefData::AD_UPLOAD_LATER:
                $uploadLater = RefData::AD_UPLOAD_LATER;
                break;
            default:
                $uploadNow = RefData::AD_UPLOAD_NOW;
        }

        $evidenceFieldset = [
            'uploadNowRadio' => $uploadNow,
            'uploadLaterRadio' => $uploadLater,
            'sendByPostRadio' => $sendByPost
        ];

        return [
            'id'       => $data['id'],
            'version'  => $data['version'],
            'evidence' => $evidenceFieldset
        ];
    }

    /**
     * Map from post
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromPost(array $data)
    {
        $uploadNow = null;
        $uploadLater = null;
        $sendByPost = null;

        // switch / case do not distinguishes 0 and null so need to use this trick
        switch (true) {
            case (int) $data['evidence']['uploadNow'] === RefData::AD_UPLOAD_NOW:
                $uploadNow = RefData::AD_UPLOAD_NOW;
                break;
            case (int) $data['evidence']['uploadNow'] === RefData::AD_POST:
                $sendByPost = RefData::AD_POST;
                break;
            case (int) $data['evidence']['uploadNow'] === RefData::AD_UPLOAD_LATER:
                $uploadLater = RefData::AD_UPLOAD_LATER;
                break;
            default:
                $uploadNow = RefData::AD_UPLOAD_NOW;
        }

        $evidenceFieldset = array_merge(
            [
                'uploadNowRadio' => $uploadNow,
                'uploadLaterRadio' => $uploadLater,
                'sendByPostRadio' => $sendByPost,
                'uploadedFileCount' => isset($data['evidence']['files']['list'])
                    ? count(isset($data['evidence']['files']['list']))
                    : 0
            ],
            $data['evidence']
        );
        $data['evidence'] = $evidenceFieldset;

        return $data;
    }

    /**
     * Map from form
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromForm(array $data)
    {
        $uploadNow = null;
        if ((int) $data['evidence']['uploadNowRadio'] === RefData::AD_UPLOAD_NOW) {
            $uploadNow = RefData::AD_UPLOAD_NOW;
        } elseif ((int) $data['evidence']['uploadLaterRadio'] === RefData::AD_UPLOAD_LATER) {
            $uploadNow = RefData::AD_UPLOAD_LATER;
        } elseif ((int) $data['evidence']['sendByPost'] === RefData::AD_POST) {
            $uploadNow = RefData::AD_POST;
        }

        return [
            'id' => $data['id'],
            'version' => $data['version'],
            'financialEvidenceUploaded' => $uploadNow
        ];
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Irhp Permit Type with Validity Date formatter
 */
class IrhpPermitTypeWithValidityDate implements FormatterPluginManagerInterface
{
    private Date $dateFormatter;

    private TranslatorDelegator $translator;

    public function __construct(Date $dateFormatter, TranslatorDelegator $translator)
    {
        $this->dateFormatter = $dateFormatter;
        $this->translator = $translator;
    }

    /**
     * Format data
     *
     * @param array $data
     * @param array $column
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $value = $data[$column['name']];

        if ($data['typeId'] == RefData::ECMT_PERMIT_TYPE_ID && !empty($data['stockValidTo'])) {
            $date = $this->dateFormatter->format(
                $data,
                [
                    'name' => 'stockValidTo',
                    'dateformat' => 'Y',
                ]
            );
            $value = sprintf('%s %s', $value, $date);
        }

        if ($data['typeId'] == RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID && !empty($data['stockValidTo'])) {
            switch (date('Y', strtotime($data['stockValidTo']))) {
                case '2019':
                    $value = sprintf('%s %s', $value, '2019');
                    break;
                default:
                    $value = sprintf('%s %s', $value, $this->translator->translate($data['periodNameKey']));
            }
        }

        return Escape::html($value);
    }
}

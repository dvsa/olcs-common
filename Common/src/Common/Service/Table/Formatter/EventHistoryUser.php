<?php

namespace Common\Service\Table\Formatter;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Event History User
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EventHistoryUser implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;

    public function __construct(TranslatorDelegator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Format
     *
     * @param array $data   Event data
     * @param array $column Column data
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $internalMarker = isset($data['user']['team'])
            ? ' ' . $this->translator->translate('internal.marker')
            : '';

        if ($data['changeMadeBy'] !== null) {
            return $data['changeMadeBy'] . $internalMarker;
        }

        if (isset($data['user']['contactDetails']['person'])) {
            $person = $data['user']['contactDetails']['person'];
            if (!empty($person['forename']) && !empty($person['familyName'])) {
                return $person['forename'] . ' ' . $person['familyName'] . $internalMarker;
            }
        }

        return $data['user']['loginId'] . $internalMarker;
    }
}

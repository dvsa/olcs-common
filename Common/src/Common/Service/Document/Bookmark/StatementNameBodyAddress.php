<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Formatter\Name as NameFormatter;
use Common\Service\Document\Bookmark\Formatter\Address as AddressFormatter;

/**
 * StatementNameBodyAddress bookmark
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementNameBodyAddress extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Statement',
            'data' => [
                'id' => $data['statement']
            ],
            'bundle' => [
                'children' => [
                    'requestorsContactDetails' => [
                        'children' => [
                            'person',
                            'address',
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $person = $this->data['requestorsContactDetails']['person'];
        $address = isset($this->data['requestorsContactDetails']['address'])
                 ? $this->data['requestorsContactDetails']['address'] : [];

        $separator = "\n";

        $oldSep = AddressFormatter::getSeparator();

        AddressFormatter::setSeparator($separator);

        $string = implode(
            $separator,
            array_filter(
                [
                    NameFormatter::format($person),
                    $this->data['requestorsBody'],
                    AddressFormatter::format($address)
                ]
            )
        );

        AddressFormatter::setSeparator($oldSep);

        return $string;
    }
}

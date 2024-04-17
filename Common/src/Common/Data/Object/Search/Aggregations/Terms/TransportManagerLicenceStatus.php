<?php

declare(strict_types=1);

namespace Common\Data\Object\Search\Aggregations\Terms;

class TransportManagerLicenceStatus extends TermsAbstract
{
    protected $title = 'search.form.filter.transport-manager-licence-status';
    protected $key = 'licStatus|appStatusId';

    public function getType(): string
    {
        return self::TYPE_FIXED;
    }

    public function getOptionsKvp(): array
    {
        return $this->getOptions();
    }

    public function getOptions(): array
    {
        return [
            'lsts_valid|lsts_granted|lsts_consideration|apsts_valid|apsts_granted|apsts_consideration' => 'Active only',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Common\Data\Object\Search\Aggregations\Terms;

class MergedStatus extends TermsAbstract
{
    protected $title = 'search.form.filter.merged-status';
    protected $key = 'isMerged';

    public function getType(): string
    {
        return self::TYPE_BOOLEAN;
    }

    public function getOptionsKvp(): array
    {
        return $this->getOptions();
    }

    public function getOptions(): array
    {
        return [
            '1' => 'Show merged',
            '0' => 'Hide merged',
        ];
    }
}

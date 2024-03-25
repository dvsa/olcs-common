<?php

declare(strict_types=1);

namespace Common\Data\Object\Search\Aggregations\Terms;

/**
 * Licence Type filter class.
 *
 * @package Common\Data\Object\Search\Filter
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class DeletedStatus extends TermsAbstract
{
    /**
     * The human readable title of this filter. This may also be used in the front-end (not sure yet).
     *
     * @var string
     */
    protected $title = 'search.form.filter.deleted-status';

    /**
     * The actual name of the field to ask for filter information for.
     *
     * @var string
     */
    protected $key = 'personDeleted';

    public function getType(): string
    {
        return self::TYPE_NULLCHECK;
    }

    public function getOptionsKvp(): array
    {
        return $this->getOptions();
    }

    public function getOptions(): array
    {
        return [
            '1' => 'Show deleted',
            '0' => 'Hide deleted',
        ];
    }
}

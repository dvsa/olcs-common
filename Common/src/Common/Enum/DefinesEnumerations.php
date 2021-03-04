<?php

declare(strict_types=1);

namespace Common\Enum;

interface DefinesEnumerations
{
    /**
     * Gets the enumerations for an enum.
     *
     * @return array
     */
    public function getEnumerations(): array;
}

<?php

declare(strict_types=1);

namespace CommonTest\Helper;

use Common\Helper\Str;
use Common\Test\MockeryTestCase;

/**
 * @see Str
 */
class StrTest extends MockeryTestCase
{
    protected const STRING_WITH_ANCHOR_TAG = '<a>Foo</a>';

    protected const STRING_WITH_NO_HTML = 'foo bar baz';

    /**
     * @test
     */
    public function containsHtml_IsCallable(): void
    {
        $this->assertIsCallable(static fn(string $str): bool => \Common\Helper\Str::containsHtml($str));
    }

    /**
     * @test
     * @depends containsHtml_IsCallable
     */
    public function containsHtml_ReturnsFalseIfStringDoesNotContainHtml(): void
    {
        $this->assertFalse(Str::containsHtml(static::STRING_WITH_NO_HTML));
    }

    /**
     * @test
     * @depends containsHtml_IsCallable
     */
    public function containsHtml_ReturnsTrueIfStringContainsAnAnchor(): void
    {
        $this->assertTrue(Str::containsHtml(static::STRING_WITH_ANCHOR_TAG));
    }
}

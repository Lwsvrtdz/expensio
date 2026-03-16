<?php

declare(strict_types=1);

namespace App\Inputs;

final class PageSize
{
    public const DEFAULT = 25;

    public const LIMIT = 100;

    public function __construct(protected int $size) {}

    public function size(): int
    {
        return max(1, min($this->size, self::LIMIT));
    }
}

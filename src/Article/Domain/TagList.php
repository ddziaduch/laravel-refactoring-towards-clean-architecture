<?php

namespace Conduit\Article\Domain;

/**
 * Tags of an article: trimmed, non-empty and unique, in the given order.
 */
final readonly class TagList
{
    /**
     * @var list<string>
     */
    public array $names;

    /**
     * @param  list<string>  $names
     */
    public function __construct(array $names)
    {
        $this->names = array_values(array_unique(array_filter(
            array_map(trim(...), $names),
            fn (string $name): bool => $name !== '',
        )));
    }
}

<?php

namespace Conduit\Identity\Application\Port\Primary;

/**
 * Only the given fields change. Bio and image may be explicitly cleared with null,
 * which is why a missing field and a null field mean different things.
 */
final readonly class UserChanges
{
    /**
     * @param  array{username?: string, email?: string, password?: string, bio?: ?string, image?: ?string}  $fields
     */
    public function __construct(private array $fields) {}

    public function has(string $field): bool
    {
        return array_key_exists($field, $this->fields);
    }

    public function get(string $field): ?string
    {
        return $this->fields[$field] ?? null;
    }
}

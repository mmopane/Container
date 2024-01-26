<?php

namespace MMOPANE\Container;

class DefinitionCollection
{
    /** @var array<string, Definition> */
    protected array $definitions = [];

    public function add(string $id, Definition $definition): Definition
    {
        return $this->definitions[$id] = $definition;
    }

    public function get(string $id): Definition|null
    {
        return $this->definitions[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }
}
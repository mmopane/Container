<?php

namespace MMOPANE\Container;

class ProviderCollection
{
    /** @var array<string, ServiceProvider> */
    protected array $providers = [];

    public function add(string $id, ServiceProvider $definition): ServiceProvider
    {
        return $this->providers[$id] = $definition;
    }

    public function get(string $id): ServiceProvider|null
    {
        return $this->providers[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->providers);
    }

    public function findByProvides(string $id): ServiceProvider|null
    {
        foreach ($this->providers as $provider)
        {
            if($provider->provides($id))
                return $provider;
        }
        return null;
    }
}
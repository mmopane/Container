<?php

namespace MMOPANE\Container;

use MMOPANE\Collection\Collection;
use MMOPANE\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var Collection<array-key, Definition>
     */
    protected Collection $definitions;

    /**
     * @var Collection<array-key, ServiceProvider>
     */
    protected Collection $providers;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->definitions = new Collection();
        $this->providers = new Collection();
    }

    /**
     * Get definition instance or value.
     * @template T
     * @param string|class-string<T> $id
     * @return mixed|T
     */
    public function get(string $id): mixed
    {
        if($this->definitions->has($id))
            return $this->definitions->get($id)->resolve();
        $providers = $this->providers->filter(fn (ServiceProvider $provider) => $provider->provides($id));
        if(!$providers->isEmpty())
        {
            $providers->map(fn (ServiceProvider $provider) => $provider->register());
            return $this->definitions->get($id)->resolve();
        }
        throw new NotFoundException('Container: Definition [' . $id . '] is not found!');
    }

    /**
     * Add definition.
     * @param string|class-string $id
     * @param mixed|null $concrete
     * @return Definition
     */
    public function add(string $id, mixed $concrete = null): Definition
    {
        $definition = new Definition($this, $concrete ?? $id);
        $this->definitions->put($id, $definition);
        return $definition;
    }

    /**
     * Determine if an definition exists.
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if($this->definitions->has($id))
            return true;
        $providers = $this->providers->filter(fn (ServiceProvider $provider) => $provider->provides($id));
        return !$providers->isEmpty();
    }

    /**
     * Add service provider.
     * @param ServiceProvider $provider
     * @return $this
     */
    public function addServiceProvider(ServiceProvider $provider): self
    {
        $provider->setContainer($this);
        $this->providers->put($provider->getID(), $provider);
        return $this;
    }
}
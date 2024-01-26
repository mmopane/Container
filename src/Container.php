<?php

namespace MMOPANE\Container;

use MMOPANE\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected DefinitionCollection $definitions;
    protected ProviderCollection $providers;

    public function __construct()
    {
        $this->definitions = new DefinitionCollection();
        $this->providers = new ProviderCollection();
    }

    /**
     * @template T
     * @param string|class-string<T> $id
     * @return mixed|T
     */
    public function get(string $id): mixed
    {
        if(!$this->has($id))
            throw new NotFoundException('Container: Definition [' . $id . '] is not found!');

        if($this->definitions->has($id))
            return $this->definitions->get($id)->resolve();

        $this->providers->findByProvides($id)->register();
        return $this->get($id);
    }

    public function add(string $id, mixed $concrete = null): Definition
    {
        return $this->definitions->add($id, new Definition($this, $concrete ?? $id));
    }

    public function has(string $id): bool
    {
        if($this->definitions->has($id))
            return true;
        if(!is_null($this->providers->findByProvides($id)))
            return true;
        return false;
    }

    public function addServiceProvider(ServiceProvider $provider): self
    {
        $provider->setContainer($this);
        $this->providers->add($provider->getId(), $provider);
        return $this;
    }
}
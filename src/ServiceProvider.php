<?php

namespace MMOPANE\Container;

use MMOPANE\Container\Exception\ContainerException;

abstract class ServiceProvider
{
    private Container|null $container = null;
    protected string $id;

    public function __construct(string|null $id = null)
    {
        $this->id = $id ?? get_class($this);
    }

    public function getContainer(): Container
    {
        if(is_null($this->container))
            throw new ContainerException('ServiceProvider: Container has not ben set!');
        return $this->container;
    }

    public function setContainer(Container $container): self
    {
        $this->container = $container;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    abstract public function provides(string $id): bool;
    abstract public function register(): void;
}
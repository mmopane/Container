<?php

namespace MMOPANE\Container;

use MMOPANE\Container\Exception\ContainerException;

abstract class ServiceProvider
{
    /**
     * @var Container|null
     */
    private Container|null $container = null;
    /**
     * @var string
     */
    protected string $id;

    /**
     * @param string|null $id
     * @return void
     */
    public function __construct(string|null $id = null)
    {
        $this->id = $id ?? get_class($this);
    }

    /**
     * Get container.
     * @return Container
     */
    public function getContainer(): Container
    {
        if(is_null($this->container))
            throw new ContainerException('ServiceProvider: Container has not ben set!');
        return $this->container;
    }

    /**
     * Set container.
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Get service provider ID.
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Does your current service provider definition.
     * @param string $id
     * @return bool
     */
    abstract public function provides(string $id): bool;

    /**
     * Register service.
     * @return void
     */
    abstract public function register(): void;
}
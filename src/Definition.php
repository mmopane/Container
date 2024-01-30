<?php

namespace MMOPANE\Container;

use MMOPANE\Container\Exception\NotFoundException;

class Definition
{
    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @var mixed
     */
    protected mixed $concrete;

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * @var array
     */
    protected array $methods = [];

    /**
     * @var bool
     */
    protected bool $singleton = false;

    /**
     * @var object|null
     */
    protected object|null $instance = null;

    /**
     * @param Container $container
     * @param mixed $concrete
     */
    public function __construct(Container $container, mixed $concrete)
    {
        $this->container = $container;
        $this->concrete = $concrete;
    }

    /**
     * Set constructor parameters.
     * @param mixed ...$parameters Constructor parameters.
     * @return $this
     */
    public function setParameters(mixed ...$parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Set public properties.
     * @param string $name Property name.
     * @param mixed $value Property value.
     * @return $this
     */
    public function setProperty(string $name, mixed $value): self
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * Set call methods.
     * @param string $name Method name.
     * @param mixed ...$parameters Method parameters.
     * @return $this
     */
    public function callMethod(string $name, mixed ...$parameters): self
    {
        $this->methods[] = [
            'name' => $name,
            'parameters' => $parameters
        ];
        return $this;
    }

    /**
     * Set singleton status.
     * @param bool $singleton Status.
     * @return $this
     */
    public function setSingleton(bool $singleton): self
    {
        $this->singleton = $singleton;
        return $this;
    }

    /**
     * Get definition instance or value.
     * @return mixed
     */
    public function resolve(): mixed
    {
        if($this->singleton)
            return $this->instance ?? $this->instance = $this->makeInstance();
        return $this->makeInstance();
    }

    /**
     * @return mixed
     */
    protected function makeInstance(): mixed
    {
        $parameters = [];
        foreach ($this->parameters as $index => $parameter)
            $parameters[$index] = $this->makeParameter($parameter);

        $instance = new $this->concrete(...$parameters);

        foreach ($this->properties as $name => $value)
            $instance->{$name} = $this->makeParameter($value);

        foreach ($this->methods as $method)
        {
            $parameters = [];
            foreach ($method['parameters'] as $index => $parameter)
                $parameters[$index] = $this->makeParameter($parameter);
            $instance->{$method['name']}(...$parameters);
        }
        return $instance;
    }

    /**
     * @param mixed $parameter
     * @return mixed
     */
    protected function makeParameter(mixed $parameter): mixed
    {
        if(!is_string($parameter))
            return $parameter;

        try
        {
            return $this->container->get($parameter);
        }
        catch (NotFoundException)
        {
            return $parameter;
        }
    }
}
<?php

namespace MMOPANE\Container;

class Definition
{
    protected Container     $container;
    protected string        $concrete;
    protected array         $parameters     = [];
    protected array         $properties     = [];
    protected array         $methods        = [];
    protected bool          $singleton      = false;
    protected object|null   $instance       = null;

    public function __construct(Container $container, mixed $concrete)
    {
        $this->container    = $container;
        $this->concrete     = $concrete;
    }

    public function setParameters(mixed ...$parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setProperty(string $name, mixed $value): self
    {
        $this->properties[$name] = $value;
        return $this;
    }

    public function callMethod(string $name, mixed ...$parameters): self
    {
        $this->methods[] = [
            'name' => $name,
            'parameters' => $parameters
        ];
        return $this;
    }

    public function setSingleton(bool $singleton): self
    {
        $this->singleton = $singleton;
        return $this;
    }

    public function resolve(): mixed
    {
        if($this->singleton)
            return $this->instance ?? $this->instance = $this->makeInstance();

        return $this->makeInstance();
    }

    protected function makeInstance(): mixed
    {
        $parameters = $this->parameters;

        foreach ($parameters as $index => $parameter)
        {
            if(is_string($parameter) and $this->container->has($parameter))
                $parameters[$index] = $this->container->get($parameter);
        }

        $instance = new $this->concrete(...$parameters);

        foreach ($this->properties as $name => $value)
        {
            if(is_string($value) and $this->container->has($value))
                $instance->{$name} = $this->container->get($value);
            else
                $instance->{$name} = $value;
        }

        foreach ($this->methods as $method)
        {
            $parameters = $method['parameters'];

            foreach ($parameters as $index => $parameter)
            {
                if(is_string($parameter) and $this->container->has($parameter))
                    $parameters[$index] = $this->container->get($parameter);
            }

            $instance->{$method['name']}(...$parameters);
        }

        return $instance;
    }
}
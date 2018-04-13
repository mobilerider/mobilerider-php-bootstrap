<?php

namespace Mr\Bootstrap;


use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;

class Container
{
    protected $definitions;
    protected $services = [];

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
        $this->services['container'] = $this;
    }

    public function get($name, $args = [])
    {
        if (!isset($this->services[$name])) {
            $definition = $this->definitions[$name];

            $service = $this->create($definition, $args);

            if ($definition['single']) {
                $this->services[$name] = $service;
            }

            return $service;
        }

        return $this->services[$name];
    }

    protected function create(array $definition, array $args)
    {
        $arguments = [];

        foreach ($definition['arguments'] as $key => $argument) {
            if (array_key_exists($key, $args)) {
                $arguments[] = $args[$key];
                continue;
            }

            if (is_string($argument)) {
                $arguments[] = $this->get($argument);
                continue;
            }

            $arguments[] = $argument;
        }

        $service = Factory::create($definition['class'], $arguments);

        // Tried class_uses but it does not seem to retrieve traits
        // used by parent classes
        if ($service instanceof ContainerAccessorInterface) {
            $service->setContainer($this);
        }

        return $service;
    }
}

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

    /**
     * Return TRUE if service instance already exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * Return service instance if exists or creates it otherwise
     *
     * @param $name
     * @param array $args
     * @return mixed|object
     */
    public function get($name, $args = [])
    {
        if (! $this->has($name)) {
            if (! isset($this->definitions[$name])) {
                throw new \Exception('Container definition not found for ' . $name);
            }

            $definition = $this->definitions[$name];            

            if (isset($definition['instance'])) {
                $service = $definition['instance'];
                $single = true;
            } else {
                $service = $this->create($definition, $args);
                $single = isset($definition['single']) && $definition['single'];
            }

            if ($single) {
                $this->services[$name] = $service;
            }

            return $service;
        }

        return $this->services[$name];
    }

    /**
     * Create service instance with given arguments
     *
     * @param array $definition
     * @param array $args
     * @return object
     */
    protected function create(array $definition, array $args)
    {
        if (isset($definition['arguments'])) {
            $arguments = [];

            foreach ($definition['arguments'] as $key => $argument) {
                if (array_key_exists($key, $args)) {
                    $arguments[] = $args[$key];
                    continue;
                }

                if ($argument instanceof ContainerServiceArg) {
                    $arguments[] = $this->get($argument->name);
                    continue;
                }

                $arguments[] = $argument;
            }
        } else {
            $arguments = $args;
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

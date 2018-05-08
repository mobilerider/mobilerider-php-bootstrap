<?php

namespace Mr\Bootstrap\Traits;

use Mr\Bootstrap\Container;

trait ContainerAccessor
{
    /**
     * @var Container
     */
    protected $container;

    protected function _has($name)
    {
        return $this->container->has($name);
    }

    protected function _get($name, array $args = [])
    {
        return $this->container->get($name, $args);
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
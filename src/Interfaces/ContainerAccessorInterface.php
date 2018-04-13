<?php

namespace Mr\Bootstrap\Interfaces;


use Mr\Bootstrap\Container;

interface ContainerAccessorInterface
{
    public function setContainer(Container $container);
    public function getContainer();
}
<?php

namespace Mr\Bootstrap\Service;


use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Traits\ContainerAccessor;

abstract class BaseHttpService implements ContainerAccessorInterface
{
    use ContainerAccessor;

    /**
     * @var HttpDataClientInterface
     */
    protected $client;
    protected $options;

    /**
     * Service constructor.
     * @param HttpDataClientInterface $client
     * @param array $options
     */
    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * @return HttpDataClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
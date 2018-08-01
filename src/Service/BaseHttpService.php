<?php

namespace Mr\Bootstrap\Service;


use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Bootstrap\Traits\ContainerAccessor;

abstract class BaseHttpService implements ContainerAccessorInterface
{
    use ContainerAccessor;

    /**
     * @var HttpDataClientInterface
     */
    protected $client;
    protected $options;
    protected $version;

    /**
     * Service constructor.
     * @param HttpDataClientInterface $client
     * @param array $options
     */
    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->options = $options;
        $this->version = $options['version'] ?? '';
    }

    /**
     * @return HttpDataClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns repository related with given name, usually repository class
     *
     * @param $name
     * @return BaseRepository
     */
    public function getRepository($name)
    {
        return $this->_get($name, [
            'client' => $this->client
        ]);
    }
}
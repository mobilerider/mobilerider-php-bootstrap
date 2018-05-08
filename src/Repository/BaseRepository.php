<?php

namespace Mr\Bootstrap\Repository;

use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Model\BaseModel;
use Mr\Bootstrap\Traits\ContainerAccessor;

abstract class BaseRepository implements ContainerAccessorInterface
{
    use ContainerAccessor;

    protected $client;
    protected $baseUrl;
    protected $apiVersion;

    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->baseUrl = $options['base_url'] ?? '';
        $this->apiVersion = $options['api_version'] ?? '';
    }

    /**
     * @return mixed
     */
    public abstract function getModelClass();

    public function getModel()
    {
        $model = static::getModelClass();

        return $model::getModel();
    }

    public function getResource()
    {
        $model = static::getModelClass();

        return $model::getResource();
    }

    protected function getResourcePath()
    {
        $path = plural($this->getResource());

        return $this->apiVersion ? $this->apiVersion . '/' . $path : $path;
    }

    public function getUri($id = null)
    {
        $parts = [
            $this->baseUrl,
            $this->getResourcePath(),
            $id
        ];

        return implode('/', array_filter($parts, 'strlen'));
    }

    public function parseOne(array $data)
    {
        return $data[$this->getResource()];
    }

    public function parseMany(array $data)
    {
        $data = $data[$this->getResource()];

        if (!is_numeric(key($data))) {
            return [$data];
        }

        return $data;
    }

    public function create($data = [])
    {
        return $this->_get($this->getModel(), [
            'repository' => $this, // Important to pass current repository and avoid container creating new one
            'data' => $data
        ]);
    }

    public function createFromXml($stream)
    {
        $data = xml2arr($stream);

        unset($data['@attributes']['href']);
        unset($data['id']);

        return $this->create($data);
    }

    public function execute($uri, array $data = [])
    {
        return $this->client->postData($this->getUri() . '/' . $uri, $data);
    }

    public function getData($id, $modifiers = [])
    {
        return $this->client->getData($this->getUri($id), $modifiers);
    }

    public function get($id)
    {
        return $this->create($this->getData($id));
    }

    /**
     * @param array $data
     * @return array
     */
    public function buildModels(array $data)
    {
        $models = [];

        $data = $this->parseAll($data);

        foreach ($data as $item) {
            $models[] = $this->create($item);
        }

        return $models;
    }

    public function all($filters = [])
    {
        $data = $this->client->getData($this->getUri(), $filters);

        if (isset($data['empty'])) {
            return [];
        }

        return $this->buildModels($data);
    }

    public function persist(BaseModel $model)
    {
        if ($model->isNew()) {
            $data = $this->client->postData($this->getUri(), $model->toArray());
        } else {
            $data = $this->client->putData($model->getUri(), $model->toArray());
        }


        $model->fill($data, true);
    }

    public function delete(BaseModel $model)
    {
        if (!$model->isNew()) {
            $this->client->delete($model->getUri());
        }
    }
}

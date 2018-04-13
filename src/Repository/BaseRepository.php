<?php

namespace Mr\Bootstrap\Repository;

use Mr\Bootstrap\Interfaces\HttpClientInterface;
use Mr\Bootstrap\Container;
use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Model\BaseModel;
use Mr\Bootstrap\Traits\ContainerAccessor;

abstract class BaseRepository implements ContainerAccessorInterface
{
    use ContainerAccessor;

    protected $client;

    public function __construct(HttpClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->baseUrl = $options['base_url'] ?? $this->getBaseUrl();
    }

    public abstract function getBaseUrl();
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

    protected function buildUri($uri)
    {
        return "{$this->baseUrl}/$uri";
    }

    public function getUri()
    {
        return $this->buildUri($this->getResource() . 's');
    }

    public function parseOne(array $data)
    {
        return $data[$this->getResource()];
    }

    public function parseAll(array $data)
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
        return $this->client->getData($this->getUri() . '/' . $id, $modifiers);
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
            $data = $this->client->postData(
                $this->getUri(), [
                    $this->getResource() => $model->toArray()
                ]
            );
        } else {
            $data = $this->client->putData($this->buildUri($model->getUri()));
        }

        // TODO: maybe we should clear all data
        $model->fill($data);
    }

    public function encode(BaseModel $model, $pretty = false)
    {
        return $this->client->encode([$this->getResource() => $model->toArray()], $pretty);
    }

    public function delete(BaseModel $model)
    {
        if (!$model->isNew()) {
            $this->client->delete($this->buildUri($model->getUri()));
        }
    }
}

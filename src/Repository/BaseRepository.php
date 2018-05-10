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
    protected $apiVersion;

    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->apiVersion = $options['api_version'] ?? '';
    }

    /**
     * @return mixed
     */
    public abstract function getModelClass();

    public function getResource()
    {
        $model = static::getModelClass();

        return $model::getResource();
    }

    protected function getResourcePath()
    {
        $path = plural($this->getResource());

        return $this->apiVersion ? "{$this->apiVersion}/$path" : $path;
    }

    public function getUri($id = null)
    {
        return $id ? "{$this->getResourcePath()}/$id" : $this->getResourcePath();
    }

    public function parseOne(array $data, array &$metadata = [])
    {
        if (isset($data['metadata'])) {
            $metadata = (array) $data['metadata'];
        }

        if (! isset($data['data'])) {
            return null;
        }

        return $data['data'];
    }

    public function parseMany(array $data, array &$metadata = [])
    {
        if (isset($data['metadata'])) {
            $metadata = (array) $data['metadata'];
        }

        if (! isset($data['data'])) {
            return [];
        }

        return $data['data'];
    }

    public function create($data = [])
    {
        return $this->_get($this->getModelClass(), [
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
        return $this->client->postData($uri, $data);
    }

    /**
     * @param array $items
     * @return array
     */
    public function buildModels(array $items)
    {
        $models = [];

        foreach ($items as $item) {
            $models[] = $this->create($item);
        }

        return $models;
    }

    public function get($id, $modifiers = [])
    {
        $data = $this->client->getData($this->getUri($id), $modifiers);
        $data = $this->parseOne($data);

        return $data ? $this->create($data) : $data;
    }

    public function one($filters = [])
    {
        $filters['limit'] = 1;

        return $this->all($filters);
    }

    public function all($filters = [])
    {
        $data = $this->client->getData($this->getUri(), $filters);
        $data = $this->parseMany($data);

        return $data ? $this->buildModels($data) : $data;
    }

    public function persist(BaseModel $model)
    {
        if ($model->isNew()) {
            $data = $this->client->postData($this->getUri(), $model->toArray());
        } else {
            $data = $this->client->putData($this->getUri($model->id), $model->toArray());
        }

        $data = $this->parseOne($data);

        $model->fill($data, true);
    }

    public function delete(BaseModel $model)
    {
        if (!$model->isNew()) {
            $this->client->delete($model->getUri());
        }
    }
}

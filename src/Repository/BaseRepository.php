<?php

namespace Mr\Bootstrap\Repository;

use Mr\Bootstrap\Http\Filtering\PrettusL5QueryBuilder;
use Mr\Bootstrap\Interfaces\HttpDataClientInterface;
use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Interfaces\QueryBuilderInterface;
use Mr\Bootstrap\Model\BaseModel;
use Mr\Bootstrap\Traits\ContainerAccessor;

abstract class BaseRepository implements ContainerAccessorInterface
{
    use ContainerAccessor;

    protected $client;
    protected $apiVersion;
    protected $queryBuilderClass;

    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;
        $this->apiVersion = $options['api_version'] ?? '';
        $this->queryBuilderClass = PrettusL5QueryBuilder::class;
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

    /**
     * @param array|QueryBuilderInterface $filters
     * @return QueryBuilderInterface
     */
    protected function parseFilters($filters)
    {
        if ($filters instanceof QueryBuilderInterface) {
            return $filters;
        }

        if (is_array($filters)) {
            $class = $this->queryBuilderClass;
            return new $class($filters);
        }

        throw new \RuntimeException('Invalid filters');
    }

    public function get($id, $modifiers = [])
    {
        $data = $this->client->getData(
            $this->getUri($id), $this->parseFilters($modifiers)->toArray()
        );

        $data = $this->parseOne($data);

        return $data ? $this->create($data) : $data;
    }

    public function one($filters = [])
    {
        $qb = $this->parseFilters($filters);

        return $this->all($qb->limit(1));
    }

    /**
     * @param array|QueryBuilderInterface $filters
     * @return array|mixed
     */
    public function all($filters = [])
    {
        $data = $this->client->getData(
            $this->getUri(), $this->parseFilters($filters)->toArray()
        );

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

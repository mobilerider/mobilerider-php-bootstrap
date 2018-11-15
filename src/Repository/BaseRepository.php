<?php

namespace Mr\Bootstrap\Repository;

use Mr\Bootstrap\Factory;
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
    protected $queryBuilderClass;

    public function __construct(HttpDataClientInterface $client, array $options = [])
    {
        $this->client = $client;

        $this->queryBuilderClass = $options['queryBuilderClass'] 
            ?? PrettusL5QueryBuilder::class;
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
        return mr_plural($this->getResource());
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
        return $this->_get(
            $this->getModelClass(), [
                // Important to pass current repository
                // and avoid container creating new one
                'repository' => $this,
                'data' => $data
            ]
        );
    }

    public function createFromXml($stream)
    {
        $data = mr_xml2arr($stream);

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
    public function resolveFilterQuery($filters)
    {
        if (! $filters) {
            return Factory::create($class = $this->queryBuilderClass);
        }

        if ($filters instanceof QueryBuilderInterface) {
            return $filters;
        }

        if (is_array($filters)) {
            return Factory::create($class = $this->queryBuilderClass, [$filters]);
        }

        throw new \RuntimeException('Invalid filters');
    }

    /**
     * Retrieve one item by given id 
     * from remote api
     *
     * @param mixed $id
     * @param array $modifiers
     * 
     * @return BaseModel|array|null
     */
    public function get($id, $modifiers = [], $asArray = false)
    {
        $data = $this->client->getData(
            $this->getUri($id), $this->resolveFilterQuery($modifiers)->toArray()
        );

        $data = $this->parseOne($data);

        if (! $data) {
            return null;
        }

        return $asArray ? $data : $this->create($data);
    }

    /**
     * Retrieve one item by given filters
     * from remote api
     * 
     * @param array $filters
     * 
     * @return BaseModel|null
     */
    public function one($filters = [], $asArray = false)
    {
        $qb = $this->resolveFilterQuery($filters);

        $result = $this->all($qb->limit(1), $asArray);

        return $result ? $result[0] : null;
    }

    /**
     * Retrieve all items by given filters
     * from remote api
     * 
     * @param array|QueryBuilderInterface $filters
     * 
     * @return array|mixed
     */
    public function all($filters = [], $asArray = false)
    {
        $data = $this->client->getData(
            $this->getUri(), $this->resolveFilterQuery($filters)->toArray()
        );

        $data = $this->parseMany($data);

        if (! $data) {
            return [];
        }

        return $asArray ? $data : $this->buildModels($data);
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

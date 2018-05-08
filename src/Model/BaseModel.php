<?php

namespace Mr\Bootstrap\Model;


use Mr\Bootstrap\Interfaces\ContainerAccessorInterface;
use Mr\Bootstrap\Interfaces\DataEntityInterface;
use Mr\Bootstrap\Repository\BaseRepository;
use Mr\Bootstrap\Traits\ContainerAccessor;
use Mr\Bootstrap\Traits\GetSet;

/**
 * @property mixed|null id
 */
abstract class BaseModel implements ContainerAccessorInterface, DataEntityInterface
{
    use GetSet {
        fill as protected traitFill;
        toArray as protected traitToArray;
    }
    use ContainerAccessor;

    protected $repository;
    protected $fetched = false;

    public function __construct(BaseRepository $repository, array $data = [])
    {
        $this->repository = $repository;
        $this->fill($data, true);
    }

    public abstract static function getModel();

    public static function getResource()
    {
        return static::getModel();
    }

    public function isNew()
    {
        return !$this->id;
    }

    protected function ensureNotNew()
    {
        if ($this->isNew()) {
            throw new \RuntimeException('Save this model first');
        }
    }

    protected function ensureField($field, $default = null)
    {
        if (isset($this[$field])) {
            return;
        }

        if (! $this->isNew()) {
            $this->fetch(true);
        }

        if (! isset($this[$field])) {
            $this->$field = $default;
        }
    }

    public function getUri()
    {
        return $this->repository->getUri($this->id);
    }

    public function fill(array $data, $clear = false)
    {
        $this->traitFill($data, $clear);
    }

    /**
     * Converts any values stored separately inside the object
     * into data ready to be sent to the server
     *
     * @return $this
     */
    public function prepare()
    {
        return $this;
    }

    public function toArray()
    {
        $this->prepare();

        return $this->traitToArray();
    }

    public function fetch($force = false, $modifiers = [])
    {
        if (!$this->id) {
            throw new \RuntimeException('Id required');
        }

        if (!$force && $this->fetched) {
            return false;
        }

        $this->data = $this->repository->getData($this->id, $modifiers);

        return true;
    }

    public function save()
    {
        $this->repository->persist($this);
    }

    public function delete()
    {
        $this->repository->delete($this);
    }
}

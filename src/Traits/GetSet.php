<?php

namespace Mr\Bootstrap\Traits;


use Mr\Bootstrap\Interfaces\DataEntityInterface;

trait GetSet
{
    protected $data = [];
    protected $isModified;

    /**
     * @param string|array $name
     * @return bool
     */
    public function has($name)
    {
        if (! is_array($name)) {
            $parts = explode('.', $name);
        }

        if (count($parts) > 1) {
            $a = &$this->data;
            $i = 0;

            while ($i < count($parts)) {
                $key = $parts[$i];

                if (! isset($a[$key])) {
                    return false;
                }

                $a = &$a[$key];

                $i++;
            }

            return true;
        }

        return isset($this->data[$name]);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param string|array $name
     * @return array|mixed|null
     */
    public function get($name)
    {
        if (! is_array($name)) {
            $parts = explode('.', $name);
        }

        if (count($parts) > 1) {
            $a = &$this->data;
            $i = 0;

            while ($i < count($parts)) {
                if (! is_array($a)) {
                    return null;
                }

                $key = $parts[$i];

                if (! isset($a[$key])) {
                    return null;
                }

                $a = &$a[$key];

                $i++;
            }

            return $a;
        }

        if (!isset($this->$name)) {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * <b>Magic method</b>. Returns value of specified property
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string|array $name
     * @param $value
     * @return bool
     */
    public function set($name, $value)
    {
        if (isset(static::$__readOnlyFields) && in_array($name, static::$__readOnlyFields)) {
            throw new \RuntimeException('Read only field: ' . $name);
        }

        $oldValue = $this->get($name);

        if ($oldValue === $value) {
            return false;
        }

        if (! is_array($name)) {
            $parts = explode('.', $name);
        }

        if (count($parts) > 1) {
            $key = $name = $parts[0];
            $rootValue = $this->has($key) ? $this->data[$key] : [];

            $a = &$rootValue;
            $i = 1;

            while($i < count($parts) - 1) {
                $key = $parts[$i];

                if (! array_key_exists($key, $a)) {
                    $a[$key] = [];
                }

                $a = &$a[$key];

                $i++;
            };

            $key = $parts[$i];
            $a[$key] = $value;
            $value = $rootValue;
        }

        $this->data[$name] = $value;

        $this->isModified = true;
    }

    /**
     * <b>Magic method</b>. Sets value of a dynamic property
     *
     * @param string $name property name
     * @param mixed  $value new value
     *
     * @return mixed param value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string|array $name1
     * @param string|array $name2
     * @return string
     */
    public function mergeNames($name1, $name2)
    {
        if (! $name2) {
            return is_array($name1) ? implode('.', $name1) : $name1;
        }

        return implode('.', array_merge((array) $name1, (array) $name2));
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    public function fill(array $data, $clear = false)
    {
        if (! $data) {
            return;
        }

        if ($clear) {
            $this->data = $data;
            return;
        }

        $this->data = array_merge($this->data, $data);
    }

    public function isModified()
    {
        return $this->isModified;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function getArrayFrom(DataEntityInterface $obj)
    {
        return $obj->toArray();
    }

    public function loadXml($xml)
    {
        $this->fill(mr_xml2arr($xml));
    }

    public function asXml($pretty = false, $root = 'root')
    {
        if (! $this->data) {
            return '';
        }

        $xml = mr_arr2xml([$root => $this->toArray()]);

        return $pretty ? mr_prettify_xml($xml) : $xml->asXML();
    }

    public function __dd()
    {
        mr_dd($this->toArray());
    }
}

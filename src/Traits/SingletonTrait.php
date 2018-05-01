<?php

namespace Mr\Bootstrap\Traits;

trait SingletonTrait
{
    private static $__singletonInstance;

    public static function getInstance()
    {
        if (! static::$__singletonInstance) {
            // Reflection is supposed to be evil but since
            // this is done only once for this class being a
            // singleton is OK to use it here
            $class = new \ReflectionClass(static::class);
            static::$__singletonInstance = $class->newInstanceArgs(func_get_args());
        }

        return static::$__singletonInstance;
    }

    public static function isInstantiated()
    {
        return (bool) static::$__singletonInstance;
    }
}

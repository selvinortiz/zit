<?php

namespace SelvinOrtiz\Zit;

/**
 * Tiny dependency injection library for PHP
 *
 * Class Zit
 *
 * @author    Selvin Ortiz - https://selvinortiz.com
 * @package   SelvinOrtiz\Zit
 * @version   1.0.1
 * @category  DI, IoC (PHP)
 * @copyright 2014-2016 Selvin Ortiz
 */

class Zit implements IZit
{
    /**
     * @var static
     */
    protected static $instances;

    protected $services = array();
    protected $callables = array();

    /**
     * Prevents direct instantiation
     */
    protected function __construct()
    {
    }

    /**
     * Prevents cloning
     */
    protected function __clone()
    {
    }

    /**
     * Instantiates Zit or the extending container
     *
     * @deprecated Deprecated since 1.0.0
     * @see        make()
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::make();
    }

    /**
     * Returns an instance of Zit or an extending class
     *
     * @return static
     */
    public static function make()
    {
        $name = get_called_class();

        if (!isset(static::$instances[$name])) {

            $instance = new $name();

            static::$instances[$name] = method_exists($instance, 'boot') ? $instance->boot() : $instance;
        }

        return static::$instances[$name];
    }

    /**
     * Binds the service generator and resolves it by its id
     *
     * @param string   $id               The service generator id
     * @param callable $serviceGenerator The service generator closure/function
     */
    public function bind($id, callable $serviceGenerator)
    {
        $this->callables[(string)$id] = function ($zit) use ($serviceGenerator) {
            static $object;

            if (null === $object) {
                $object = $serviceGenerator($zit);
            }

            return $object;
        };
    }

    /**
     * Stashes away a service instance and resolves it by its id
     *
     * @param    string $id      The service instance id
     * @param    object $service The service instance
     */
    public function stash($id, $service)
    {
        if (is_object($service)) {
            $this->services[$id] = $service;
        }
    }

    /**
     * Binds the callable function and executes it by its id
     *
     * @param string   $id       The callable function id
     * @param callable $callback The callable function
     */
    public function extend($id, callable $callback)
    {
        if (is_callable($callback) || method_exists($callback, '__invoke')) {
            $this->callables[$id] = $callback;
        }
    }

    /**
     * Pops a dependency out of the container
     *
     * @param string $id
     * @param array  $args
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function pop($id, $args = array())
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        if (array_key_exists($id, $this->callables)) {
            $callback = $this->callables[$id];

            return call_user_func_array($callback, array_merge(array($this), $args));
        }

        throw new \Exception("The dependency with id of ({$id}) is missing.");
    }

    /**
     * @param string $id
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->pop($id);
    }

    /**
     * @param string $id
     * @param array  $args
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($id, $args = array())
    {
        return $this->pop($id, $args);
    }

    /**
     * @param string $id
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($id, $args = array())
    {
        return static::getInstance()->pop($id, $args);
    }
}

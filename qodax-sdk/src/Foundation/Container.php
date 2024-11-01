<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Exceptions\ContainerException;
class Container
{
    /**
     * @var Container
     */
    private static $instance;
    /**
     * List of container bindings.
     */
    private array $bindings = [];
    /**
     * List of container singleton instances.
     */
    private array $singletons = [];
    private array $singletonInstances = [];
    /**
     * Container constructor.
     * @param array $bindings
     */
    public function __construct($bindings = [])
    {
        $this->bindings = $bindings;
    }
    public static function instance($bindings = [])
    {
        if (null === self::$instance) {
            self::$instance = new self($bindings);
        }
        return self::$instance;
    }
    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->{$key};
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->{$key} = $value;
    }
    /**
     * PSR 11 implementation
     */
    public function has(string $id) : bool
    {
        return isset($this->bindings[$id]);
    }
    /**
     * @param string $id
     * @return mixed
     *
     * @throws ContainerException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            return $this->resolve($this->bindings[$id]);
        } else {
            // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
            throw new ContainerException('Dependency ' . $id . ' not found.');
        }
    }
    /**
     * Bind concrete implementation.
     *
     * @param string $abstract
     * @param callable $concrete
     *
     * @throws ContainerException
     */
    public function bind($abstract, $concrete)
    {
        if (\is_callable($concrete)) {
            $this->bindings[$abstract] = $concrete;
        } else {
            throw new ContainerException('Bind method expects second parameter to be a callable');
        }
    }
    /**
     * @throws ContainerException
     */
    public function singleton(string $abstract, callable $resolver) : void
    {
        $this->bind('singleton.' . $abstract, $resolver);
        $this->singletons[] = $abstract;
    }
    /**
     * Make concrete instance. Trying to create current type instance if no exists.
     *
     * @param string $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        try {
            if (\in_array($abstract, $this->singletons, \true)) {
                return $this->resolveSingleton($abstract);
            }
            return $this->get($abstract);
        } catch (ContainerException $e) {
            return $this->resolveWithWiring($abstract);
        }
    }
    /**
     * @param string $dependency
     * @return mixed
     */
    private function resolve($dependency)
    {
        return \call_user_func($dependency, $this);
    }
    /**
     * @param string $abstract
     * @return mixed
     */
    private function resolveSingleton(string $abstract)
    {
        if (!isset($this->singletonInstances[$abstract])) {
            $this->singletonInstances[$abstract] = $this->make('singleton.' . $abstract);
        }
        return $this->singletonInstances[$abstract];
    }
    /**
     * @param string $abstract
     * @return mixed
     */
    private function resolveWithWiring($abstract)
    {
        $reflectionClass = new \ReflectionClass($abstract);
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $abstract();
        }
        $params = $constructor->getParameters();
        $args = [];
        foreach ($params as $param) {
            $paramType = $param->getType();
            if (\version_compare(\PHP_VERSION, '7.1.0') >= 0) {
                if ($paramType instanceof \ReflectionNamedType) {
                    $args[] = $this->make($paramType->getName());
                }
            } else {
                $args[] = $this->make((string) $paramType);
            }
        }
        return $reflectionClass->newInstanceArgs($args);
    }
}

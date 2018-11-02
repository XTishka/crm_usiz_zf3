<?php

namespace Application\Domain;

use ReflectionClass;
use ReflectionProperty;
use Zend\Filter;

abstract class AbstractValueObject implements ValueObjectInterface {

    /**
     * @param mixed $data
     * @param bool $underscoreSeparatedKeys
     * @return object
     */
    public static function factory($data = null, $underscoreSeparatedKeys = true) {
        $calledClass = get_called_class();
        $reflectionClass = new ReflectionClass($calledClass);
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_array($data)) {
            $constructor = $reflectionClass->getConstructor();
            $parameters = $constructor->getParameters();
            $chain = new Filter\FilterChain();
            if (true == $underscoreSeparatedKeys) {
                $chain->attach(new Filter\Word\CamelCaseToUnderscore());
                $chain->attach(new Filter\StringToLower());
            }
            $arguments = [];
            foreach ($parameters as $number => $parameter) {
                $parameterName = $chain->filter($parameter->getName());
                if (array_key_exists($parameterName, $data)) {
                    $arguments[$number] = $data[$parameterName];
                }
            }
            $instance = $reflectionClass->newInstanceArgs($arguments);
        } else {
            $instance = $reflectionClass->newInstanceWithoutConstructor();
        }
        return $instance;
    }

    /**
     * @param bool $underscoreSeparatedKeys
     * @return array
     */
    public function toArray($underscoreSeparatedKeys = true) {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PROTECTED);
        $chain = new Filter\FilterChain();
        if (true == $underscoreSeparatedKeys) {
            $chain->attach(new Filter\Word\CamelCaseToUnderscore());
            $chain->attach(new Filter\StringToLower());
        }
        $array = [];
        foreach ($properties as $reflectionProperty) {
            $method = 'get' . ucfirst($reflectionProperty->getName());
            if ($reflectionClass->hasMethod($method) && !$reflectionProperty->isStatic()) {
                $key = $chain->filter($reflectionProperty->getName());
                $array[$key] = $this->{$method}();
            }
        }
        return $array;
    }

    /**
     * @param bool $underscoreSeparatedKeys
     * @return string
     */
    public function toJson($underscoreSeparatedKeys = true) {
        $data = $this->toArray($underscoreSeparatedKeys);
        return json_encode($data);
    }

}
<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;

/**
 * Class AutoMapper
 *
 * @package AutoMapperPlus
 */
class AutoMapper implements AutoMapperInterface
{
    /**
     * @var AutoMapperConfigInterface
     */
    private $autoMapperConfig;

    /**
     * AutoMapper constructor.
     *
     * @param AutoMapperConfigInterface $autoMapperConfig
     */
    function __construct(AutoMapperConfigInterface $autoMapperConfig = null)
    {
        $this->autoMapperConfig = $autoMapperConfig ?: new AutoMapperConfig();
    }

    /**
     * @inheritdoc
     */
    public static function initialize(callable $configurator): AutoMapperInterface
    {
        $mapper = new static;
        $configurator($mapper->autoMapperConfig);

        return $mapper;
    }

    /**
     * @inheritdoc
     */
    public function map($from, string $to)
    {
        $toReflectionClass = new \ReflectionClass($to);
        $toObject = $toReflectionClass->newInstanceWithoutConstructor();

        return $this->mapToObject($from, $toObject);
    }

    /**
     * @inheritdoc
     */
    public function mapToObject($from, $to)
    {
        $fromReflectionClass = new \ReflectionClass($from);
        $toReflectionClass = new \ReflectionClass($to);
        $configExists = $this->autoMapperConfig->hasMappingFor(
            $fromReflectionClass->getName(),
            $toReflectionClass->getName()
        );
        if (!$configExists) {
            throw UnregisteredMappingException::fromClasses(
                $fromReflectionClass->getName(),
                $toReflectionClass->getName()
            );
        }

        $mapping = $this->autoMapperConfig->getMappingFor(
            $fromReflectionClass->getName(),
            $toReflectionClass->getName()
        );

        foreach ($toReflectionClass->getProperties() as $destinationProperty) {
            $mappingOperation = $mapping->getMappingCallbackFor($destinationProperty->getName());
            $mappingOperation(
                $from,
                $to,
                $destinationProperty->getName(),
                $this->autoMapperConfig
            );
        }

        return $to;
    }
}

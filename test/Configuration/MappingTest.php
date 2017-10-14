<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameResolver\IdentityNameResolver;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\Source;

/**
 * Class MappingTest
 *
 * @package AutoMapperPlus\Configuration
 */
class MappingTest extends TestCase
{
    public function testItCanAddAMappingCallback()
    {
        $mapping = new Mapping(
            Source::class,
            Destination::class,
            ['defaultOperation' => Operation::getProperty(new IdentityNameResolver())]
        );
        $callable = function() {};
        $mapping->forMember('name', $callable);

        $this->assertEquals(Operation::mapFrom($callable), $mapping->getMappingCallbackFor('name'));
    }

    public function testItCanOverrideTheDefaultOperation()
    {
        $newDefault = Operation::ignore();
        $mapping = new Mapping(
            Source::class,
            Destination::class,
            ['defaultOperation' => $newDefault]
        );

        $this->assertEquals($newDefault, $mapping->getMappingCallbackFor('name'));
    }
}

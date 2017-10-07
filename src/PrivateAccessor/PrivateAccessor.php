<?php

namespace AutoMapperPlus\PrivateAccessor;

/**
 * Class PrivateAccessor.
 *
 * This implementation is taken from
 * https://gist.github.com/githubjeka/153e5a0f6d15cf20512e.
 *
 * @package AutoMapperPlus\PrivateAccessor
 */
class PrivateAccessor implements PrivateAccessorInterface
{
    /**
     * @inheritdoc
     */
    public static function getPrivate($object, string $attribute)
    {
        $getter = function() use ($attribute) {
            return $this->$attribute;
        };
        $boundGetter = \Closure::bind($getter, $object, get_class($object));

        return $boundGetter();
    }

    /**
     * @inheritdoc
     */
    public static function setPrivate($object, string $attribute, $value): void
    {
        $setter = function($value) use ($attribute) {
            $this->$attribute = $value;
        };
        $boundSetter = \Closure::bind($setter, $object, get_class($object));
        $boundSetter($value);
    }
}

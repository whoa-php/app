<?php

declare(strict_types=1);

namespace App\Json\Schemas;

use Whoa\Common\Reflection\ClassIsTrait;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Flute\Schema\Schema;

/**
 * @package App
 */
abstract class BaseSchema extends Schema
{
    use ClassIsTrait;

    /** @var string Attribute name */
    public const ATTR_UUID = 'uuid';

    /** @var string Attribute name */
    public const ATTR_CREATED_AT = 'created-at';

    /** @var string Attribute name */
    public const ATTR_UPDATED_AT = 'updated-at';

    /** @var string Attribute name */
    public const ATTR_DELETED_AT = 'deleted-at';

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        assert(get_class($resource) === static::MODEL);
        assert($this->classImplements(static::MODEL, ModelInterface::class) === true);

        /** @var ModelInterface $modelClass */
        $modelClass = static::MODEL;

        $pkName = $modelClass::getPrimaryKeyName();

        return (string)$resource->{$pkName};
    }
}

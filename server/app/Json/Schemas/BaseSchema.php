<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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

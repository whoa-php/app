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

namespace App\Json\Exceptions;

use Whoa\Application\Exceptions\AuthorizationException;
use Whoa\Flute\Contracts\Exceptions\JsonApiThrowableConverterInterface;
use Whoa\Passport\Exceptions\AuthenticationException;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Throwable;

/**
 * @package App
 */
class ThrowableConverter implements JsonApiThrowableConverterInterface
{
    /**
     * @inheritdoc
     * This code provides an ability to transform various exceptions in API (application specific,
     * authorization, 3rd party, etc.) and convert it to JSON API error.
     */
    public static function convert(Throwable $throwable): ?JsonApiException
    {
        $converted = null;

        if ($throwable instanceof AuthorizationException) {
            $httpCode = 403;
            $action = $throwable->getAction();
            $errors = static::createErrorWith(
                'Unauthorized',
                "You are not authorized for action `$action`.",
                $httpCode
            );
            $converted = new JsonApiException($errors, $httpCode, $throwable);
        } elseif ($throwable instanceof AuthenticationException) {
            $httpCode = 401;
            $errors = static::createErrorWith('Authentication failed', 'Authentication failed', $httpCode);
            $converted = new JsonApiException($errors, $httpCode, $throwable);
        }

        return $converted;
    }

    /**
     * @param string $title
     * @param string $detail
     * @param int $httpCode
     * @return ErrorCollection
     */
    private static function createErrorWith(string $title, string $detail, int $httpCode): ErrorCollection
    {
        return (new ErrorCollection())->addDataError($title, $detail, null, null, null, null, (string)$httpCode);
    }
}

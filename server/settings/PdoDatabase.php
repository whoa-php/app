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

namespace Settings;

//use Dotenv\Dotenv;
//use Whoa\Application\Packages\PDO\PdoSettings;
//
///**
// * @package Settings
// */
//class PdoDatabase extends PdoSettings
//{
//    /**
//     * @inheritdoc
//     */
//    protected function getSettings(): array
//    {
//        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();
//
//        return [
//
//                static::KEY_USER_NAME         => getenv('PDO_USER_NAME'),
//                static::KEY_PASSWORD          => getenv('PDO_USER_PASSWORD'),
//                static::KEY_CONNECTION_STRING => getenv('PDO_CONNECTION_STRING'),
//
//            ] + parent::getSettings();
//    }
//}

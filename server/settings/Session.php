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

use Whoa\Application\Packages\Session\SessionSettings;

/**
 * @package Settings
 */
class Session extends SessionSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        // For the full list of available options
        // - @see SessionSettings
        // - @link http://php.net/manual/en/session.configuration.php

        return [

                static::KEY_COOKIE_SECURE => '',
                static::KEY_COOKIE_LIFETIME => '0',

            ] + parent::getSettings();
    }
}

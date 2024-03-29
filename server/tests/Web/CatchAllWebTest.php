<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
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

namespace Tests\Web;

use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

/**
 * @package Tests
 */
class CatchAllWebTest extends TestCase
{
    /**
     * @return void
     */
    public function testIndex()
    {
        // execution time measurement example
        $response = $this->measureTime(function (): ResponseInterface {
            return $this->get('/');
        }, $time);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(1.0, $time, 'Our home page has become sloppy.');
    }

    /**
     * @return void
     */
    public function testUnreachable()
    {
        // execution time measurement example
        $response = $this->measureTime(function (): ResponseInterface {
            return $this->get('/unreachable');
        }, $time);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(1.0, $time, 'Our home page has become sloppy.');
    }
}

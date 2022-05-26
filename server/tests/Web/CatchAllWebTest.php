<?php

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

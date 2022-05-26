<?php

declare(strict_types=1);

namespace App;

use Whoa\Contracts\Core\SapiInterface;
use Settings\Application as ApplicationSettings;

/**
 * @package App
 */
class Application extends \Whoa\Application\Packages\Application\Application
{
    /**
     * @inheritdoc
     */
    public function __construct(SapiInterface $sapi = null)
    {
        $settings =
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . '*.php';

        parent::__construct($settings, ApplicationSettings::CACHE_CALLABLE, $sapi);
    }
}

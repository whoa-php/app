<?php

declare(strict_types=1);

namespace App\Web\Controllers;

use Whoa\Common\Reflection\ClassIsTrait;

/**
 * @package App
 */
abstract class BaseController
{
    use ClassIsTrait;
    use ControllerTrait;
}

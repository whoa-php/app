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

namespace App\Web\Controllers;

use App\Web\Views;
use Whoa\Contracts\L10n\FormatterFactoryInterface;
use Whoa\Contracts\L10n\FormatterInterface;
use Whoa\Contracts\Templates\TemplatesInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
trait ControllerTrait
{
    /**
     * @param ContainerInterface $container
     * @param int $viewId
     * @param array $parameters
     * @param string $viewsNamespace
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function view(
        ContainerInterface $container,
        int $viewId,
        array $parameters = [],
        string $viewsNamespace = Views::NAMESPACE
    ): string {
        $formatter = static::createFormatter($container, $viewsNamespace);
        $templateName = $formatter->formatMessage((string)$viewId);

        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);

        return $templates->render($templateName, $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param string $namespace
     * @param string|null $locale
     * @return FormatterInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createFormatter(
        ContainerInterface $container,
        string $namespace,
        string $locale = null
    ): FormatterInterface {
        /** @var FormatterFactoryInterface $factory */
        $factory = $container->get(FormatterFactoryInterface::class);
        return $locale === null ?
            $factory->createFormatter($namespace) : $factory->createFormatterForLocale($namespace, $locale);
    }
}

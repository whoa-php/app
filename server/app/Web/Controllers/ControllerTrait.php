<?php

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
     *
     * @return string
     *
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
     *
     * @return FormatterInterface
     *
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

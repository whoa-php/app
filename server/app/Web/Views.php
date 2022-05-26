<?php

declare(strict_types=1);

namespace App\Web;

/**
 * @package App\L10n
 */
interface Views
{
    /**
     * Namespace name for mapping template IDs with localized templates.
     *
     * see `server/resources/messages/{LANG}/App.Views.Pages.php`
     *
     * @var string
     */
    public const NAMESPACE = 'App.Views.Pages';

    /** @var string Template ID. */
    public const CATCH_ALL_PAGE = 0;
}

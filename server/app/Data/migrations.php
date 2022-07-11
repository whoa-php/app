<?php

declare(strict_types=1);

// Place here migrations in an order you want them to be executed.

return [
    \App\Data\Migrations\RolesMigration::class,
    \App\Data\Migrations\UsersMigration::class,
    \Whoa\Passport\Package\PassportMigration::class,
    \App\Data\Migrations\RolesScopesMigration::class,
];

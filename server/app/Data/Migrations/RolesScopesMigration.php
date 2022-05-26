<?php

declare(strict_types=1);

namespace App\Data\Migrations;

use App\Data\Models\OAuthScope;
use App\Data\Models\Role;
use App\Data\Models\RoleOAuthScope as Model;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Data\MigrationInterface;
use Whoa\Data\Migrations\MigrationTrait;
use Whoa\Data\Migrations\RelationshipRestrictions;

/**
 * @package App
 */
class RolesScopesMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     * @throws DBALException
     */
    public function migrate(): void
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->foreignRelationship(Model::FIELD_ID_ROLE, Role::class, RelationshipRestrictions::CASCADE),
            $this->foreignRelationship(Model::FIELD_ID_SCOPE, OAuthScope::class, RelationshipRestrictions::CASCADE),
            $this->timestamps(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(): void
    {
        $this->dropTableIfExists(Model::class);
    }
}

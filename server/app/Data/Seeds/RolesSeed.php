<?php

declare(strict_types=1);

namespace App\Data\Seeds;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Whoa\Contracts\Data\SeedInterface;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Data\Seeds\SeedTrait;

/**
 * @package App
 */
class RolesSeed implements SeedInterface
{
    use SeedTrait;

    /** @var int Field value */
    public const ID_ADMINISTRATORS = 1;
    /** @var string Field value */
    public const NAME_ADMINISTRATORS = 'Administrators';

    /** @var int Field value */
    public const ID_MODERATORS = 2;
    /** @var string Field value */
    public const NAME_MODERATORS = 'Moderators';

    /** @var int Field value */
    public const ID_USERS = 3;
    /** @var string Field value */
    public const NAME_USERS = 'Users';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     * @throws Exception
     */
    public function run(): void
    {
        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_ADMINISTRATORS,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_NAME => self::NAME_ADMINISTRATORS,
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_MODERATORS,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_NAME => self::NAME_MODERATORS,
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_USERS,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_NAME => self::NAME_USERS,
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);
    }
}

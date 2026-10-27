<?php

namespace ChurchCRM\Service;

use ChurchCRM\Utils\LoggerUtils;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;

/**
 * pledge_plg.plg_PerID ships in this fork without its own schema version, so
 * databases already at the release version never run an upgrade script for it.
 * The bootstrapper adds it on demand instead.
 */
class PledgePersonSchema
{
    public static function ensure(): void
    {
        $connection = Propel::getConnection();
        if (self::columnExists($connection)) {
            return;
        }

        try {
            $connection->exec(
                'ALTER TABLE `pledge_plg`'
                . ' ADD COLUMN `plg_PerID` mediumint(9) unsigned DEFAULT NULL AFTER `plg_GroupKey`,'
                . ' ADD INDEX `idx_plg_PerID` (`plg_PerID`)'
            );
            LoggerUtils::getAppLogger()->info('Added pledge_plg.plg_PerID');
        } catch (\PDOException $e) {
            // A concurrent request may have added it first.
            if (!self::columnExists($connection)) {
                throw $e;
            }
        }
    }

    private static function columnExists(ConnectionInterface $connection): bool
    {
        return $connection->query("SHOW COLUMNS FROM `pledge_plg` LIKE 'plg_PerID'")->fetch() !== false;
    }
}

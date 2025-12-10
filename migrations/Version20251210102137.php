<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration from symfony/acl-bundle to native Voters
 * Creates gift_list_permissions table and migrates existing ACL data
 */
final class Version20251210102137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates gift_list_permissions table and migrates data from ACL tables';
    }

    public function up(Schema $schema): void
    {
        // Create new permissions table
        $this->addSql('CREATE TABLE bw_gift_list_permissions (
            id INT AUTO_INCREMENT NOT NULL, 
            gift_list_id INT NOT NULL, 
            user_id INT NOT NULL, 
            permission VARCHAR(50) NOT NULL, 
            INDEX permission_lookup_idx (gift_list_id, user_id, permission), 
            UNIQUE INDEX unique_permission (gift_list_id, user_id, permission), 
            INDEX IDX_gift_list (gift_list_id), 
            INDEX IDX_user (user_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE bw_gift_list_permissions ADD CONSTRAINT FK_gift_list FOREIGN KEY (gift_list_id) REFERENCES bw_gift_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bw_gift_list_permissions ADD CONSTRAINT FK_user FOREIGN KEY (user_id) REFERENCES bw_user (id) ON DELETE CASCADE');

        // Migrate existing ACL data if tables exist
        $hasAclTables = $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.tables 
             WHERE table_schema = DATABASE() 
             AND table_name LIKE 'bw_acl_%'"
        );

        if ($hasAclTables > 0) {
            // Migrate permissions from ACL tables
            $this->addSql("
                INSERT INTO bw_gift_list_permissions (gift_list_id, user_id, permission)
                SELECT 
                    oi.object_identifier as gift_list_id,
                    u.id as user_id,
                    CASE 
                        WHEN e.mask & 1 = 1 THEN 'VIEW'
                        WHEN e.mask & 2 = 2 THEN 'VIEW'
                        WHEN e.mask & 4 = 4 THEN 'EDIT'
                        WHEN e.mask & 8 = 8 THEN 'DELETE'
                        WHEN e.mask & 16 = 16 THEN 'DELETE'
                        WHEN e.mask & 32 = 32 THEN 'OWNER'
                        WHEN e.mask & 64 = 64 THEN 'OWNER'
                        WHEN e.mask & 128 = 128 THEN 'OWNER'
                        WHEN e.mask & 256 = 256 THEN 'SURPRISE_ADD'
                        WHEN e.mask & 512 = 512 THEN 'ALERT_ADD'
                        WHEN e.mask & 1024 = 1024 THEN 'ALERT_PURCHASE'
                        WHEN e.mask & 2048 = 2048 THEN 'ALERT_EDIT'
                        WHEN e.mask & 4096 = 4096 THEN 'ALERT_DELETE'
                    END as permission
                FROM bw_acl_entries e
                JOIN bw_acl_object_identities oi ON e.object_identity_id = oi.id
                JOIN bw_acl_security_identities si ON e.security_identity_id = si.id
                JOIN bw_acl_classes c ON oi.class_id = c.id
                JOIN bw_user u ON u.username = SUBSTRING_INDEX(si.identifier, '-', -1)
                WHERE c.class_type = 'BestWishes\\Entity\\GiftList'
                AND si.identifier LIKE 'BestWishes\\\\Entity\\\\User-%'
                AND (
                    e.mask & 1 = 1 OR e.mask & 2 = 2 OR e.mask & 4 = 4 OR 
                    e.mask & 8 = 8 OR e.mask & 16 = 16 OR e.mask & 32 = 32 OR 
                    e.mask & 64 = 64 OR e.mask & 128 = 128 OR e.mask & 256 = 256 OR 
                    e.mask & 512 = 512 OR e.mask & 1024 = 1024 OR e.mask & 2048 = 2048 OR 
                    e.mask & 4096 = 4096
                )
                ON DUPLICATE KEY UPDATE permission = VALUES(permission)
            ");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bw_gift_list_permissions DROP FOREIGN KEY FK_gift_list');
        $this->addSql('ALTER TABLE bw_gift_list_permissions DROP FOREIGN KEY FK_user');
        $this->addSql('DROP TABLE bw_gift_list_permissions');
    }
}


<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251210102138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop ACL tables (symfony/acl-bundle)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS bw_acl_entries');
        $this->addSql('DROP TABLE IF EXISTS bw_acl_object_identity_ancestors');
        $this->addSql('DROP TABLE IF EXISTS bw_acl_object_identities');
        $this->addSql('DROP TABLE IF EXISTS bw_acl_security_identities');
        $this->addSql('DROP TABLE IF EXISTS bw_acl_classes');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Cannot recreate ACL tables.');
    }
}


<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200423202024 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Drop FOSUserBundle and adjust User entity';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bw_reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_398C30ECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bw_reset_password_request ADD CONSTRAINT FK_398C30ECA76ED395 FOREIGN KEY (user_id) REFERENCES bw_user (id)');
        $this->addSql('DROP INDEX UNIQ_81F6EE84C05FB297 ON bw_user');
        $this->addSql('DROP INDEX UNIQ_81F6EE8492FC23A8 ON bw_user');
        $this->addSql('DROP INDEX UNIQ_81F6EE84A0D96FBF ON bw_user');
        $this->addSql('ALTER TABLE bw_user DROP username_canonical, DROP email_canonical, DROP enabled, DROP salt, DROP confirmation_token, DROP password_requested_at, CHANGE list_id list_id INT DEFAULT NULL, CHANGE username username VARCHAR(100) NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81F6EE84F85E0677 ON bw_user (username)');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bw_reset_password_request');
        $this->addSql('DROP INDEX UNIQ_81F6EE84F85E0677 ON bw_user');
        $this->addSql('ALTER TABLE bw_user ADD username_canonical VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, ADD email_canonical VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, ADD enabled TINYINT(1) NOT NULL, ADD salt VARCHAR(255) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, ADD confirmation_token VARCHAR(180) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`, ADD password_requested_at DATETIME DEFAULT \'NULL\', CHANGE list_id list_id INT DEFAULT NULL, CHANGE username username VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81F6EE84C05FB297 ON bw_user (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81F6EE8492FC23A8 ON bw_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81F6EE84A0D96FBF ON bw_user (email_canonical)');
    }
}

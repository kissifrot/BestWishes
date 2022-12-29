<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20221230103531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use immutable dates everywhere';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bw_gift CHANGE added_date added_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE received_date received_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE purchase_date purchase_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE bw_gift_list CHANGE last_update last_update DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE birthdate birthdate DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE bw_reset_password_request CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE bw_user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql("UPDATE bw_user SET roles='[]' WHERE roles='a:0:{}'");
        $this->addSql("UPDATE bw_user SET roles='[\"ROLE_SUPER_ADMIN\"]' WHERE roles LIKE 'a:1%'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bw_reset_password_request CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bw_user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE bw_gift_list CHANGE last_update last_update DATE NOT NULL, CHANGE birthdate birthdate DATE NOT NULL');
        $this->addSql('ALTER TABLE bw_gift CHANGE added_date added_date DATE NOT NULL, CHANGE received_date received_date DATETIME DEFAULT NULL, CHANGE purchase_date purchase_date DATE DEFAULT NULL');
    }
}

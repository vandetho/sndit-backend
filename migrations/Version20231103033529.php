<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103033529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sndit_tracking ADD package_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE sndit_tracking ADD CONSTRAINT FK_2C7AD04DF44CABFF FOREIGN KEY (package_id) REFERENCES sndit_package (id)');
        $this->addSql('CREATE INDEX IDX_2C7AD04DF44CABFF ON sndit_tracking (package_id)');
        $this->addSql('ALTER TABLE sndit_user ADD email VARCHAR(255) DEFAULT NULL, ADD email_canonical VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6FDB534E7927C74 ON sndit_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6FDB534A0D96FBF ON sndit_user (email_canonical)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sndit_tracking DROP FOREIGN KEY FK_2C7AD04DF44CABFF');
        $this->addSql('DROP INDEX IDX_2C7AD04DF44CABFF ON sndit_tracking');
        $this->addSql('ALTER TABLE sndit_tracking DROP package_id');
        $this->addSql('DROP INDEX UNIQ_A6FDB534E7927C74 ON sndit_user');
        $this->addSql('DROP INDEX UNIQ_A6FDB534A0D96FBF ON sndit_user');
        $this->addSql('ALTER TABLE sndit_user DROP email, DROP email_canonical');
    }
}

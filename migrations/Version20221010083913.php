<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221010083913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sndit_city (id INT UNSIGNED AUTO_INCREMENT NOT NULL, region_id INT UNSIGNED NOT NULL, name VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_635614998260155 (region_id), UNIQUE INDEX unique_city_in_region (name, region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_company (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, canonical_name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, marking JSON NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_F74C865C5E237E06 (name), UNIQUE INDEX UNIQ_F74C865C674D812 (canonical_name), UNIQUE INDEX UNIQ_F74C865C5F37A13B (token), INDEX IDX_F74C865CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_employee (id INT UNSIGNED AUTO_INCREMENT NOT NULL, creator_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, company_id INT UNSIGNED DEFAULT NULL, token VARCHAR(255) NOT NULL, roles JSON NOT NULL, marking JSON NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_D999C7F05F37A13B (token), INDEX IDX_D999C7F061220EA6 (creator_id), INDEX IDX_D999C7F0A76ED395 (user_id), INDEX IDX_D999C7F0979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_helpdesk_ticket (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, internal_user_id INT UNSIGNED DEFAULT NULL, name VARCHAR(150) NOT NULL, token VARCHAR(255) NOT NULL, email VARCHAR(150) DEFAULT NULL, phoneNumber VARCHAR(30) DEFAULT NULL, content VARCHAR(255) NOT NULL, marking JSON NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_BD51F10A5F37A13B (token), INDEX IDX_BD51F10AA76ED395 (user_id), INDEX IDX_BD51F10ABF7692A3 (internal_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_helpdesk_ticket_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ticket_id INT UNSIGNED NOT NULL, ticket_message_id INT UNSIGNED DEFAULT NULL, filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2FEF00A3700047D2 (ticket_id), INDEX IDX_2FEF00A3C5E9817D (ticket_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_helpdesk_ticket_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, internal_user_id INT UNSIGNED DEFAULT NULL, ticket_id INT UNSIGNED NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BEB9EB50A76ED395 (user_id), INDEX IDX_BEB9EB50BF7692A3 (internal_user_id), INDEX IDX_BEB9EB50700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_internal_last_login (id INT UNSIGNED AUTO_INCREMENT NOT NULL, internal_user_id INT UNSIGNED NOT NULL, ip VARCHAR(50) NOT NULL, device VARCHAR(150) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_85F32969BF7692A3 (internal_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_internal_user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, reset_password TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, country_code VARCHAR(5) DEFAULT NULL, roles JSON NOT NULL, first_name VARCHAR(150) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, dob DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(1) NOT NULL, telegram_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, locale VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_88790615E7927C74 (email), UNIQUE INDEX UNIQ_88790615A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_887906156B01BC5B (phone_number), UNIQUE INDEX UNIQ_88790615CC0B3066 (telegram_id), UNIQUE INDEX UNIQ_887906155F37A13B (token), UNIQUE INDEX unique_telegram_room (telegram_id, token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_last_login (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, ip VARCHAR(50) NOT NULL, device VARCHAR(150) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4E8CC7B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_notification_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_18CE6BD35E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_otp (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, phone_number VARCHAR(180) NOT NULL, requestId VARCHAR(255) NOT NULL, price NUMERIC(20, 6) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7DA5777BA1637001 (requestId), INDEX IDX_7DA5777BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_package (id INT UNSIGNED AUTO_INCREMENT NOT NULL, city_id INT UNSIGNED NOT NULL, company_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED DEFAULT NULL, deliverer_id INT UNSIGNED DEFAULT NULL, creator_id INT UNSIGNED NOT NULL, name VARCHAR(150) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, token VARCHAR(255) NOT NULL, marking JSON NOT NULL, address VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, latitude NUMERIC(11, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_669BE8865F37A13B (token), INDEX IDX_669BE8868BAC62AF (city_id), INDEX IDX_669BE886979B1AD6 (company_id), INDEX IDX_669BE886A76ED395 (user_id), INDEX IDX_669BE886B6A6A3F4 (deliverer_id), INDEX IDX_669BE88661220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_package_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, package_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED DEFAULT NULL, transition_name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_973A9176F44CABFF (package_id), INDEX IDX_973A9176A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_package_image (id INT UNSIGNED AUTO_INCREMENT NOT NULL, package_id INT UNSIGNED NOT NULL, image_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B953422FF44CABFF (package_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_refresh_token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_2E256002C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_region (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, iso_country_code VARCHAR(2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX region_country_code (iso_country_code), UNIQUE INDEX unique_region_in_country (iso_country_code, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_template (id INT UNSIGNED AUTO_INCREMENT NOT NULL, city_id INT UNSIGNED NOT NULL, company_id INT UNSIGNED NOT NULL, creator_id INT UNSIGNED NOT NULL, name VARCHAR(150) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1366ADD28BAC62AF (city_id), INDEX IDX_1366ADD2979B1AD6 (company_id), INDEX IDX_1366ADD261220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_tracking (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, latitude NUMERIC(11, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2C7AD04DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, last_otp_id INT UNSIGNED DEFAULT NULL, phone_number VARCHAR(30) NOT NULL, country_code VARCHAR(5) DEFAULT NULL, roles JSON NOT NULL, verified TINYINT(1) NOT NULL, deleted_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', first_name VARCHAR(150) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, dob DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(1) NOT NULL, telegram_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, locale VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_A6FDB5346B01BC5B (phone_number), UNIQUE INDEX UNIQ_A6FDB534CC0B3066 (telegram_id), UNIQUE INDEX UNIQ_A6FDB5345F37A13B (token), INDEX IDX_A6FDB5343C0A6447 (last_otp_id), UNIQUE INDEX unique_telegram_room (telegram_id, token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_user_notification_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, notification_category_id INT UNSIGNED DEFAULT NULL, user_notification_token_id INT UNSIGNED DEFAULT NULL, title VARCHAR(150) DEFAULT NULL, body VARCHAR(150) DEFAULT NULL, data JSON DEFAULT NULL, is_read TINYINT(1) NOT NULL, receipt_id VARCHAR(150) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_42536702C7DDAEA3 (notification_category_id), INDEX IDX_42536702D4CA9262 (user_notification_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sndit_user_notification_token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, token VARCHAR(255) NOT NULL, valid TINYINT(1) NOT NULL, communication_type VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_84A795A5F37A13B (token), INDEX IDX_84A795AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sndit_city ADD CONSTRAINT FK_635614998260155 FOREIGN KEY (region_id) REFERENCES sndit_region (id)');
        $this->addSql('ALTER TABLE sndit_company ADD CONSTRAINT FK_F74C865CA76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_employee ADD CONSTRAINT FK_D999C7F061220EA6 FOREIGN KEY (creator_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_employee ADD CONSTRAINT FK_D999C7F0A76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_employee ADD CONSTRAINT FK_D999C7F0979B1AD6 FOREIGN KEY (company_id) REFERENCES sndit_company (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket ADD CONSTRAINT FK_BD51F10AA76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket ADD CONSTRAINT FK_BD51F10ABF7692A3 FOREIGN KEY (internal_user_id) REFERENCES sndit_internal_user (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_attachment ADD CONSTRAINT FK_2FEF00A3700047D2 FOREIGN KEY (ticket_id) REFERENCES sndit_helpdesk_ticket (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_attachment ADD CONSTRAINT FK_2FEF00A3C5E9817D FOREIGN KEY (ticket_message_id) REFERENCES sndit_helpdesk_ticket_message (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message ADD CONSTRAINT FK_BEB9EB50A76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message ADD CONSTRAINT FK_BEB9EB50BF7692A3 FOREIGN KEY (internal_user_id) REFERENCES sndit_internal_user (id)');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message ADD CONSTRAINT FK_BEB9EB50700047D2 FOREIGN KEY (ticket_id) REFERENCES sndit_helpdesk_ticket (id)');
        $this->addSql('ALTER TABLE sndit_internal_last_login ADD CONSTRAINT FK_85F32969BF7692A3 FOREIGN KEY (internal_user_id) REFERENCES sndit_internal_user (id)');
        $this->addSql('ALTER TABLE sndit_last_login ADD CONSTRAINT FK_4E8CC7B8A76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_otp ADD CONSTRAINT FK_7DA5777BA76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_package ADD CONSTRAINT FK_669BE8868BAC62AF FOREIGN KEY (city_id) REFERENCES sndit_city (id)');
        $this->addSql('ALTER TABLE sndit_package ADD CONSTRAINT FK_669BE886979B1AD6 FOREIGN KEY (company_id) REFERENCES sndit_company (id)');
        $this->addSql('ALTER TABLE sndit_package ADD CONSTRAINT FK_669BE886A76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_package ADD CONSTRAINT FK_669BE886B6A6A3F4 FOREIGN KEY (deliverer_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_package ADD CONSTRAINT FK_669BE88661220EA6 FOREIGN KEY (creator_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_package_history ADD CONSTRAINT FK_973A9176F44CABFF FOREIGN KEY (package_id) REFERENCES sndit_package (id)');
        $this->addSql('ALTER TABLE sndit_package_history ADD CONSTRAINT FK_973A9176A76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_package_image ADD CONSTRAINT FK_B953422FF44CABFF FOREIGN KEY (package_id) REFERENCES sndit_package (id)');
        $this->addSql('ALTER TABLE sndit_template ADD CONSTRAINT FK_1366ADD28BAC62AF FOREIGN KEY (city_id) REFERENCES sndit_city (id)');
        $this->addSql('ALTER TABLE sndit_template ADD CONSTRAINT FK_1366ADD2979B1AD6 FOREIGN KEY (company_id) REFERENCES sndit_company (id)');
        $this->addSql('ALTER TABLE sndit_template ADD CONSTRAINT FK_1366ADD261220EA6 FOREIGN KEY (creator_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_tracking ADD CONSTRAINT FK_2C7AD04DA76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
        $this->addSql('ALTER TABLE sndit_user ADD CONSTRAINT FK_A6FDB5343C0A6447 FOREIGN KEY (last_otp_id) REFERENCES sndit_otp (id)');
        $this->addSql('ALTER TABLE sndit_user_notification_message ADD CONSTRAINT FK_42536702C7DDAEA3 FOREIGN KEY (notification_category_id) REFERENCES sndit_notification_category (id)');
        $this->addSql('ALTER TABLE sndit_user_notification_message ADD CONSTRAINT FK_42536702D4CA9262 FOREIGN KEY (user_notification_token_id) REFERENCES sndit_user_notification_token (id)');
        $this->addSql('ALTER TABLE sndit_user_notification_token ADD CONSTRAINT FK_84A795AA76ED395 FOREIGN KEY (user_id) REFERENCES sndit_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sndit_package DROP FOREIGN KEY FK_669BE8868BAC62AF');
        $this->addSql('ALTER TABLE sndit_template DROP FOREIGN KEY FK_1366ADD28BAC62AF');
        $this->addSql('ALTER TABLE sndit_employee DROP FOREIGN KEY FK_D999C7F0979B1AD6');
        $this->addSql('ALTER TABLE sndit_package DROP FOREIGN KEY FK_669BE886979B1AD6');
        $this->addSql('ALTER TABLE sndit_template DROP FOREIGN KEY FK_1366ADD2979B1AD6');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_attachment DROP FOREIGN KEY FK_2FEF00A3700047D2');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message DROP FOREIGN KEY FK_BEB9EB50700047D2');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_attachment DROP FOREIGN KEY FK_2FEF00A3C5E9817D');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket DROP FOREIGN KEY FK_BD51F10ABF7692A3');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message DROP FOREIGN KEY FK_BEB9EB50BF7692A3');
        $this->addSql('ALTER TABLE sndit_internal_last_login DROP FOREIGN KEY FK_85F32969BF7692A3');
        $this->addSql('ALTER TABLE sndit_user_notification_message DROP FOREIGN KEY FK_42536702C7DDAEA3');
        $this->addSql('ALTER TABLE sndit_user DROP FOREIGN KEY FK_A6FDB5343C0A6447');
        $this->addSql('ALTER TABLE sndit_package_history DROP FOREIGN KEY FK_973A9176F44CABFF');
        $this->addSql('ALTER TABLE sndit_package_image DROP FOREIGN KEY FK_B953422FF44CABFF');
        $this->addSql('ALTER TABLE sndit_city DROP FOREIGN KEY FK_635614998260155');
        $this->addSql('ALTER TABLE sndit_company DROP FOREIGN KEY FK_F74C865CA76ED395');
        $this->addSql('ALTER TABLE sndit_employee DROP FOREIGN KEY FK_D999C7F061220EA6');
        $this->addSql('ALTER TABLE sndit_employee DROP FOREIGN KEY FK_D999C7F0A76ED395');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket DROP FOREIGN KEY FK_BD51F10AA76ED395');
        $this->addSql('ALTER TABLE sndit_helpdesk_ticket_message DROP FOREIGN KEY FK_BEB9EB50A76ED395');
        $this->addSql('ALTER TABLE sndit_last_login DROP FOREIGN KEY FK_4E8CC7B8A76ED395');
        $this->addSql('ALTER TABLE sndit_otp DROP FOREIGN KEY FK_7DA5777BA76ED395');
        $this->addSql('ALTER TABLE sndit_package DROP FOREIGN KEY FK_669BE886A76ED395');
        $this->addSql('ALTER TABLE sndit_package DROP FOREIGN KEY FK_669BE886B6A6A3F4');
        $this->addSql('ALTER TABLE sndit_package DROP FOREIGN KEY FK_669BE88661220EA6');
        $this->addSql('ALTER TABLE sndit_package_history DROP FOREIGN KEY FK_973A9176A76ED395');
        $this->addSql('ALTER TABLE sndit_template DROP FOREIGN KEY FK_1366ADD261220EA6');
        $this->addSql('ALTER TABLE sndit_tracking DROP FOREIGN KEY FK_2C7AD04DA76ED395');
        $this->addSql('ALTER TABLE sndit_user_notification_token DROP FOREIGN KEY FK_84A795AA76ED395');
        $this->addSql('ALTER TABLE sndit_user_notification_message DROP FOREIGN KEY FK_42536702D4CA9262');
        $this->addSql('DROP TABLE sndit_city');
        $this->addSql('DROP TABLE sndit_company');
        $this->addSql('DROP TABLE sndit_employee');
        $this->addSql('DROP TABLE sndit_helpdesk_ticket');
        $this->addSql('DROP TABLE sndit_helpdesk_ticket_attachment');
        $this->addSql('DROP TABLE sndit_helpdesk_ticket_message');
        $this->addSql('DROP TABLE sndit_internal_last_login');
        $this->addSql('DROP TABLE sndit_internal_user');
        $this->addSql('DROP TABLE sndit_last_login');
        $this->addSql('DROP TABLE sndit_notification_category');
        $this->addSql('DROP TABLE sndit_otp');
        $this->addSql('DROP TABLE sndit_package');
        $this->addSql('DROP TABLE sndit_package_history');
        $this->addSql('DROP TABLE sndit_package_image');
        $this->addSql('DROP TABLE sndit_refresh_token');
        $this->addSql('DROP TABLE sndit_region');
        $this->addSql('DROP TABLE sndit_template');
        $this->addSql('DROP TABLE sndit_tracking');
        $this->addSql('DROP TABLE sndit_user');
        $this->addSql('DROP TABLE sndit_user_notification_message');
        $this->addSql('DROP TABLE sndit_user_notification_token');
    }
}

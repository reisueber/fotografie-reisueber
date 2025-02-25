<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225140241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_price_package (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, description LONGTEXT DEFAULT NULL, tracklist JSON NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_2F3392653DA5256D (image_id), INDEX IDX_2F339265DBF11E1D (idUsersCreator), INDEX IDX_2F33926530D07CD5 (idUsersChanger), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F3392653DA5256D FOREIGN KEY (image_id) REFERENCES me_media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F339265DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F33926530D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_package DROP FOREIGN KEY FK_3335E7D830D07CD5');
        $this->addSql('ALTER TABLE app_package DROP FOREIGN KEY FK_3335E7D8DBF11E1D');
        $this->addSql('DROP TABLE app_package');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_package (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, price NUMERIC(10, 2) NOT NULL, includes JSON DEFAULT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_3335E7D830D07CD5 (idUsersChanger), INDEX IDX_3335E7D8DBF11E1D (idUsersCreator), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE app_package ADD CONSTRAINT FK_3335E7D830D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_package ADD CONSTRAINT FK_3335E7D8DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_price_package DROP FOREIGN KEY FK_2F3392653DA5256D');
        $this->addSql('ALTER TABLE app_price_package DROP FOREIGN KEY FK_2F339265DBF11E1D');
        $this->addSql('ALTER TABLE app_price_package DROP FOREIGN KEY FK_2F33926530D07CD5');
        $this->addSql('DROP TABLE app_price_package');
    }
}

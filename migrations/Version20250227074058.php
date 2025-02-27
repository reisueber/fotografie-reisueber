<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227074058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Korrigiert die Struktur der PricePackage Tabelle ohne Datenverlust';
    }

    public function up(Schema $schema): void
    {
        // Prüfen, ob die Tabelle existiert
        $tableExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'app_price_package'"
        )->fetchOne();

        if (!$tableExists) {
            // Tabelle existiert nicht, erstelle sie
            $this->addSql('CREATE TABLE app_price_package (
                id INT AUTO_INCREMENT NOT NULL,
                image_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) DEFAULT NULL,
                description LONGTEXT DEFAULT NULL,
                bulletPoints JSON NOT NULL DEFAULT ("[]"),
                created DATETIME NOT NULL,
                changed DATETIME NOT NULL,
                idUsersCreator INT DEFAULT NULL,
                idUsersChanger INT DEFAULT NULL,
                INDEX IDX_2F3392653DA5256D (image_id),
                INDEX IDX_2F339265DBF11E1D (idUsersCreator),
                INDEX IDX_2F33926530D07CD5 (idUsersChanger),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            
            $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F3392653DA5256D FOREIGN KEY (image_id) REFERENCES me_media (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F339265DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
            $this->addSql('ALTER TABLE app_price_package ADD CONSTRAINT FK_2F33926530D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
            
            return;
        }

        // Überprüfen und korrigieren aller Spalten

        // Prüfen ob bulletPoints existiert
        $bulletPointsExists = $this->connection->executeQuery(
            "SHOW COLUMNS FROM `app_price_package` LIKE 'bulletPoints'"
        )->rowCount() > 0;

        // Prüfen ob bullet_points existiert
        $bullet_pointsExists = $this->connection->executeQuery(
            "SHOW COLUMNS FROM `app_price_package` LIKE 'bullet_points'"
        )->rowCount() > 0;

        // Fall 1: bulletPoints existiert nicht, aber bullet_points existiert
        if (!$bulletPointsExists && $bullet_pointsExists) {
            $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bulletPoints JSON NOT NULL DEFAULT ("[]")');
        }
        // Fall 2: Beide existieren nicht
        else if (!$bulletPointsExists && !$bullet_pointsExists) {
            $this->addSql('ALTER TABLE app_price_package ADD bulletPoints JSON NOT NULL DEFAULT ("[]")');
        }
        // Fall 3: bulletPoints existiert, aber hat keinen Default-Wert
        else if ($bulletPointsExists) {
            $this->addSql('ALTER TABLE app_price_package MODIFY bulletPoints JSON NOT NULL DEFAULT ("[]")');
        }

        // Prüfen ob price korrekt ist
        $priceColumn = $this->connection->executeQuery(
            "SHOW COLUMNS FROM `app_price_package` LIKE 'price'"
        )->fetchAssociative();

        if ($priceColumn) {
            // Korrekten Datentyp für price sicherstellen (DECIMAL(10,2) NULL)
            $this->addSql('ALTER TABLE app_price_package MODIFY price DECIMAL(10,2) DEFAULT NULL');
        } else {
            $this->addSql('ALTER TABLE app_price_package ADD price DECIMAL(10,2) DEFAULT NULL');
        }

        // Prüfen ob tracklist existiert und falls ja, ob es noch gebraucht wird
        $tracklistExists = $this->connection->executeQuery(
            "SHOW COLUMNS FROM `app_price_package` LIKE 'tracklist'"
        )->rowCount() > 0;

        if ($tracklistExists) {
            // Dieser Schritt ist optional - falls tracklist nicht mehr gebraucht wird, kannst du es entfernen
            // $this->addSql('ALTER TABLE app_price_package DROP tracklist');
        }

        // Sicherstellen, dass alle NULL-Werte für bulletPoints korrigiert werden
        $this->addSql('UPDATE app_price_package SET bulletPoints = "[]" WHERE bulletPoints IS NULL');
    }

    public function down(Schema $schema): void
    {
        // Keine Änderungen im down(), da wir die Struktur nur fixieren
    }
}

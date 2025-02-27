<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix bulletPoints field name issue in app_price_package table
 */
final class Version20250226FixBulletPoints extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix bulletPoints field name mismatch in app_price_package table';
    }

    public function up(Schema $schema): void
    {
        // Check if bullet_points column exists
        $sql = "SHOW COLUMNS FROM `app_price_package` LIKE 'bullet_points'";
        $stmt = $this->connection->executeQuery($sql);
        $bulletPointsColumnExists = count($stmt->fetchAllAssociative()) > 0;
        
        // Check if bulletPoints column exists
        $sql = "SHOW COLUMNS FROM `app_price_package` LIKE 'bulletPoints'";
        $stmt = $this->connection->executeQuery($sql);
        $bulletPointsDirectColumnExists = count($stmt->fetchAllAssociative()) > 0;
        
        if ($bulletPointsColumnExists && !$bulletPointsDirectColumnExists) {
            // Rename bullet_points to bulletPoints
            $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bulletPoints JSON NOT NULL DEFAULT ("[]") COMMENT \'(DC2Type:json)\'');
        } else if (!$bulletPointsColumnExists && !$bulletPointsDirectColumnExists) {
            // Create bulletPoints column
            $this->addSql('ALTER TABLE app_price_package ADD bulletPoints JSON NOT NULL DEFAULT ("[]") COMMENT \'(DC2Type:json)\'');
        } else if (!$bulletPointsColumnExists && $bulletPointsDirectColumnExists) {
            // Set default value for bulletPoints if it already exists
            $this->addSql('ALTER TABLE app_price_package MODIFY bulletPoints JSON NOT NULL DEFAULT ("[]") COMMENT \'(DC2Type:json)\'');
        }
        
        // Check for tracklist
        $sql = "SHOW COLUMNS FROM `app_price_package` LIKE 'tracklist'";
        $stmt = $this->connection->executeQuery($sql);
        
        // Update existing records
        $this->addSql('UPDATE app_price_package SET bulletPoints = "[]" WHERE bulletPoints IS NULL OR bulletPoints = ""');
    }

    public function down(Schema $schema): void
    {
        // Check if bulletPoints column exists
        $sql = "SHOW COLUMNS FROM `app_price_package` LIKE 'bulletPoints'";
        $stmt = $this->connection->executeQuery($sql);
        $bulletPointsDirectColumnExists = count($stmt->fetchAllAssociative()) > 0;
        
        // Check if bullet_points column exists
        $sql = "SHOW COLUMNS FROM `app_price_package` LIKE 'bullet_points'";
        $stmt = $this->connection->executeQuery($sql);
        $bulletPointsColumnExists = count($stmt->fetchAllAssociative()) > 0;
        
        if ($bulletPointsDirectColumnExists && !$bulletPointsColumnExists) {
            // Rename bulletPoints back to bullet_points
            $this->addSql('ALTER TABLE app_price_package CHANGE bulletPoints bullet_points JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        }
    }
}
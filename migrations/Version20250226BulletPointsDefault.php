<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Set default value for bullet_points field in app_price_package table
 */
final class Version20250226BulletPointsDefault extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add default value for bullet_points field in app_price_package table';
    }

    public function up(Schema $schema): void
    {
        // Setze einen Default-Wert für das bullet_points Feld
        $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bullet_points JSON NOT NULL DEFAULT ("[]") COMMENT \'(DC2Type:json)\'');
        
        // Update existing records that might have NULL values
        $this->addSql('UPDATE app_price_package SET bullet_points = "[]" WHERE bullet_points IS NULL');
    }

    public function down(Schema $schema): void
    {
        // Restore original field without default value
        $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bullet_points JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
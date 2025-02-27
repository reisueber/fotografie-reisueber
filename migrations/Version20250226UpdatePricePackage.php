<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Update Price Package table to fix issues with price field and bulletPoints
 */
final class Version20250226UpdatePricePackage extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix price and bullet_points field in app_price_package table';
    }

    public function up(Schema $schema): void
    {
        // Ensure price field is properly set up
        $this->addSql('ALTER TABLE app_price_package MODIFY price DECIMAL(10,2) NULL');
        
        // Ensure bullet_points field has a default value
        $this->addSql('ALTER TABLE app_price_package MODIFY bullet_points JSON NOT NULL DEFAULT ("[]") COMMENT \'(DC2Type:json)\'');

        // Update existing records
        $this->addSql('UPDATE app_price_package SET bullet_points = "[]" WHERE bullet_points IS NULL OR bullet_points = ""');
    }

    public function down(Schema $schema): void
    {
        // Restore original fields
        $this->addSql('ALTER TABLE app_price_package MODIFY price DECIMAL(10,2) NULL');
        $this->addSql('ALTER TABLE app_price_package MODIFY bullet_points JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251029154800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Behebt bullet_points Spaltenname in PricePackage';
    }

    public function up(Schema $schema): void
    {
        // Prüfe ob bulletPoints (camelCase) existiert und benenne zurück zu bullet_points
        $camelCaseExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.columns 
             WHERE table_schema = DATABASE() 
             AND table_name = 'app_price_package' 
             AND column_name = 'bulletPoints'"
        )->fetchOne();

        if ($camelCaseExists) {
            $this->addSql('ALTER TABLE app_price_package CHANGE bulletPoints bullet_points JSON DEFAULT NULL');
        }
        
        // Prüfe ob die Spalte existiert bevor wir sie updaten
        $columnExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.columns 
             WHERE table_schema = DATABASE() 
             AND table_name = 'app_price_package' 
             AND column_name = 'bullet_points'"
        )->fetchOne();
        
        if ($columnExists) {
            $this->addSql('UPDATE app_price_package SET bullet_points = JSON_ARRAY() WHERE bullet_points IS NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bulletPoints JSON DEFAULT NULL');
    }
}


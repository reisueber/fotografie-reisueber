<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225152231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Verwende snake_case für den Spaltennamen in der Datenbank
        $this->addSql('ALTER TABLE app_price_package ADD bullet_points JSON NOT NULL');
        
        // Initialisiere mit einem leeren Array für bestehende Einträge
        $this->addSql("UPDATE app_price_package SET bullet_points = '[]'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_price_package DROP bullet_points');
    }
}

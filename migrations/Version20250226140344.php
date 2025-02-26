<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226140344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_price_package MODIFY bulletPoints JSON NULL');
        $this->addSql('ALTER TABLE app_price_package CHANGE tracklist tracklist JSON NOT NULL');
        $this->addSql('ALTER TABLE app_price_package CHANGE bullet_points bullet_points JSON NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_price_package ADD bulletPoints JSON NOT NULL, CHANGE tracklist tracklist JSON NOT NULL, CHANGE bullet_points bullet_points JSON DEFAULT \'_utf8mb4\\\\\'\'[]\\\\\'\'\' NOT NULL');
    }
}

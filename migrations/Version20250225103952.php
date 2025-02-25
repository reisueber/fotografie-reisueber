<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225103952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create price_package table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('app_price_package');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('title', 'string', ['length' => 255]);
        // Weitere Felder hier hinzufügen
        
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('app_price_package');
    }
}

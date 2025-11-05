<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251023120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Erstellt die Blog-Post Tabelle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_blog_post (
            id INT AUTO_INCREMENT NOT NULL,
            image_id INT DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            teaser LONGTEXT DEFAULT NULL,
            content LONGTEXT DEFAULT NULL,
            published_at DATETIME DEFAULT NULL,
            created DATETIME NOT NULL,
            changed DATETIME NOT NULL,
            idUsersCreator INT DEFAULT NULL,
            idUsersChanger INT DEFAULT NULL,
            INDEX IDX_BLOG_POST_IMAGE (image_id),
            INDEX IDX_BLOG_POST_CREATOR (idUsersCreator),
            INDEX IDX_BLOG_POST_CHANGER (idUsersChanger),
            INDEX IDX_BLOG_POST_PUBLISHED (published_at),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE app_blog_post ADD CONSTRAINT FK_BLOG_POST_IMAGE FOREIGN KEY (image_id) REFERENCES me_media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_blog_post ADD CONSTRAINT FK_BLOG_POST_CREATOR FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE app_blog_post ADD CONSTRAINT FK_BLOG_POST_CHANGER FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_blog_post DROP FOREIGN KEY FK_BLOG_POST_IMAGE');
        $this->addSql('ALTER TABLE app_blog_post DROP FOREIGN KEY FK_BLOG_POST_CREATOR');
        $this->addSql('ALTER TABLE app_blog_post DROP FOREIGN KEY FK_BLOG_POST_CHANGER');
        $this->addSql('DROP TABLE app_blog_post');
    }
}


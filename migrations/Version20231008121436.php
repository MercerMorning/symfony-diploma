<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231008121436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, name, variant, file, previous_video_id FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, variant VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL, previous_video_id INTEGER DEFAULT NULL, CONSTRAINT FK_7CC7DA2CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO video (id, name, variant, file, previous_video_id) SELECT id, name, variant, file, previous_video_id FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
        $this->addSql('CREATE INDEX IDX_7CC7DA2CA76ED395 ON video (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, name, variant, file, previous_video_id FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, variant VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL, previous_video_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO video (id, name, variant, file, previous_video_id) SELECT id, name, variant, file, previous_video_id FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
    }
}

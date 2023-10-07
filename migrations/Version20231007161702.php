<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231007161702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, previous_video_id, name, variant FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, previous_video_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, variant VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO video (id, previous_video_id, name, variant) SELECT id, previous_video_id, name, variant FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, name, variant, previous_video_id FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, previous_video_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, variant VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_7CC7DA2C362A184A FOREIGN KEY (previous_video_id) REFERENCES video (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO video (id, name, variant, previous_video_id) SELECT id, name, variant, previous_video_id FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2C362A184A ON video (previous_video_id)');
    }
}

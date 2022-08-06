<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220805154020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user and file tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, expire_in DATETIME DEFAULT NULL, access_once BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON file (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE user');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220807124340 extends AbstractMigration {

    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_8C9F3610A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, user_id, uuid, name, path, mime, expire_in, access_once FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, expire_in DATETIME DEFAULT NULL, access_once BOOLEAN NOT NULL, created_at DATETIME NOT NULL, delete_token VARCHAR(255) NOT NULL, CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO file (id, user_id, uuid, name, path, mime, expire_in, access_once, created_at, delete_token) SELECT id, user_id, uuid, name, path, mime, expire_in, access_once, datetime(), hex(randomblob(8)) FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON file (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C9F3610D17F50A6 ON file (uuid)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, uuid, name FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO user (id, uuid, name, created_at) SELECT id, uuid, name, datetime() FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON user (uuid)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8C9F3610D17F50A6');
        $this->addSql('DROP INDEX IDX_8C9F3610A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__file AS SELECT id, user_id, uuid, name, path, mime, expire_in, access_once FROM file');
        $this->addSql('DROP TABLE file');
        $this->addSql('CREATE TABLE file (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, expire_in DATETIME DEFAULT NULL, access_once BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO file (id, user_id, uuid, name, path, mime, expire_in, access_once) SELECT id, user_id, uuid, name, path, mime, expire_in, access_once FROM __temp__file');
        $this->addSql('DROP TABLE __temp__file');
        $this->addSql('CREATE INDEX IDX_8C9F3610A76ED395 ON file (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649D17F50A6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, uuid, name FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL --(DC2Type:guid)
        , name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, uuid, name) SELECT id, uuid, name FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}

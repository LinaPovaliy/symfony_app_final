<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116133529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sessions (sess_id VARBINARY(128) NOT NULL PRIMARY KEY, sess_data BLOB NOT NULL, sess_lifetime INT NOT NULL, sess_time INT NOT NULL)');
        $this->addSql('CREATE INDEX expiry ON sessions (sess_lifetime)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sessions');
    }
}

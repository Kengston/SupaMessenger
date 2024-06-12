<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611172206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f2f118837');
        $this->addSql('DROP INDEX idx_b6bd307f2f118837');
        $this->addSql('ALTER TABLE message ADD forwarded_from TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE message DROP forwarded_from_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message ADD forwarded_from_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message DROP forwarded_from');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f2f118837 FOREIGN KEY (forwarded_from_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b6bd307f2f118837 ON message (forwarded_from_id)');
    }
}

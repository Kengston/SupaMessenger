<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321114023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add the 'deleted' column as nullable
        $this->addSql('ALTER TABLE message ADD deleted BOOLEAN');

        // Set a default value for the 'deleted' column
        $this->addSql('UPDATE message SET deleted = false');

        // Alter the 'deleted' column to be NOT NULL
        $this->addSql('ALTER TABLE message ALTER COLUMN deleted SET NOT NULL');

        $this->addSql('ALTER TABLE message ALTER created_at SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE message ALTER updated_at SET DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // This down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP deleted');
        $this->addSql('ALTER TABLE message ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE message ALTER updated_at DROP DEFAULT');
    }
}

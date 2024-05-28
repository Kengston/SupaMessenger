<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527190043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f537a1329');
        $this->addSql('DROP INDEX uniq_b6bd307f537a1329');
        $this->addSql('ALTER TABLE message RENAME COLUMN message_id TO replyToMessage');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7FA738CB FOREIGN KEY (replyToMessage) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6BD307F7FA738CB ON message (replyToMessage)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F7FA738CB');
        $this->addSql('DROP INDEX UNIQ_B6BD307F7FA738CB');
        $this->addSql('ALTER TABLE message RENAME COLUMN replyToMessage TO message_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f537a1329 FOREIGN KEY (message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_b6bd307f537a1329 ON message (message_id)');
    }
}

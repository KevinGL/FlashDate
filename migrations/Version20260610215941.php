<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610215941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E956AE248B FOREIGN KEY (user1_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9441B8B65 FOREIGN KEY (user2_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E956AE248B ON conversation (user1_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9441B8B65 ON conversation (user2_id)');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b119d86650f TO IDX_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11a4392681 TO IDX_D79F6B11613FECDF');
        $this->addSql('ALTER TABLE user ADD city VARCHAR(255) NOT NULL, ADD distance_filter DOUBLE PRECISION NOT NULL, ADD age_range VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E956AE248B');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9441B8B65');
        $this->addSql('DROP INDEX IDX_8A8E26E956AE248B ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9441B8B65 ON conversation');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11a76ed395 TO IDX_D79F6B119D86650F');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11613fecdf TO IDX_D79F6B11A4392681');
        $this->addSql('ALTER TABLE `user` DROP city, DROP distance_filter, DROP age_range');
    }
}

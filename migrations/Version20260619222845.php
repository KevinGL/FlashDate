<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260619222845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE daty (id INT AUTO_INCREMENT NOT NULL, part1_id INT NOT NULL, part2_id INT NOT NULL, INDEX IDX_BE9F6B357F9C608B (part1_id), INDEX IDX_BE9F6B356D29CF65 (part2_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE daty ADD CONSTRAINT FK_BE9F6B357F9C608B FOREIGN KEY (part1_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE daty ADD CONSTRAINT FK_BE9F6B356D29CF65 FOREIGN KEY (part2_id) REFERENCES participant (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E956AE248B FOREIGN KEY (user1_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9441B8B65 FOREIGN KEY (user2_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E956AE248B ON conversation (user1_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9441B8B65 ON conversation (user2_id)');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b119d86650f TO IDX_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11a4392681 TO IDX_D79F6B11613FECDF');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE daty DROP FOREIGN KEY FK_BE9F6B357F9C608B');
        $this->addSql('ALTER TABLE daty DROP FOREIGN KEY FK_BE9F6B356D29CF65');
        $this->addSql('DROP TABLE daty');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E956AE248B');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9441B8B65');
        $this->addSql('DROP INDEX IDX_8A8E26E956AE248B ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9441B8B65 ON conversation');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11a76ed395 TO IDX_D79F6B119D86650F');
        $this->addSql('ALTER TABLE participant RENAME INDEX idx_d79f6b11613fecdf TO IDX_D79F6B11A4392681');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610211033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY `FK_8A8E26E94BA75E4E`');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY `FK_8A8E26E97A4F44D3`');
        $this->addSql('DROP INDEX IDX_8A8E26E94BA75E4E ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E97A4F44D3 ON conversation');
        $this->addSql('ALTER TABLE conversation ADD user1_id INT NOT NULL, ADD user2_id INT NOT NULL, DROP user1_id_id, DROP user2_id_id');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E956AE248B FOREIGN KEY (user1_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9441B8B65 FOREIGN KEY (user2_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E956AE248B ON conversation (user1_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9441B8B65 ON conversation (user2_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E956AE248B');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9441B8B65');
        $this->addSql('DROP INDEX IDX_8A8E26E956AE248B ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9441B8B65 ON conversation');
        $this->addSql('ALTER TABLE conversation ADD user1_id_id INT NOT NULL, ADD user2_id_id INT NOT NULL, DROP user1_id, DROP user2_id');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT `FK_8A8E26E94BA75E4E` FOREIGN KEY (user1_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT `FK_8A8E26E97A4F44D3` FOREIGN KEY (user2_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8A8E26E94BA75E4E ON conversation (user1_id_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E97A4F44D3 ON conversation (user2_id_id)');
    }
}

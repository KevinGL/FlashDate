<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610212054 extends AbstractMigration
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
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY `FK_D79F6B119D86650F`');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY `FK_D79F6B11A4392681`');
        $this->addSql('DROP INDEX IDX_D79F6B119D86650F ON participant');
        $this->addSql('DROP INDEX IDX_D79F6B11A4392681 ON participant');
        $this->addSql('ALTER TABLE participant ADD user_id INT NOT NULL, ADD session_id INT NOT NULL, DROP user_id_id, DROP session_id_id');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11A76ED395 ON participant (user_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11613FECDF ON participant (session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E956AE248B');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9441B8B65');
        $this->addSql('DROP INDEX IDX_8A8E26E956AE248B ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9441B8B65 ON conversation');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11613FECDF');
        $this->addSql('DROP INDEX IDX_D79F6B11A76ED395 ON participant');
        $this->addSql('DROP INDEX IDX_D79F6B11613FECDF ON participant');
        $this->addSql('ALTER TABLE participant ADD user_id_id INT NOT NULL, ADD session_id_id INT NOT NULL, DROP user_id, DROP session_id');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT `FK_D79F6B119D86650F` FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT `FK_D79F6B11A4392681` FOREIGN KEY (session_id_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D79F6B119D86650F ON participant (user_id_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11A4392681 ON participant (session_id_id)');
    }
}

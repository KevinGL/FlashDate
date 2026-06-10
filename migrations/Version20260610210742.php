<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610210742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY `FK_B6BD307F6B92BD7B`');
        $this->addSql('DROP INDEX IDX_B6BD307F6B92BD7B ON message');
        $this->addSql('ALTER TABLE message CHANGE conversation_id_id conversation_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message CHANGE conversation_id conversation_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT `FK_B6BD307F6B92BD7B` FOREIGN KEY (conversation_id_id) REFERENCES conversation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B6BD307F6B92BD7B ON message (conversation_id_id)');
    }
}

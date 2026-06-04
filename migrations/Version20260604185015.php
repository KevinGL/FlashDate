<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260604185015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, user_id1_id INT NOT NULL, user_id2_id INT NOT NULL, INDEX IDX_8A8E26E9464EB667 (user_id1_id), INDEX IDX_8A8E26E954FB1989 (user_id2_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, conversation_id_id INT NOT NULL, INDEX IDX_B6BD307F6B92BD7B (conversation_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, session_id_id INT NOT NULL, UNIQUE INDEX UNIQ_D79F6B119D86650F (user_id_id), INDEX IDX_D79F6B11A4392681 (session_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, user_id_id INT NOT NULL, UNIQUE INDEX UNIQ_876E0D99D86650F (user_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, is_active TINYINT NOT NULL, max_participants INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, birth_date DATE NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9464EB667 FOREIGN KEY (user_id1_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E954FB1989 FOREIGN KEY (user_id2_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6B92BD7B FOREIGN KEY (conversation_id_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B119D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A4392681 FOREIGN KEY (session_id_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D99D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9464EB667');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E954FB1989');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6B92BD7B');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B119D86650F');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A4392681');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D99D86650F');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE `user`');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804105122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A76ED395');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE choices DROP FOREIGN KEY FK_5CE96391E27F6BF');
        $this->addSql('ALTER TABLE choices ADD CONSTRAINT FK_5CE96391E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE personal_informations DROP FOREIGN KEY FK_8923AEFB853CD175');
        $this->addSql('ALTER TABLE personal_informations ADD CONSTRAINT FK_8923AEFB853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE pireply DROP FOREIGN KEY FK_6575603A7A7B643');
        $this->addSql('ALTER TABLE pireply DROP FOREIGN KEY FK_6575603AE0DEB379');
        $this->addSql('ALTER TABLE pireply ADD CONSTRAINT FK_6575603A7A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('ALTER TABLE pireply ADD CONSTRAINT FK_6575603AE0DEB379 FOREIGN KEY (pi_id) REFERENCES personal_informations (id)');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9212469DE2');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92A76ED395');
        $this->addSql('ALTER TABLE quiz ADD timer TINYINT(1) DEFAULT NULL, ADD quiztime INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E08A0E4E7F');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E08A0E4E7F FOREIGN KEY (reply_id) REFERENCES choices (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A76ED395');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE choices DROP FOREIGN KEY FK_5CE96391E27F6BF');
        $this->addSql('ALTER TABLE choices ADD CONSTRAINT FK_5CE96391E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_informations DROP FOREIGN KEY FK_8923AEFB853CD175');
        $this->addSql('ALTER TABLE personal_informations ADD CONSTRAINT FK_8923AEFB853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pireply DROP FOREIGN KEY FK_6575603AE0DEB379');
        $this->addSql('ALTER TABLE pireply DROP FOREIGN KEY FK_6575603A7A7B643');
        $this->addSql('ALTER TABLE pireply ADD CONSTRAINT FK_6575603AE0DEB379 FOREIGN KEY (pi_id) REFERENCES personal_informations (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pireply ADD CONSTRAINT FK_6575603A7A7B643 FOREIGN KEY (result_id) REFERENCES result (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9212469DE2');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92A76ED395');
        $this->addSql('ALTER TABLE quiz DROP timer, DROP quiztime');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E08A0E4E7F');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E08A0E4E7F FOREIGN KEY (reply_id) REFERENCES choices (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}

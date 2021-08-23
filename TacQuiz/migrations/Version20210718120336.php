<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210718120336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pireply (id INT AUTO_INCREMENT NOT NULL, pi_id INT DEFAULT NULL, reply VARCHAR(255) DEFAULT NULL, crypted_id VARCHAR(255) DEFAULT NULL, INDEX IDX_6575603AE0DEB379 (pi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pireply ADD CONSTRAINT FK_6575603AE0DEB379 FOREIGN KEY (pi_id) REFERENCES personal_informations (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pireply');
    }
}

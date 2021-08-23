<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721110040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113E0DEB379');
        $this->addSql('DROP INDEX UNIQ_136AC113E0DEB379 ON result');
        $this->addSql('ALTER TABLE result ADD crypted_id VARCHAR(255) DEFAULT NULL, DROP pi_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result ADD pi_id INT DEFAULT NULL, DROP crypted_id');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113E0DEB379 FOREIGN KEY (pi_id) REFERENCES personal_informations (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_136AC113E0DEB379 ON result (pi_id)');
    }
}

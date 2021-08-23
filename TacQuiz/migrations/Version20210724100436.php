<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210724100436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113325BAF86');
        $this->addSql('DROP INDEX UNIQ_136AC113325BAF86 ON result');
        $this->addSql('ALTER TABLE result DROP pirep_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE result ADD pirep_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113325BAF86 FOREIGN KEY (pirep_id) REFERENCES pireply (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_136AC113325BAF86 ON result (pirep_id)');
    }
}

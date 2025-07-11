<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711071302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD formateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D4155D8F51 ON session (formateur_id)');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(50) NOT NULL, ADD prenom VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4155D8F51');
        $this->addSql('DROP INDEX IDX_D044D5D4155D8F51 ON session');
        $this->addSql('ALTER TABLE session DROP formateur_id');
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom');
    }
}

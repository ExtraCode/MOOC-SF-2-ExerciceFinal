<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221216135518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prestation ADD proprietaire_id INT NOT NULL');
        $this->addSql('UPDATE prestation SET proprietaire_id = 1');
        $this->addSql('ALTER TABLE prestation ADD CONSTRAINT FK_51C88FAD76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_51C88FAD76C50E4A ON prestation (proprietaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prestation DROP FOREIGN KEY FK_51C88FAD76C50E4A');
        $this->addSql('DROP INDEX IDX_51C88FAD76C50E4A ON prestation');
        $this->addSql('ALTER TABLE prestation DROP proprietaire_id');
    }
}

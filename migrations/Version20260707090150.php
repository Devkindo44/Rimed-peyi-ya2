<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707090150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD adressede_livraison_id INT DEFAULT NULL, CHANGE frais_port frais_port DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D9095A002 FOREIGN KEY (adressede_livraison_id) REFERENCES adresse_de_livraison (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D9095A002 ON commande (adressede_livraison_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D9095A002');
        $this->addSql('DROP INDEX IDX_6EEAA67D9095A002 ON commande');
        $this->addSql('ALTER TABLE commande DROP adressede_livraison_id, CHANGE frais_port frais_port DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }
}

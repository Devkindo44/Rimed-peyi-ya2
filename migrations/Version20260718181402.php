<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260718181402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_de_livraison ADD telephone VARCHAR(20) DEFAULT NULL, CHANGE adresse_postale adresse_postale VARCHAR(255) DEFAULT NULL, CHANGE code_postale code_postale VARCHAR(255) DEFAULT NULL, CHANGE ville ville VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande CHANGE frais_port frais_port DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE numero_de_telephone numero_de_telephone VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_de_livraison DROP telephone, CHANGE adresse_postale adresse_postale VARCHAR(255) NOT NULL, CHANGE code_postale code_postale VARCHAR(255) NOT NULL, CHANGE ville ville VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande CHANGE frais_port frais_port DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE numero_de_telephone numero_de_telephone INT DEFAULT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260613170247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_de_livraison ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE adresse_de_livraison ADD CONSTRAINT FK_49630C06A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_49630C06A76ED395 ON adresse_de_livraison (user_id)');
        $this->addSql('ALTER TABLE ligne_de_commande ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_de_commande ADD CONSTRAINT FK_7982ACE64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_7982ACE64584665A ON ligne_de_commande (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_de_livraison DROP FOREIGN KEY FK_49630C06A76ED395');
        $this->addSql('DROP INDEX IDX_49630C06A76ED395 ON adresse_de_livraison');
        $this->addSql('ALTER TABLE adresse_de_livraison DROP user_id');
        $this->addSql('ALTER TABLE ligne_de_commande DROP FOREIGN KEY FK_7982ACE64584665A');
        $this->addSql('DROP INDEX IDX_7982ACE64584665A ON ligne_de_commande');
        $this->addSql('ALTER TABLE ligne_de_commande DROP product_id');
    }
}

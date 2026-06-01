<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260601134549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD ligne_de_commande_id INT DEFAULT NULL, ADD stock_id INT DEFAULT NULL, DROP ligne_de_commande');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADCA2A78B2 FOREIGN KEY (ligne_de_commande_id) REFERENCES ligne_de_commande (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADDCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADCA2A78B2 ON product (ligne_de_commande_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADDCD6110 ON product (stock_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADCA2A78B2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADDCD6110');
        $this->addSql('DROP INDEX IDX_D34A04ADCA2A78B2 ON product');
        $this->addSql('DROP INDEX UNIQ_D34A04ADDCD6110 ON product');
        $this->addSql('ALTER TABLE product ADD ligne_de_commande VARCHAR(255) NOT NULL, DROP ligne_de_commande_id, DROP stock_id');
    }
}

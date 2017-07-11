<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170705195113 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bill_remessa (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, sent TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill ADD bill_remessa_id INT NOT NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312C1C260 FOREIGN KEY (bill_remessa_id) REFERENCES bill_remessa (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A2119E312C1C260 ON bill (bill_remessa_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312C1C260');
        $this->addSql('DROP TABLE bill_remessa');
        $this->addSql('DROP INDEX UNIQ_7A2119E312C1C260 ON bill');
        $this->addSql('ALTER TABLE bill DROP bill_remessa_id');
    }
}

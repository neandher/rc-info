<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170622133138 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bill (id INT AUTO_INCREMENT NOT NULL, bill_status_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, due_date_at DATETIME NOT NULL, payment_date_at DATETIME DEFAULT NULL, amount VARCHAR(255) NOT NULL, amount_paid VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7A2119E387190E55 (bill_status_id), INDEX IDX_7A2119E39395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_status (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, referency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E387190E55 FOREIGN KEY (bill_status_id) REFERENCES bill_status (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E387190E55');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE bill_status');
    }
}

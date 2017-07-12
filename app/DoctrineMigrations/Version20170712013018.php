<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170712013018 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, image_name VARCHAR(255) NOT NULL, published_at DATETIME NOT NULL, is_enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, cnpj VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, bill_pay_day SMALLINT NOT NULL, bill_amount NUMERIC(10, 2) NOT NULL, UNIQUE INDEX UNIQ_81398E09E7927C74 (email), UNIQUE INDEX UNIQ_81398E09A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_81398E09C8C6906B (cnpj), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_addresses (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, uf_id INT DEFAULT NULL, street VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, main_address TINYINT(1) NOT NULL, complement VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C4378D0C9395C3F3 (customer_id), INDEX IDX_C4378D0C705D2C5F (uf_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_user (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login_at DATETIME DEFAULT NULL, is_locked TINYINT(1) NOT NULL, is_expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B6096BB09395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login_at DATETIME DEFAULT NULL, is_locked TINYINT(1) NOT NULL, is_expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill (id INT AUTO_INCREMENT NOT NULL, bill_status_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, bill_remessa_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, due_date_at DATETIME NOT NULL, payment_date_at DATETIME DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, amount_paid NUMERIC(10, 2) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7A2119E387190E55 (bill_status_id), INDEX IDX_7A2119E39395C3F3 (customer_id), INDEX IDX_7A2119E312C1C260 (bill_remessa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_remessa (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, sent TINYINT(1) NOT NULL, sent_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_status (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, referency VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, uf_id INT DEFAULT NULL, nome_fantasia VARCHAR(255) NOT NULL, razao_social VARCHAR(255) NOT NULL, cnpj VARCHAR(255) NOT NULL, agencia VARCHAR(255) NOT NULL, agencia_digito VARCHAR(255) NOT NULL, conta VARCHAR(255) NOT NULL, conta_digito VARCHAR(255) NOT NULL, codigo_banco VARCHAR(255) NOT NULL, carteira VARCHAR(255) NOT NULL, codigo_cliente VARCHAR(255) NOT NULL, aceite VARCHAR(255) NOT NULL, especie_doc VARCHAR(255) NOT NULL, juros VARCHAR(255) NOT NULL, multa VARCHAR(255) NOT NULL, prazo_apos_vencimento VARCHAR(255) NOT NULL, codigo_protesto VARCHAR(255) NOT NULL, prazo_protesto VARCHAR(255) NOT NULL, codigo_baixa_devolucao VARCHAR(255) NOT NULL, prazo_baixa_devolucao VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, main_address TINYINT(1) NOT NULL, complement VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_4FBF094F705D2C5F (uf_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE uf (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, sigla VARCHAR(2) NOT NULL, regiao VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_addresses ADD CONSTRAINT FK_C4378D0C705D2C5F FOREIGN KEY (uf_id) REFERENCES uf (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE site_user ADD CONSTRAINT FK_B6096BB09395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E387190E55 FOREIGN KEY (bill_status_id) REFERENCES bill_status (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312C1C260 FOREIGN KEY (bill_remessa_id) REFERENCES bill_remessa (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F705D2C5F FOREIGN KEY (uf_id) REFERENCES uf (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0C9395C3F3');
        $this->addSql('ALTER TABLE site_user DROP FOREIGN KEY FK_B6096BB09395C3F3');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E39395C3F3');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312C1C260');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E387190E55');
        $this->addSql('ALTER TABLE customer_addresses DROP FOREIGN KEY FK_C4378D0C705D2C5F');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F705D2C5F');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_addresses');
        $this->addSql('DROP TABLE site_user');
        $this->addSql('DROP TABLE admin_user');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE bill_remessa');
        $this->addSql('DROP TABLE bill_status');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE uf');
    }
}

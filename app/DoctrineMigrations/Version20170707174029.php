<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170707174029 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, uf_id INT DEFAULT NULL, nome_fantasia VARCHAR(255) NOT NULL, razao_social VARCHAR(255) NOT NULL, cnpj VARCHAR(255) NOT NULL, agencia VARCHAR(255) NOT NULL, agencia_digito VARCHAR(255) NOT NULL, conta VARCHAR(255) NOT NULL, conta_digito VARCHAR(255) NOT NULL, codigo_banco VARCHAR(255) NOT NULL, carteira VARCHAR(255) NOT NULL, codigo_cliente VARCHAR(255) NOT NULL, aceite VARCHAR(255) NOT NULL, especie_doc VARCHAR(255) NOT NULL, juros VARCHAR(255) NOT NULL, multa VARCHAR(255) NOT NULL, prazo_apos_vencimento VARCHAR(255) NOT NULL, codigo_protesto VARCHAR(255) NOT NULL, prazo_protesto VARCHAR(255) NOT NULL, codigo_baixa_devolucao VARCHAR(255) NOT NULL, prazo_baixa_devolucao VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, district VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, main_address TINYINT(1) NOT NULL, complement VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_4FBF094F705D2C5F (uf_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F705D2C5F FOREIGN KEY (uf_id) REFERENCES uf (id) ON DELETE SET NULL');

        $this->addSql('INSERT INTO company (nome_fantasia, razao_social, cnpj, agencia, agencia_digito, conta, conta_digito, codigo_banco, carteira, codigo_cliente, aceite, especie_doc, juros, multa, prazo_apos_vencimento, codigo_protesto, prazo_protesto, codigo_baixa_devolucao, prazo_baixa_devolucao, street, district, city, zip_code, main_address, complement, created_at, updated_at, uf_id) VALUES (\'R C INFORMATICA\', \'VIRGILANY PERDIGAO ME\', \'04.445.096/0001-52\', \'3132\', \'1\', \'845\', \'2\', \'104\', \'RG\', \'808414-9\', \'S\', \'DM\', 0.81, 4.94, \'120\', \'1\', \'5\', \'1\', \'120\', \'AV COR PEDRO MAIA DE CARVALHO\', \'Praia das Gaivotas\', \'Vila Velha\', \'29102-570\', 1, NULL, \'2017-07-07 16:24:08\', \'2017-07-07 16:24:08\', 8);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE company');
    }
}

<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170712013323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        //$this->addSql('INSERT INTO company (nome_fantasia, razao_social, cnpj, agencia, agencia_digito, conta, conta_digito, codigo_banco, carteira, codigo_cliente, aceite, especie_doc, juros, multa, prazo_apos_vencimento, codigo_protesto, prazo_protesto, codigo_baixa_devolucao, prazo_baixa_devolucao, street, district, city, zip_code, main_address, complement, created_at, updated_at, uf_id) VALUES (\'R C INFORMATICA\', \'VIRGILANY PERDIGAO ME\', \'04.445.096/0001-52\', \'3132\', \'1\', \'845\', \'2\', \'104\', \'RG\', \'808414-9\', \'S\', \'DM\', 0.81, 4.94, \'120\', \'1\', \'5\', \'1\', \'120\', \'AV COR PEDRO MAIA DE CARVALHO\', \'Praia das Gaivotas\', \'Vila Velha\', \'29102-570\', 1, NULL, \'2017-07-07 16:24:08\', \'2017-07-07 16:24:08\', 8);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170704004850 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE uf (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, sigla VARCHAR(2) NOT NULL, regiao VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO uf (nome, sigla, regiao) VALUES (\'Acre\', \'AC\', \'Norte\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Alagoas\', \'AL\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Amapá\', \'AP\', \'Norte\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Amazonas\', \'AM\', \'Norte\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Bahia\', \'BA\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Ceará\', \'CE\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Distrito Federal\', \'DF\', \'Centro-Oeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Espírito Santo\', \'ES\', \'Sudeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Goiás\', \'GO\', \'Centro-Oeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Maranhão\', \'MA\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Mato Grosso\', \'MT\', \'Centro-Oeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Mato Grosso do Sul\', \'MS\', \'Centro-Oeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Minas Gerais\', \'MG\', \'Sudeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Pará\', \'PA\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Paraíba\', \'PB\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Paraná\', \'PR\', \'Sul\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Pernambuco\', \'PE\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Piauí\', \'PI\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Rio de Janeiro\', \'RJ\', \'Sudeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Rio Grande do Norte\', \'R\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Rio Grande do Sul\', \'RS\', \'Sul\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Rondônia\', \'RO\', \'Norte\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Roraima\', \'RR\', \'Norte\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Santa Catarina\', \'SC\', \'Sul\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'São Paulo\', \'SP\', \'Sudeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Sergipe\', \'SE\', \'Nordeste\');
                        INSERT INTO uf (nome, sigla, regiao) VALUES (\'Tocantins\', \'TO\', \'Norte\');');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE uf');
    }
}

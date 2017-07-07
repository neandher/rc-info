<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170705204656 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP INDEX UNIQ_7A2119E312C1C260, ADD INDEX IDX_7A2119E312C1C260 (bill_remessa_id)');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312C1C260');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312C1C260 FOREIGN KEY (bill_remessa_id) REFERENCES bill_remessa (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bill DROP INDEX IDX_7A2119E312C1C260, ADD UNIQUE INDEX UNIQ_7A2119E312C1C260 (bill_remessa_id)');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E312C1C260');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E312C1C260 FOREIGN KEY (bill_remessa_id) REFERENCES bill_remessa (id) ON DELETE CASCADE');
    }
}
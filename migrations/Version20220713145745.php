<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713145745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE27C2E161');
        $this->addSql('DROP INDEX IDX_E00CEDDE27C2E161 ON booking');
        $this->addSql('ALTER TABLE booking DROP station_id_id, CHANGE station_id station_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD station_id_id INT NOT NULL, CHANGE station_id station_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE27C2E161 FOREIGN KEY (station_id_id) REFERENCES station (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE27C2E161 ON booking (station_id_id)');
    }
}

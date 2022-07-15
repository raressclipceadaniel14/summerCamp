<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713083539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA0EF1B80');
        $this->addSql('DROP INDEX IDX_E00CEDDEA0EF1B80 ON booking');
        $this->addSql('ALTER TABLE booking ADD car_id INT DEFAULT NULL, DROP car_id_id');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEC3C6F69F ON booking (car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEC3C6F69F');
        $this->addSql('DROP INDEX IDX_E00CEDDEC3C6F69F ON booking');
        $this->addSql('ALTER TABLE booking ADD car_id_id INT NOT NULL, DROP car_id');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA0EF1B80 FOREIGN KEY (car_id_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEA0EF1B80 ON booking (car_id_id)');
    }
}

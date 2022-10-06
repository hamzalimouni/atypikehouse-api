<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006201845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE house_equipement (house_id INT NOT NULL, equipement_id INT NOT NULL, INDEX IDX_D9248EFE6BB74515 (house_id), INDEX IDX_D9248EFE806F0F5C (equipement_id), PRIMARY KEY(house_id, equipement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE house_equipement ADD CONSTRAINT FK_D9248EFE6BB74515 FOREIGN KEY (house_id) REFERENCES house (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE house_equipement ADD CONSTRAINT FK_D9248EFE806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipement_value DROP FOREIGN KEY FK_3B41B4016BB74515');
        $this->addSql('ALTER TABLE equipement_value DROP FOREIGN KEY FK_3B41B401806F0F5C');
        $this->addSql('DROP TABLE equipement_value');
        $this->addSql('ALTER TABLE house CHANGE status status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipement_value (id INT AUTO_INCREMENT NOT NULL, equipement_id INT NOT NULL, house_id INT NOT NULL, INDEX IDX_3B41B401806F0F5C (equipement_id), INDEX IDX_3B41B4016BB74515 (house_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE equipement_value ADD CONSTRAINT FK_3B41B4016BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('ALTER TABLE equipement_value ADD CONSTRAINT FK_3B41B401806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE house_equipement DROP FOREIGN KEY FK_D9248EFE6BB74515');
        $this->addSql('ALTER TABLE house_equipement DROP FOREIGN KEY FK_D9248EFE806F0F5C');
        $this->addSql('DROP TABLE house_equipement');
        $this->addSql('ALTER TABLE house CHANGE status status TINYINT(1) NOT NULL');
    }
}

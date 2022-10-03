<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003204630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE house ADD address_id INT NOT NULL');
        $this->addSql('ALTER TABLE house ADD CONSTRAINT FK_67D5399DF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_67D5399DF5B7AF75 ON house (address_id)');
        $this->addSql('ALTER TABLE message CHANGE sender_id sender_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE propriety_value ADD propriety_id INT NOT NULL, ADD house_id INT NOT NULL');
        $this->addSql('ALTER TABLE propriety_value ADD CONSTRAINT FK_3D683FC62998488 FOREIGN KEY (propriety_id) REFERENCES propriety (id)');
        $this->addSql('ALTER TABLE propriety_value ADD CONSTRAINT FK_3D683FC66BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('CREATE INDEX IDX_3D683FC62998488 ON propriety_value (propriety_id)');
        $this->addSql('CREATE INDEX IDX_3D683FC66BB74515 ON propriety_value (house_id)');
        $this->addSql('ALTER TABLE reservation ADD user_id INT NOT NULL, ADD house_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C849556BB74515 ON reservation (house_id)');
        $this->addSql('ALTER TABLE review ADD user_id INT NOT NULL, ADD house_id INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C66BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
        $this->addSql('CREATE INDEX IDX_794381C66BB74515 ON review (house_id)');
        $this->addSql('ALTER TABLE user ADD address_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F5B7AF75 ON user (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE house DROP FOREIGN KEY FK_67D5399DF5B7AF75');
        $this->addSql('DROP INDEX UNIQ_67D5399DF5B7AF75 ON house');
        $this->addSql('ALTER TABLE house DROP address_id');
        $this->addSql('ALTER TABLE message CHANGE sender_id sender_id INT NOT NULL');
        $this->addSql('ALTER TABLE propriety_value DROP FOREIGN KEY FK_3D683FC62998488');
        $this->addSql('ALTER TABLE propriety_value DROP FOREIGN KEY FK_3D683FC66BB74515');
        $this->addSql('DROP INDEX IDX_3D683FC62998488 ON propriety_value');
        $this->addSql('DROP INDEX IDX_3D683FC66BB74515 ON propriety_value');
        $this->addSql('ALTER TABLE propriety_value DROP propriety_id, DROP house_id');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BB74515');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395 ON reservation');
        $this->addSql('DROP INDEX IDX_42C849556BB74515 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP user_id, DROP house_id');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C66BB74515');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('DROP INDEX IDX_794381C66BB74515 ON review');
        $this->addSql('ALTER TABLE review DROP user_id, DROP house_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_8D93D649F5B7AF75 ON user');
        $this->addSql('ALTER TABLE user DROP address_id');
    }
}

<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160112035554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, email VARCHAR(50) NOT NULL, password VARCHAR(100) NOT NULL, state INT NOT NULL, registrationDate DATE DEFAULT NULL, lastLoginDate DATE DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Person (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(60) DEFAULT NULL, middlename VARCHAR(60) DEFAULT NULL, lastname VARCHAR(60) DEFAULT NULL, aka VARCHAR(60) DEFAULT NULL, dob DATE DEFAULT NULL, birthplace VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA17977217BBB47 FOREIGN KEY (person_id) REFERENCES Person (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977217BBB47');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE Person');
    }
}

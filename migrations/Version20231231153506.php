<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231153506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE scenario_person (scenario_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_289FBFA5E04E49DF (scenario_id), INDEX IDX_289FBFA5217BBB47 (person_id), PRIMARY KEY(scenario_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_place (scenario_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_5B12E29CE04E49DF (scenario_id), INDEX IDX_5B12E29CDA6A219 (place_id), PRIMARY KEY(scenario_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scenario_person ADD CONSTRAINT FK_289FBFA5E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_person ADD CONSTRAINT FK_289FBFA5217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_place ADD CONSTRAINT FK_5B12E29CE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_place ADD CONSTRAINT FK_5B12E29CDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode ADD archived TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE scenario ADD archived TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scenario_person DROP FOREIGN KEY FK_289FBFA5E04E49DF');
        $this->addSql('ALTER TABLE scenario_person DROP FOREIGN KEY FK_289FBFA5217BBB47');
        $this->addSql('ALTER TABLE scenario_place DROP FOREIGN KEY FK_5B12E29CE04E49DF');
        $this->addSql('ALTER TABLE scenario_place DROP FOREIGN KEY FK_5B12E29CDA6A219');
        $this->addSql('DROP TABLE scenario_person');
        $this->addSql('DROP TABLE scenario_place');
        $this->addSql('ALTER TABLE episode DROP archived');
        $this->addSql('ALTER TABLE scenario DROP archived');
    }
}

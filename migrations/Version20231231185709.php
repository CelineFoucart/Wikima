<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231185709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(2500) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_93ADAABB3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_portal (map_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_4F2A48CC53C55F64 (map_id), INDEX IDX_4F2A48CCB887E1DD (portal_id), PRIMARY KEY(map_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_category (map_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_64BE2FE653C55F64 (map_id), INDEX IDX_64BE2FE612469DE2 (category_id), PRIMARY KEY(map_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_position (id INT AUTO_INCREMENT NOT NULL, place_id INT DEFAULT NULL, map_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(1500) DEFAULT NULL, points JSON NOT NULL COMMENT \'(DC2Type:json)\', color VARCHAR(30) DEFAULT NULL, marker VARCHAR(50) DEFAULT NULL, INDEX IDX_24DED2D2DA6A219 (place_id), INDEX IDX_24DED2D253C55F64 (map_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_person (scenario_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_289FBFA5E04E49DF (scenario_id), INDEX IDX_289FBFA5217BBB47 (person_id), PRIMARY KEY(scenario_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_place (scenario_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_5B12E29CE04E49DF (scenario_id), INDEX IDX_5B12E29CDA6A219 (place_id), PRIMARY KEY(scenario_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE map ADD CONSTRAINT FK_93ADAABB3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE map_portal ADD CONSTRAINT FK_4F2A48CC53C55F64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_portal ADD CONSTRAINT FK_4F2A48CCB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_category ADD CONSTRAINT FK_64BE2FE653C55F64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_category ADD CONSTRAINT FK_64BE2FE612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE map_position ADD CONSTRAINT FK_24DED2D2DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE map_position ADD CONSTRAINT FK_24DED2D253C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
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
        $this->addSql('ALTER TABLE map DROP FOREIGN KEY FK_93ADAABB3DA5256D');
        $this->addSql('ALTER TABLE map_portal DROP FOREIGN KEY FK_4F2A48CC53C55F64');
        $this->addSql('ALTER TABLE map_portal DROP FOREIGN KEY FK_4F2A48CCB887E1DD');
        $this->addSql('ALTER TABLE map_category DROP FOREIGN KEY FK_64BE2FE653C55F64');
        $this->addSql('ALTER TABLE map_category DROP FOREIGN KEY FK_64BE2FE612469DE2');
        $this->addSql('ALTER TABLE map_position DROP FOREIGN KEY FK_24DED2D2DA6A219');
        $this->addSql('ALTER TABLE map_position DROP FOREIGN KEY FK_24DED2D253C55F64');
        $this->addSql('ALTER TABLE scenario_person DROP FOREIGN KEY FK_289FBFA5E04E49DF');
        $this->addSql('ALTER TABLE scenario_person DROP FOREIGN KEY FK_289FBFA5217BBB47');
        $this->addSql('ALTER TABLE scenario_place DROP FOREIGN KEY FK_5B12E29CE04E49DF');
        $this->addSql('ALTER TABLE scenario_place DROP FOREIGN KEY FK_5B12E29CDA6A219');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE map_portal');
        $this->addSql('DROP TABLE map_category');
        $this->addSql('DROP TABLE map_position');
        $this->addSql('DROP TABLE scenario_person');
        $this->addSql('DROP TABLE scenario_place');
        $this->addSql('ALTER TABLE episode DROP archived');
        $this->addSql('ALTER TABLE scenario DROP archived');
    }
}

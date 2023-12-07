<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207095445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, scenario_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(1500) DEFAULT NULL, color VARCHAR(30) DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, INDEX IDX_DDAA1CDAE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE episode_place (episode_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_4F17A599362B62A0 (episode_id), INDEX IDX_4F17A599DA6A219 (place_id), PRIMARY KEY(episode_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE episode_person (episode_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_58E14E6D362B62A0 (episode_id), INDEX IDX_58E14E6D217BBB47 (person_id), PRIMARY KEY(episode_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, pitch VARCHAR(3000) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, public TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_scenario_category (scenario_id INT NOT NULL, scenario_category_id INT NOT NULL, INDEX IDX_93CB7EDEE04E49DF (scenario_id), INDEX IDX_93CB7EDE9341769F (scenario_category_id), PRIMARY KEY(scenario_id, scenario_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_portal (scenario_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_17EDFD23E04E49DF (scenario_id), INDEX IDX_17EDFD23B887E1DD (portal_id), PRIMARY KEY(scenario_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_timeline (scenario_id INT NOT NULL, timeline_id INT NOT NULL, INDEX IDX_46E100A6E04E49DF (scenario_id), INDEX IDX_46E100A6EDBEDD37 (timeline_id), PRIMARY KEY(scenario_id, timeline_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDAE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE episode_place ADD CONSTRAINT FK_4F17A599362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode_place ADD CONSTRAINT FK_4F17A599DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode_person ADD CONSTRAINT FK_58E14E6D362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode_person ADD CONSTRAINT FK_58E14E6D217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_scenario_category ADD CONSTRAINT FK_93CB7EDEE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_scenario_category ADD CONSTRAINT FK_93CB7EDE9341769F FOREIGN KEY (scenario_category_id) REFERENCES scenario_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_portal ADD CONSTRAINT FK_17EDFD23E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_portal ADD CONSTRAINT FK_17EDFD23B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_timeline ADD CONSTRAINT FK_46E100A6E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_timeline ADD CONSTRAINT FK_46E100A6EDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDAE04E49DF');
        $this->addSql('ALTER TABLE episode_place DROP FOREIGN KEY FK_4F17A599362B62A0');
        $this->addSql('ALTER TABLE episode_place DROP FOREIGN KEY FK_4F17A599DA6A219');
        $this->addSql('ALTER TABLE episode_person DROP FOREIGN KEY FK_58E14E6D362B62A0');
        $this->addSql('ALTER TABLE episode_person DROP FOREIGN KEY FK_58E14E6D217BBB47');
        $this->addSql('ALTER TABLE scenario_scenario_category DROP FOREIGN KEY FK_93CB7EDEE04E49DF');
        $this->addSql('ALTER TABLE scenario_scenario_category DROP FOREIGN KEY FK_93CB7EDE9341769F');
        $this->addSql('ALTER TABLE scenario_portal DROP FOREIGN KEY FK_17EDFD23E04E49DF');
        $this->addSql('ALTER TABLE scenario_portal DROP FOREIGN KEY FK_17EDFD23B887E1DD');
        $this->addSql('ALTER TABLE scenario_timeline DROP FOREIGN KEY FK_46E100A6E04E49DF');
        $this->addSql('ALTER TABLE scenario_timeline DROP FOREIGN KEY FK_46E100A6EDBEDD37');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE episode_place');
        $this->addSql('DROP TABLE episode_person');
        $this->addSql('DROP TABLE scenario');
        $this->addSql('DROP TABLE scenario_scenario_category');
        $this->addSql('DROP TABLE scenario_portal');
        $this->addSql('DROP TABLE scenario_timeline');
        $this->addSql('DROP TABLE scenario_category');
    }
}

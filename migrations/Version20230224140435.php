<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230224140435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE about (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, timeline_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL, presentation VARCHAR(2500) DEFAULT NULL, timeline_order SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_3BAE0AA7EDBEDD37 (timeline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, portal_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, is_processed TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_CFBDFA1412469DE2 (category_id), INDEX IDX_CFBDFA14B887E1DD (portal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_category (page_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_86D31EE1C4663E4 (page_id), INDEX IDX_86D31EE112469DE2 (category_id), PRIMARY KEY(page_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_portal (page_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_647865EEC4663E4 (page_id), INDEX IDX_647865EEB887E1DD (portal_id), PRIMARY KEY(page_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) DEFAULT NULL, fullname VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, nationality VARCHAR(255) DEFAULT NULL, job VARCHAR(255) DEFAULT NULL, birthday VARCHAR(255) DEFAULT NULL, birthday_place VARCHAR(255) DEFAULT NULL, death_date VARCHAR(255) DEFAULT NULL, death_place VARCHAR(255) DEFAULT NULL, parents VARCHAR(1500) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, presentation VARCHAR(2500) NOT NULL, biography LONGTEXT DEFAULT NULL, personality LONGTEXT DEFAULT NULL, is_sticky TINYINT(1) DEFAULT NULL, children VARCHAR(255) DEFAULT NULL, siblings VARCHAR(255) DEFAULT NULL, partner VARCHAR(255) DEFAULT NULL, physical_description VARCHAR(400) DEFAULT NULL, species VARCHAR(255) DEFAULT NULL, gender VARCHAR(100) DEFAULT NULL, INDEX IDX_34DCD1763DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_category (person_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_920ABCF6217BBB47 (person_id), INDEX IDX_920ABCF612469DE2 (category_id), PRIMARY KEY(person_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_portal (person_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_970BA346217BBB47 (person_id), INDEX IDX_970BA346B887E1DD (portal_id), PRIMARY KEY(person_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_person_type (person_id INT NOT NULL, person_type_id INT NOT NULL, INDEX IDX_6BD38C8A217BBB47 (person_id), INDEX IDX_6BD38C8AE7D23F1A (person_type_id), PRIMARY KEY(person_id, person_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, illustration_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, situation VARCHAR(255) DEFAULT NULL, dominated_by VARCHAR(255) DEFAULT NULL, map_file VARCHAR(255) DEFAULT NULL, population VARCHAR(255) DEFAULT NULL, capitale_city VARCHAR(255) DEFAULT NULL, description VARCHAR(1500) DEFAULT NULL, history LONGTEXT DEFAULT NULL, presentation LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, is_inhabitable VARCHAR(255) DEFAULT NULL, languages VARCHAR(255) DEFAULT NULL, is_sticky TINYINT(1) DEFAULT NULL, INDEX IDX_741D53CD5926566C (illustration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_place (place_source INT NOT NULL, place_target INT NOT NULL, INDEX IDX_DD6B48EEFD44781A (place_source), INDEX IDX_DD6B48EEE4A12895 (place_target), PRIMARY KEY(place_source, place_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_place_type (place_id INT NOT NULL, place_type_id INT NOT NULL, INDEX IDX_68ABB1CDDA6A219 (place_id), INDEX IDX_68ABB1CDF1809B68 (place_type_id), PRIMARY KEY(place_id, place_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_category (place_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_2C17FE42DA6A219 (place_id), INDEX IDX_2C17FE4212469DE2 (category_id), PRIMARY KEY(place_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_portal (place_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_A9609499DA6A219 (place_id), INDEX IDX_A9609499B887E1DD (portal_id), PRIMARY KEY(place_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, position SMALLINT DEFAULT NULL, INDEX IDX_2D737AEF7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline_category (timeline_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_567C42CAEDBEDD37 (timeline_id), INDEX IDX_567C42CA12469DE2 (category_id), PRIMARY KEY(timeline_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline_portal (timeline_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_943119D8EDBEDD37 (timeline_id), INDEX IDX_943119D8B887E1DD (portal_id), PRIMARY KEY(timeline_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id)');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE1C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_portal ADD CONSTRAINT FK_647865EEC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_portal ADD CONSTRAINT FK_647865EEB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1763DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE person_category ADD CONSTRAINT FK_920ABCF6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_category ADD CONSTRAINT FK_920ABCF612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_portal ADD CONSTRAINT FK_970BA346217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_portal ADD CONSTRAINT FK_970BA346B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8AE7D23F1A FOREIGN KEY (person_type_id) REFERENCES person_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD5926566C FOREIGN KEY (illustration_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE place_place ADD CONSTRAINT FK_DD6B48EEFD44781A FOREIGN KEY (place_source) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place ADD CONSTRAINT FK_DD6B48EEE4A12895 FOREIGN KEY (place_target) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place_type ADD CONSTRAINT FK_68ABB1CDDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place_type ADD CONSTRAINT FK_68ABB1CDF1809B68 FOREIGN KEY (place_type_id) REFERENCES place_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE42DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE4212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_portal ADD CONSTRAINT FK_A9609499DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_portal ADD CONSTRAINT FK_A9609499B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE timeline_category ADD CONSTRAINT FK_567C42CAEDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_category ADD CONSTRAINT FK_567C42CA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_portal ADD CONSTRAINT FK_943119D8EDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_portal ADD CONSTRAINT FK_943119D8B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD type_id INT DEFAULT NULL, ADD is_draft TINYINT(1) DEFAULT NULL, ADD is_private TINYINT(1) DEFAULT NULL, ADD is_sticky TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C54C8C93 FOREIGN KEY (type_id) REFERENCES article_type (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66C54C8C93 ON article (type_id)');
        $this->addSql('ALTER TABLE category ADD presentation LONGTEXT DEFAULT NULL, ADD banner VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE portal ADD presentation LONGTEXT DEFAULT NULL, ADD banner VARCHAR(255) DEFAULT NULL, ADD position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66C54C8C93');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EDBEDD37');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1412469DE2');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B887E1DD');
        $this->addSql('ALTER TABLE page_category DROP FOREIGN KEY FK_86D31EE1C4663E4');
        $this->addSql('ALTER TABLE page_category DROP FOREIGN KEY FK_86D31EE112469DE2');
        $this->addSql('ALTER TABLE page_portal DROP FOREIGN KEY FK_647865EEC4663E4');
        $this->addSql('ALTER TABLE page_portal DROP FOREIGN KEY FK_647865EEB887E1DD');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1763DA5256D');
        $this->addSql('ALTER TABLE person_category DROP FOREIGN KEY FK_920ABCF6217BBB47');
        $this->addSql('ALTER TABLE person_category DROP FOREIGN KEY FK_920ABCF612469DE2');
        $this->addSql('ALTER TABLE person_portal DROP FOREIGN KEY FK_970BA346217BBB47');
        $this->addSql('ALTER TABLE person_portal DROP FOREIGN KEY FK_970BA346B887E1DD');
        $this->addSql('ALTER TABLE person_person_type DROP FOREIGN KEY FK_6BD38C8A217BBB47');
        $this->addSql('ALTER TABLE person_person_type DROP FOREIGN KEY FK_6BD38C8AE7D23F1A');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD5926566C');
        $this->addSql('ALTER TABLE place_place DROP FOREIGN KEY FK_DD6B48EEFD44781A');
        $this->addSql('ALTER TABLE place_place DROP FOREIGN KEY FK_DD6B48EEE4A12895');
        $this->addSql('ALTER TABLE place_place_type DROP FOREIGN KEY FK_68ABB1CDDA6A219');
        $this->addSql('ALTER TABLE place_place_type DROP FOREIGN KEY FK_68ABB1CDF1809B68');
        $this->addSql('ALTER TABLE place_category DROP FOREIGN KEY FK_2C17FE42DA6A219');
        $this->addSql('ALTER TABLE place_category DROP FOREIGN KEY FK_2C17FE4212469DE2');
        $this->addSql('ALTER TABLE place_portal DROP FOREIGN KEY FK_A9609499DA6A219');
        $this->addSql('ALTER TABLE place_portal DROP FOREIGN KEY FK_A9609499B887E1DD');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF7294869C');
        $this->addSql('ALTER TABLE timeline_category DROP FOREIGN KEY FK_567C42CAEDBEDD37');
        $this->addSql('ALTER TABLE timeline_category DROP FOREIGN KEY FK_567C42CA12469DE2');
        $this->addSql('ALTER TABLE timeline_portal DROP FOREIGN KEY FK_943119D8EDBEDD37');
        $this->addSql('ALTER TABLE timeline_portal DROP FOREIGN KEY FK_943119D8B887E1DD');
        $this->addSql('DROP TABLE about');
        $this->addSql('DROP TABLE article_type');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE page_category');
        $this->addSql('DROP TABLE page_portal');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE person_category');
        $this->addSql('DROP TABLE person_portal');
        $this->addSql('DROP TABLE person_person_type');
        $this->addSql('DROP TABLE person_type');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE place_place');
        $this->addSql('DROP TABLE place_place_type');
        $this->addSql('DROP TABLE place_category');
        $this->addSql('DROP TABLE place_portal');
        $this->addSql('DROP TABLE place_type');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE timeline');
        $this->addSql('DROP TABLE timeline_category');
        $this->addSql('DROP TABLE timeline_portal');
        $this->addSql('DROP INDEX IDX_23A0E66C54C8C93 ON article');
        $this->addSql('ALTER TABLE article DROP type_id, DROP is_draft, DROP is_private, DROP is_sticky');
        $this->addSql('ALTER TABLE category DROP presentation, DROP banner');
        $this->addSql('ALTER TABLE portal DROP presentation, DROP banner, DROP position');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}

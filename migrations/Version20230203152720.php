<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203152720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, situation VARCHAR(255) DEFAULT NULL, dominated_by VARCHAR(255) DEFAULT NULL, illustration VARCHAR(255) DEFAULT NULL, map_file VARCHAR(255) DEFAULT NULL, population VARCHAR(255) DEFAULT NULL, capitale_city VARCHAR(255) DEFAULT NULL, description VARCHAR(1500) DEFAULT NULL, history LONGTEXT NOT NULL, presentation LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_place (place_source INT NOT NULL, place_target INT NOT NULL, INDEX IDX_DD6B48EEFD44781A (place_source), INDEX IDX_DD6B48EEE4A12895 (place_target), PRIMARY KEY(place_source, place_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_place_type (place_id INT NOT NULL, place_type_id INT NOT NULL, INDEX IDX_68ABB1CDDA6A219 (place_id), INDEX IDX_68ABB1CDF1809B68 (place_type_id), PRIMARY KEY(place_id, place_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place_place ADD CONSTRAINT FK_DD6B48EEFD44781A FOREIGN KEY (place_source) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place ADD CONSTRAINT FK_DD6B48EEE4A12895 FOREIGN KEY (place_target) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place_type ADD CONSTRAINT FK_68ABB1CDDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_place_type ADD CONSTRAINT FK_68ABB1CDF1809B68 FOREIGN KEY (place_type_id) REFERENCES place_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place_place DROP FOREIGN KEY FK_DD6B48EEFD44781A');
        $this->addSql('ALTER TABLE place_place DROP FOREIGN KEY FK_DD6B48EEE4A12895');
        $this->addSql('ALTER TABLE place_place_type DROP FOREIGN KEY FK_68ABB1CDDA6A219');
        $this->addSql('ALTER TABLE place_place_type DROP FOREIGN KEY FK_68ABB1CDF1809B68');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE place_place');
        $this->addSql('DROP TABLE place_place_type');
        $this->addSql('DROP TABLE place_type');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231130004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) DEFAULT NULL, fullname VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, job VARCHAR(255) DEFAULT NULL, birthday VARCHAR(255) DEFAULT NULL, birthday_place VARCHAR(255) DEFAULT NULL, death_date VARCHAR(255) DEFAULT NULL, death_place VARCHAR(255) DEFAULT NULL, parents VARCHAR(1500) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, presentation VARCHAR(2500) NOT NULL, biography LONGTEXT DEFAULT NULL, personality LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_category (person_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_920ABCF6217BBB47 (person_id), INDEX IDX_920ABCF612469DE2 (category_id), PRIMARY KEY(person_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_portal (person_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_970BA346217BBB47 (person_id), INDEX IDX_970BA346B887E1DD (portal_id), PRIMARY KEY(person_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_category ADD CONSTRAINT FK_920ABCF6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_category ADD CONSTRAINT FK_920ABCF612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_portal ADD CONSTRAINT FK_970BA346217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_portal ADD CONSTRAINT FK_970BA346B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person_category DROP FOREIGN KEY FK_920ABCF6217BBB47');
        $this->addSql('ALTER TABLE person_portal DROP FOREIGN KEY FK_970BA346217BBB47');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE person_category');
        $this->addSql('DROP TABLE person_portal');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240203132120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE section_person (section_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_7E942350D823E37A (section_id), INDEX IDX_7E942350217BBB47 (person_id), PRIMARY KEY(section_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_place (section_id INT NOT NULL, place_id INT NOT NULL, INDEX IDX_E34A34A9D823E37A (section_id), INDEX IDX_E34A34A9DA6A219 (place_id), PRIMARY KEY(section_id, place_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_timeline (section_id INT NOT NULL, timeline_id INT NOT NULL, INDEX IDX_D6939C3AD823E37A (section_id), INDEX IDX_D6939C3AEDBEDD37 (timeline_id), PRIMARY KEY(section_id, timeline_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section_person ADD CONSTRAINT FK_7E942350D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_person ADD CONSTRAINT FK_7E942350217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_place ADD CONSTRAINT FK_E34A34A9D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_place ADD CONSTRAINT FK_E34A34A9DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_timeline ADD CONSTRAINT FK_D6939C3AD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_timeline ADD CONSTRAINT FK_D6939C3AEDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE section_person DROP FOREIGN KEY FK_7E942350D823E37A');
        $this->addSql('ALTER TABLE section_person DROP FOREIGN KEY FK_7E942350217BBB47');
        $this->addSql('ALTER TABLE section_place DROP FOREIGN KEY FK_E34A34A9D823E37A');
        $this->addSql('ALTER TABLE section_place DROP FOREIGN KEY FK_E34A34A9DA6A219');
        $this->addSql('ALTER TABLE section_timeline DROP FOREIGN KEY FK_D6939C3AD823E37A');
        $this->addSql('ALTER TABLE section_timeline DROP FOREIGN KEY FK_D6939C3AEDBEDD37');
        $this->addSql('DROP TABLE section_person');
        $this->addSql('DROP TABLE section_place');
        $this->addSql('DROP TABLE section_timeline');
    }
}

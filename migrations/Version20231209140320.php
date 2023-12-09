<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231209140320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(5000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_group_image (image_group_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_5AC764B671A457EF (image_group_id), INDEX IDX_5AC764B63DA5256D (image_id), PRIMARY KEY(image_group_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_group_portal (image_group_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_D2E7334C71A457EF (image_group_id), INDEX IDX_D2E7334CB887E1DD (portal_id), PRIMARY KEY(image_group_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_group_image ADD CONSTRAINT FK_5AC764B671A457EF FOREIGN KEY (image_group_id) REFERENCES image_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_group_image ADD CONSTRAINT FK_5AC764B63DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_group_portal ADD CONSTRAINT FK_D2E7334C71A457EF FOREIGN KEY (image_group_id) REFERENCES image_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_group_portal ADD CONSTRAINT FK_D2E7334CB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode ADD valid TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD image_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD71A457EF FOREIGN KEY (image_group_id) REFERENCES image_group (id)');
        $this->addSql('CREATE INDEX IDX_741D53CD71A457EF ON place (image_group_id)');
        $this->addSql('ALTER TABLE scenario ADD image_group_id INT DEFAULT NULL, ADD comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE scenario ADD CONSTRAINT FK_3E45C8D871A457EF FOREIGN KEY (image_group_id) REFERENCES image_group (id)');
        $this->addSql('CREATE INDEX IDX_3E45C8D871A457EF ON scenario (image_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD71A457EF');
        $this->addSql('ALTER TABLE scenario DROP FOREIGN KEY FK_3E45C8D871A457EF');
        $this->addSql('ALTER TABLE image_group_image DROP FOREIGN KEY FK_5AC764B671A457EF');
        $this->addSql('ALTER TABLE image_group_image DROP FOREIGN KEY FK_5AC764B63DA5256D');
        $this->addSql('ALTER TABLE image_group_portal DROP FOREIGN KEY FK_D2E7334C71A457EF');
        $this->addSql('ALTER TABLE image_group_portal DROP FOREIGN KEY FK_D2E7334CB887E1DD');
        $this->addSql('DROP TABLE image_group');
        $this->addSql('DROP TABLE image_group_image');
        $this->addSql('DROP TABLE image_group_portal');
        $this->addSql('ALTER TABLE episode DROP valid');
        $this->addSql('DROP INDEX IDX_741D53CD71A457EF ON place');
        $this->addSql('ALTER TABLE place DROP image_group_id');
        $this->addSql('DROP INDEX IDX_3E45C8D871A457EF ON scenario');
        $this->addSql('ALTER TABLE scenario DROP image_group_id, DROP comment');
    }
}

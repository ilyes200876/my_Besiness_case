<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915132350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nft DROP FOREIGN KEY FK_D9C7463CEA9FDD75');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP INDEX IDX_D9C7463CEA9FDD75 ON nft');
        $this->addSql('ALTER TABLE nft ADD title VARCHAR(255) NOT NULL, ADD src VARCHAR(255) NOT NULL, ADD format VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT NOT NULL, CHANGE media_id weight INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_category CHANGE sub_category_name sub_category_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, src VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, weight INT DEFAULT NULL, format VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sub_category CHANGE sub_category_name sub_category_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE nft DROP title, DROP src, DROP format, DROP description, CHANGE weight media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nft ADD CONSTRAINT FK_D9C7463CEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D9C7463CEA9FDD75 ON nft (media_id)');
    }
}

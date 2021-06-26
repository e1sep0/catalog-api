<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210626112845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE good (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE good_category (good_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_F397E7061CF98C70 (good_id), INDEX IDX_F397E70612469DE2 (category_id), PRIMARY KEY(good_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE good_category ADD CONSTRAINT FK_F397E7061CF98C70 FOREIGN KEY (good_id) REFERENCES good (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE good_category ADD CONSTRAINT FK_F397E70612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE good_category DROP FOREIGN KEY FK_F397E70612469DE2');
        $this->addSql('ALTER TABLE good_category DROP FOREIGN KEY FK_F397E7061CF98C70');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE good');
        $this->addSql('DROP TABLE good_category');
    }
}

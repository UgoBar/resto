<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230110085946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_row ADD dish_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_row ADD CONSTRAINT FK_B420E598148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id)');
        $this->addSql('CREATE INDEX IDX_B420E598148EB0CB ON cart_row (dish_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_row DROP FOREIGN KEY FK_B420E598148EB0CB');
        $this->addSql('DROP INDEX IDX_B420E598148EB0CB ON cart_row');
        $this->addSql('ALTER TABLE cart_row DROP dish_id');
    }
}

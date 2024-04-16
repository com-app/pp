<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230510075832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       
        $this->addSql('CREATE TABLE user_cash (id INT AUTO_INCREMENT NOT NULL, cashier_id INT NOT NULL, credit DOUBLE PRECISION NOT NULL, credit_at DATETIME NOT NULL, INDEX IDX_7C38A9702EDB0489 (cashier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_cash ADD CONSTRAINT FK_7C38A9702EDB0489 FOREIGN KEY (cashier_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        
        $this->addSql('ALTER TABLE user_cash DROP FOREIGN KEY FK_7C38A9702EDB0489');
        $this->addSql('DROP TABLE user_cash');
        
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230510102528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_cash ADD debit DOUBLE PRECISION NOT NULL');
        $this->addSql('CREATE TABLE cash_transfert (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, comment LONGTEXT NOT NULL, performed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FBF99147953C1C61 (source_id), INDEX IDX_FBF99147158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147953C1C61 FOREIGN KEY (source_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147953C1C61');
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147158E0B66');
        $this->addSql('DROP TABLE cash_transfert');
        $this->addSql('ALTER TABLE user_cash DROP debit');
    
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205171202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_log (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, user_id INT DEFAULT NULL, action LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8C46B3E9700047D2 (ticket_id), INDEX IDX_8C46B3E9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_log ADD CONSTRAINT FK_8C46B3E9700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE ticket_log ADD CONSTRAINT FK_8C46B3E9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_log DROP FOREIGN KEY FK_8C46B3E9700047D2');
        $this->addSql('ALTER TABLE ticket_log DROP FOREIGN KEY FK_8C46B3E9A76ED395');
        $this->addSql('DROP TABLE ticket_log');
    }
}

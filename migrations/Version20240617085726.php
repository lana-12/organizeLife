<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617085726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events ADD date_event_end DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ADD hour_event_end TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\'');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4642B8210');
        $this->addSql('ALTER TABLE projects CHANGE name name VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4642B8210 FOREIGN KEY (admin_id) REFERENCES `users` (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A45E237E06 ON projects (name)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES `projects` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `events` DROP date_event_end, DROP hour_event_end');
        $this->addSql('ALTER TABLE `projects` DROP FOREIGN KEY FK_5C93B3A4642B8210');
        $this->addSql('DROP INDEX UNIQ_5C93B3A45E237E06 ON `projects`');
        $this->addSql('ALTER TABLE `projects` CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `projects` ADD CONSTRAINT FK_5C93B3A4642B8210 FOREIGN KEY (admin_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
    }
}

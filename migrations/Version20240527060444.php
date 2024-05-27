<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527060444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_collection DROP FOREIGN KEY FK_5B2AA3DE9D86650F');
        $this->addSql('DROP INDEX IDX_5B2AA3DE9D86650F ON user_collection');
        $this->addSql('ALTER TABLE user_collection CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5B2AA3DEA76ED395 ON user_collection (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_collection DROP FOREIGN KEY FK_5B2AA3DEA76ED395');
        $this->addSql('DROP INDEX IDX_5B2AA3DEA76ED395 ON user_collection');
        $this->addSql('ALTER TABLE user_collection CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_collection ADD CONSTRAINT FK_5B2AA3DE9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5B2AA3DE9D86650F ON user_collection (user_id_id)');
    }
}

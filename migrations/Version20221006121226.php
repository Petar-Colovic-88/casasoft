<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221006121226 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE property_permission (property_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(property_id, user_id))');
        $this->addSql('CREATE INDEX IDX_E04992AA549213EC ON property_permission (property_id)');
        $this->addSql('CREATE INDEX IDX_E04992AAA76ED395 ON property_permission (user_id)');
        $this->addSql('ALTER TABLE property_permission ADD CONSTRAINT FK_E04992AA549213EC FOREIGN KEY (property_id) REFERENCES property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE property_permission ADD CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE property_permission DROP CONSTRAINT FK_E04992AA549213EC');
        $this->addSql('ALTER TABLE property_permission DROP CONSTRAINT FK_E04992AAA76ED395');
        $this->addSql('DROP TABLE property_permission');
    }
}

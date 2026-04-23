<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional image URL to question';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question ADD image VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question DROP image');
    }
}

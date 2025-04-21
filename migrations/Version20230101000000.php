<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration initiale forcée';
    }

    public function up(Schema $schema): void
    {
        // Cette migration est vide intentionnellement
        // Vos prochaines migrations s'ajouteront après celle-ci
    }

    public function down(Schema $schema): void
    {
        // Cette migration est vide intentionnellement
    }
}
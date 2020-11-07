<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201107141910 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add the table for appointments in the AcmeVet\Appointment context';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE appointments (
                id CHARACTER(36) PRIMARY KEY, 
                start_time VARCHAR, 
                length CHARACTER(2), 
                pet_name VARCHAR, 
                owner_name VARCHAR,
                contact_number VARCHAR
              )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appointments');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190918140203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announce CHANGE vehicle_id vehicle_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zipcode zipcode INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE enable enable TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE birthday_date birthday_date DATETIME DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zipcode zipcode VARCHAR(10) DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE license_driving license_driving VARCHAR(255) DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE loyalty_points loyalty_points INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE user_id user_id INT DEFAULT NULL, CHANGE announce_id announce_id INT DEFAULT NULL, CHANGE rate rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE user_id user_id INT DEFAULT NULL, CHANGE announce_id announce_id INT DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle CHANGE user_id user_id INT DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT NULL, CHANGE km km INT DEFAULT NULL, CHANGE matriculation matriculation VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE year year DATETIME DEFAULT NULL, CHANGE autonomy autonomy INT DEFAULT NULL, CHANGE door door INT DEFAULT NULL, CHANGE place place INT DEFAULT NULL, CHANGE model model VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announce CHANGE vehicle_id vehicle_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zipcode zipcode INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE price price INT DEFAULT NULL, CHANGE enable enable TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE comment CHANGE user_id user_id INT DEFAULT NULL, CHANGE announce_id announce_id INT DEFAULT NULL, CHANGE rate rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location CHANGE user_id user_id INT DEFAULT NULL, CHANGE announce_id announce_id INT DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE firstname firstname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE birthday_date birthday_date DATETIME DEFAULT \'NULL\', CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zipcode zipcode VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE license_driving license_driving VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE loyalty_points loyalty_points INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE vehicle CHANGE user_id user_id INT DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE model model VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE km km INT DEFAULT NULL, CHANGE matriculation matriculation VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE type type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE year year DATETIME DEFAULT \'NULL\', CHANGE autonomy autonomy INT DEFAULT NULL, CHANGE door door INT DEFAULT NULL, CHANGE place place INT DEFAULT NULL');
    }
}

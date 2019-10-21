<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190916132348 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD7564D218E');
        $this->addSql('DROP INDEX IDX_E6D6DD7564D218E ON announce');
        $this->addSql('ALTER TABLE announce DROP location_id, CHANGE vehicle_id vehicle_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE enable enable TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE birthday_date birthday_date DATETIME DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE zipcode zipcode INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE sign_up_date sign_up_date DATETIME NOT NULL, CHANGE license_driving license_driving VARCHAR(255) DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE mail mail VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C87016831');
        $this->addSql('DROP INDEX IDX_9474526C87016831 ON comment');
        $this->addSql('ALTER TABLE comment ADD announce_id INT DEFAULT NULL, DROP anounce_id, DROP end_date, DROP start_date, CHANGE user_id user_id INT DEFAULT NULL, CHANGE note note INT DEFAULT NULL, CHANGE content content VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id)');
        $this->addSql('CREATE INDEX IDX_9474526C6F5DA3DE ON comment (announce_id)');
        $this->addSql('ALTER TABLE location ADD announce_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB6F5DA3DE FOREIGN KEY (announce_id) REFERENCES announce (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB6F5DA3DE ON location (announce_id)');
        $this->addSql('ALTER TABLE vehicle CHANGE user_id user_id INT DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT NULL, CHANGE km km INT DEFAULT NULL, CHANGE matriculation matriculation VARCHAR(255) DEFAULT NULL, CHANGE year year DATETIME DEFAULT NULL, CHANGE autonomie autonomie INT DEFAULT NULL, CHANGE door door INT DEFAULT NULL, CHANGE place place INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announce ADD location_id INT DEFAULT NULL, CHANGE vehicle_id vehicle_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE price price INT DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE enable enable TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_E6D6DD7564D218E ON announce (location_id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C6F5DA3DE');
        $this->addSql('DROP INDEX IDX_9474526C6F5DA3DE ON comment');
        $this->addSql('ALTER TABLE comment ADD anounce_id INT DEFAULT NULL, ADD end_date DATETIME DEFAULT \'NULL\', ADD start_date DATETIME NOT NULL, DROP announce_id, CHANGE user_id user_id INT DEFAULT NULL, CHANGE note note INT DEFAULT NULL, CHANGE content content VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C87016831 FOREIGN KEY (anounce_id) REFERENCES announce (id)');
        $this->addSql('CREATE INDEX IDX_9474526C87016831 ON comment (anounce_id)');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB6F5DA3DE');
        $this->addSql('DROP INDEX IDX_5E9E89CB6F5DA3DE ON location');
        $this->addSql('ALTER TABLE location DROP announce_id, CHANGE user_id user_id INT DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE firstname firstname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE birthday_date birthday_date DATETIME DEFAULT \'NULL\', CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE zipcode zipcode VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE sign_up_date sign_up_date VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE license_driving license_driving VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE mail mail VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE vehicle CHANGE user_id user_id INT DEFAULT NULL, CHANGE brand brand VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE km km INT DEFAULT NULL, CHANGE matriculation matriculation VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE year year DATETIME DEFAULT \'NULL\', CHANGE autonomie autonomie INT DEFAULT NULL, CHANGE door door INT DEFAULT NULL, CHANGE place place INT DEFAULT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260405160345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(20) NOT NULL, balance NUMERIC(15, 2) NOT NULL, type VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, user_id INT NOT NULL, INDEX IDX_7D3656A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cheque (id INT AUTO_INCREMENT NOT NULL, cheque_number VARCHAR(255) NOT NULL, receiver_iban VARCHAR(255) DEFAULT NULL, amount NUMERIC(15, 2) NOT NULL, status VARCHAR(50) NOT NULL, secure_token VARCHAR(255) NOT NULL, expiration_date DATETIME NOT NULL, created_at DATETIME NOT NULL, sender_id INT NOT NULL, receiver_id INT DEFAULT NULL, INDEX IDX_A0BBFDE9F624B39D (sender_id), INDEX IDX_A0BBFDE9CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE complaint (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_5F2732B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, message VARCHAR(255) NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(15, 2) NOT NULL, type VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, INDEX IDX_723705D1F624B39D (sender_id), INDEX IDX_723705D1CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cheque ADD CONSTRAINT FK_A0BBFDE9F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE cheque ADD CONSTRAINT FK_A0BBFDE9CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY fk_accounts_user');
        $this->addSql('ALTER TABLE cheques DROP FOREIGN KEY fk_cheque_bank');
        $this->addSql('ALTER TABLE cheques DROP FOREIGN KEY fk_cheque_sender');
        $this->addSql('ALTER TABLE cheques DROP FOREIGN KEY fk_cheque_admin');
        $this->addSql('ALTER TABLE cheque_confirmations DROP FOREIGN KEY fk_confirmation_cheque');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY fk_complaint_user');
        $this->addSql('ALTER TABLE complaint_attachments DROP FOREIGN KEY fk_attachment_complaint');
        $this->addSql('ALTER TABLE complaint_responses DROP FOREIGN KEY fk_response_responder');
        $this->addSql('ALTER TABLE complaint_responses DROP FOREIGN KEY fk_response_complaint');
        $this->addSql('ALTER TABLE credits DROP FOREIGN KEY fk_credits_user');
        $this->addSql('ALTER TABLE digital_cheques DROP FOREIGN KEY fk_digital_sender');
        $this->addSql('ALTER TABLE digital_cheque_logs DROP FOREIGN KEY fk_log_cheque');
        $this->addSql('ALTER TABLE money_transactions DROP FOREIGN KEY fk_tx_from_card');
        $this->addSql('ALTER TABLE otp_codes DROP FOREIGN KEY fk_otp_user');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE banks');
        $this->addSql('DROP TABLE cards');
        $this->addSql('DROP TABLE cheques');
        $this->addSql('DROP TABLE cheque_confirmations');
        $this->addSql('DROP TABLE complaints');
        $this->addSql('DROP TABLE complaint_attachments');
        $this->addSql('DROP TABLE complaint_responses');
        $this->addSql('DROP TABLE credits');
        $this->addSql('DROP TABLE digital_cheques');
        $this->addSql('DROP TABLE digital_cheque_logs');
        $this->addSql('DROP TABLE money_transactions');
        $this->addSql('DROP TABLE otp_codes');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounts (id_account INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, balance NUMERIC(15, 2) DEFAULT \'0.00\' NOT NULL, account_type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'Active\' NOT NULL COLLATE `utf8mb4_unicode_ci`, id_user INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_accounts_user (id_user), UNIQUE INDEX account_number (account_number), PRIMARY KEY(id_account)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE banks (id INT AUTO_INCREMENT NOT NULL, bank_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, bank_code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, swift_code VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX swift_code (swift_code), UNIQUE INDEX bank_code (bank_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cards (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, card_number VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, card_type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, secret_code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'ACTIVE\' NOT NULL COLLATE `utf8mb4_unicode_ci`, expires_at DATETIME NOT NULL, limit_amount NUMERIC(15, 2) DEFAULT \'0.00\' NOT NULL, balance NUMERIC(15, 2) DEFAULT \'0.00\' NOT NULL, label VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX card_number (card_number), INDEX idx_cards_user (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cheques (id INT AUTO_INCREMENT NOT NULL, cheque_number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sender_id INT NOT NULL, receiver_iban VARCHAR(34) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, receiver_bank_id INT NOT NULL, amount NUMERIC(15, 2) NOT NULL, issue_date DATE NOT NULL, expiry_date DATE NOT NULL, status ENUM(\'PENDING\', \'VALIDATED\', \'REJECTED\', \'CANCELLED\', \'EXPIRED\', \'REDEEMED\') CHARACTER SET utf8mb4 DEFAULT \'PENDING\' COLLATE `utf8mb4_unicode_ci`, clearing_status ENUM(\'INTERNAL\', \'PENDING_CLEARING\', \'CLEARED\', \'FAILED\') CHARACTER SET utf8mb4 DEFAULT \'INTERNAL\' COLLATE `utf8mb4_unicode_ci`, validation_date DATETIME DEFAULT NULL, admin_id INT DEFAULT NULL, clearing_reference VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, secure_token VARCHAR(12) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, failed_attempts INT DEFAULT 0, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_cheque_status (status), INDEX fk_cheque_bank (receiver_bank_id), INDEX fk_cheque_admin (admin_id), UNIQUE INDEX cheque_number (cheque_number), INDEX idx_cheque_number (cheque_number), INDEX fk_cheque_sender (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cheque_confirmations (id INT AUTO_INCREMENT NOT NULL, cheque_id INT NOT NULL, code_confirmation VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, methode_confirmation ENUM(\'SMS\', \'EMAIL\', \'AUTHENTICATOR\') CHARACTER SET utf8mb4 DEFAULT \'EMAIL\' COLLATE `utf8mb4_unicode_ci`, tentatives INT DEFAULT 0, date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP, date_confirmation DATETIME DEFAULT NULL, ip_address VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, statut ENUM(\'EN_ATTENTE\', \'CONFIRME\', \'EXPIRE\', \'ECHOUE\') CHARACTER SET utf8mb4 DEFAULT \'EN_ATTENTE\' COLLATE `utf8mb4_unicode_ci`, INDEX idx_conf_cheque (cheque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE complaints (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category ENUM(\'Cheque\', \'Transfer\', \'Account\', \'Other\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status ENUM(\'PENDING\', \'IN_REVIEW\', \'RESOLVED\', \'REJECTED\') CHARACTER SET utf8mb4 DEFAULT \'PENDING\' COLLATE `utf8mb4_unicode_ci`, priority ENUM(\'LOW\', \'MEDIUM\', \'HIGH\') CHARACTER SET utf8mb4 DEFAULT \'MEDIUM\' COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_complaint_user (user_id), INDEX idx_complaint_status (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE complaint_attachments (id INT AUTO_INCREMENT NOT NULL, complaint_id INT NOT NULL, file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_path VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX fk_attachment_complaint (complaint_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE complaint_responses (id INT AUTO_INCREMENT NOT NULL, complaint_id INT NOT NULL, responder_id INT NOT NULL, message TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX fk_response_responder (responder_id), INDEX fk_response_complaint (complaint_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE credits (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, amount NUMERIC(12, 2) NOT NULL, interest_rate NUMERIC(5, 2) NOT NULL, duration_months INT NOT NULL, monthly_payment NUMERIC(12, 2) DEFAULT NULL, total_amount NUMERIC(12, 2) DEFAULT NULL, status ENUM(\'PENDING\', \'APPROVED\', \'REJECTED\', \'COMPLETED\') CHARACTER SET utf8mb4 DEFAULT \'PENDING\' COLLATE `utf8mb4_unicode_ci`, request_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, approval_date DATETIME DEFAULT NULL, INDEX idx_credits_status (status), INDEX idx_credits_user (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE digital_cheques (id INT AUTO_INCREMENT NOT NULL, cheque_number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sender_id INT NOT NULL, receiver_id INT DEFAULT NULL, receiver_iban VARCHAR(34) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, amount NUMERIC(15, 2) NOT NULL, secure_token VARCHAR(12) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status ENUM(\'ISSUED\', \'REDEEMED\', \'EXPIRED\', \'CANCELLED\', \'PENDING_BANK\') CHARACTER SET utf8mb4 DEFAULT \'ISSUED\' COLLATE `utf8mb4_unicode_ci`, expiration_date DATE NOT NULL, failed_attempts INT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, redeemed_at DATETIME DEFAULT NULL, INDEX fk_digital_sender (sender_id), UNIQUE INDEX cheque_number (cheque_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE digital_cheque_logs (id INT AUTO_INCREMENT NOT NULL, cheque_id INT DEFAULT NULL, action VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, user_id INT DEFAULT NULL, details TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_log_cheque (cheque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE money_transactions (id BIGINT AUTO_INCREMENT NOT NULL, from_card_id BIGINT NOT NULL, to_card_number VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, amount_dt NUMERIC(15, 2) NOT NULL, status VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'SUCCESS\' NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_tx_from (from_card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE otp_codes (id_otp INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, purpose ENUM(\'EMAIL_VERIFICATION\', \'LOGIN_2FA\', \'PASSWORD_RESET\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, expires_at DATETIME NOT NULL, is_used TINYINT(1) DEFAULT 0 NOT NULL, INDEX fk_otp_user (id_user), PRIMARY KEY(id_otp)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, priorite ENUM(\'BASSE\', \'MOYENNE\', \'HAUTE\') CHARACTER SET utf8mb4 DEFAULT \'MOYENNE\' COLLATE `utf8mb4_unicode_ci`, statut ENUM(\'OUVERT\', \'EN_COURS\', \'RESOLU\', \'FERME\') CHARACTER SET utf8mb4 DEFAULT \'OUVERT\' COLLATE `utf8mb4_unicode_ci`, client_nom VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, client_email VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, date_modification DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_statut (statut), INDEX idx_priorite (priorite), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id_user INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password_hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, role ENUM(\'USER\', \'ADMIN\') CHARACTER SET utf8mb4 DEFAULT \'USER\' NOT NULL COLLATE `utf8mb4_unicode_ci`, profile_image VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email_verified TINYINT(1) DEFAULT 0 NOT NULL, two_factor_enabled TINYINT(1) DEFAULT 0 NOT NULL, face_model_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, account_status ENUM(\'ACTIVE\', \'SUSPENDED\') CHARACTER SET utf8mb4 DEFAULT \'ACTIVE\' NOT NULL COLLATE `utf8mb4_unicode_ci`, revenu DOUBLE PRECISION DEFAULT \'0\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, bank_id INT DEFAULT NULL, iban VARCHAR(34) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, balance NUMERIC(15, 2) DEFAULT \'0.00\', UNIQUE INDEX email (email), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT fk_accounts_user FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cheques ADD CONSTRAINT fk_cheque_bank FOREIGN KEY (receiver_bank_id) REFERENCES banks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cheques ADD CONSTRAINT fk_cheque_sender FOREIGN KEY (sender_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cheques ADD CONSTRAINT fk_cheque_admin FOREIGN KEY (admin_id) REFERENCES users (id_user) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cheque_confirmations ADD CONSTRAINT fk_confirmation_cheque FOREIGN KEY (cheque_id) REFERENCES cheques (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT fk_complaint_user FOREIGN KEY (user_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaint_attachments ADD CONSTRAINT fk_attachment_complaint FOREIGN KEY (complaint_id) REFERENCES complaints (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaint_responses ADD CONSTRAINT fk_response_responder FOREIGN KEY (responder_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaint_responses ADD CONSTRAINT fk_response_complaint FOREIGN KEY (complaint_id) REFERENCES complaints (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE credits ADD CONSTRAINT fk_credits_user FOREIGN KEY (user_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE digital_cheques ADD CONSTRAINT fk_digital_sender FOREIGN KEY (sender_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE digital_cheque_logs ADD CONSTRAINT fk_log_cheque FOREIGN KEY (cheque_id) REFERENCES digital_cheques (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE money_transactions ADD CONSTRAINT fk_tx_from_card FOREIGN KEY (from_card_id) REFERENCES cards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE otp_codes ADD CONSTRAINT fk_otp_user FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE cheque DROP FOREIGN KEY FK_A0BBFDE9F624B39D');
        $this->addSql('ALTER TABLE cheque DROP FOREIGN KEY FK_A0BBFDE9CD53EDB6');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F624B39D');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CD53EDB6');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE cheque');
        $this->addSql('DROP TABLE complaint');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

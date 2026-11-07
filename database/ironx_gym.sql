CREATE DATABASE IF NOT EXISTS ironx_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ironx_gym;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS ai_messages, ai_conversations, password_resets, notifications, reviews, progress_records, workout_exercises, workout_days, exercises, workout_plans, diet_plans, bookings, attendance, payments, subscriptions, membership_plans, trainer_profiles, settings, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','trainer','member') NOT NULL DEFAULT 'member',
 status ENUM('active','suspended') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE trainer_profiles (
 user_id BIGINT UNSIGNED PRIMARY KEY,
 specialization VARCHAR(150) DEFAULT '',
 experience_years SMALLINT UNSIGNED DEFAULT 0,
 bio TEXT,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE membership_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 duration_days INT UNSIGNED NOT NULL,
 description TEXT,
 is_active TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE subscriptions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 plan_id BIGINT UNSIGNED NOT NULL,
 start_date DATE NOT NULL,
 end_date DATE NOT NULL,
 status ENUM('pending','active','expired','cancelled') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id),
 FOREIGN KEY(plan_id) REFERENCES membership_plans(id),
 INDEX(user_id,status,end_date)
) ENGINE=InnoDB;

CREATE TABLE payments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 subscription_id BIGINT UNSIGNED NULL,
 amount DECIMAL(10,2) NOT NULL,
 status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
 gateway_order_id VARCHAR(190) NULL,
 gateway_payment_id VARCHAR(190) NULL UNIQUE,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id),
 FOREIGN KEY(subscription_id) REFERENCES subscriptions(id)
) ENGINE=InnoDB;

CREATE TABLE attendance (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 check_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 check_out TIMESTAMP NULL,
 FOREIGN KEY(user_id) REFERENCES users(id),
 INDEX(user_id,check_in)
) ENGINE=InnoDB;

CREATE TABLE exercises (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 muscle_group VARCHAR(100),
 instructions TEXT
) ENGINE=InnoDB;

CREATE TABLE workout_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 trainer_id BIGINT UNSIGNED NULL,
 title VARCHAR(150) NOT NULL,
 description TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(member_id) REFERENCES users(id),
 FOREIGN KEY(trainer_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE workout_days (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 plan_id BIGINT UNSIGNED NOT NULL,
 day_name VARCHAR(40) NOT NULL,
 FOREIGN KEY(plan_id) REFERENCES workout_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE workout_exercises (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 day_id BIGINT UNSIGNED NOT NULL,
 exercise_id BIGINT UNSIGNED NOT NULL,
 sets_count SMALLINT UNSIGNED DEFAULT 3,
 reps VARCHAR(30) DEFAULT '10',
 rest_seconds SMALLINT UNSIGNED DEFAULT 60,
 FOREIGN KEY(day_id) REFERENCES workout_days(id) ON DELETE CASCADE,
 FOREIGN KEY(exercise_id) REFERENCES exercises(id)
) ENGINE=InnoDB;

CREATE TABLE diet_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 trainer_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(150) NOT NULL,
 notes TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(member_id) REFERENCES users(id),
 FOREIGN KEY(trainer_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE bookings (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 trainer_id BIGINT UNSIGNED NULL,
 session_date DATETIME NOT NULL,
 notes VARCHAR(500),
 status ENUM('requested','confirmed','completed','cancelled') DEFAULT 'requested',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(member_id) REFERENCES users(id),
 FOREIGN KEY(trainer_id) REFERENCES users(id),
 INDEX(session_date,status)
) ENGINE=InnoDB;

CREATE TABLE progress_records (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 weight_kg DECIMAL(6,2) NOT NULL,
 height_cm DECIMAL(6,2) NOT NULL,
 body_fat DECIMAL(5,2) NULL,
 notes TEXT,
 recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(member_id) REFERENCES users(id),
 INDEX(member_id,recorded_at)
) ENGINE=InnoDB;

CREATE TABLE notifications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 message VARCHAR(500) NOT NULL,
 is_read TINYINT(1) DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE reviews (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL,
 comment TEXT,
 status ENUM('pending','approved','rejected') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE ai_conversations (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(150),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ai_messages (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 conversation_id BIGINT UNSIGNED NOT NULL,
 sender ENUM('user','assistant') NOT NULL,
 message TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(conversation_id) REFERENCES ai_conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE password_resets (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL,
 expires_at DATETIME NOT NULL,
 used_at DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX(token_hash,expires_at)
) ENGINE=InnoDB;

CREATE TABLE settings (
 setting_key VARCHAR(100) PRIMARY KEY,
 setting_value TEXT
) ENGINE=InnoDB;

INSERT INTO membership_plans(name,price,duration_days,description) VALUES
('STARTER',999,30,'Gym access and foundational training guidance.'),
('PERFORMANCE',1999,30,'Gym access, group sessions and fitness tracking.'),
('ELITE',3999,30,'Premium coaching support and personalized planning.');

INSERT INTO settings(setting_key,setting_value) VALUES ('gym_name','IRONX Fitness');

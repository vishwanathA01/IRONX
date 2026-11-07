USE ironx_gym;

CREATE TABLE IF NOT EXISTS member_activities (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 activity_type VARCHAR(80) NOT NULL,
 activity_date DATE NOT NULL,
 duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 calories_burned SMALLINT UNSIGNED NULL,
 notes VARCHAR(500) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_member_activities_user FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_member_activity_date(member_id, activity_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fitness_goals (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 member_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(150) NOT NULL,
 goal_type ENUM('weight','workouts','attendance','minutes','custom') NOT NULL DEFAULT 'custom',
 target_value DECIMAL(8,2) NOT NULL,
 current_value DECIMAL(8,2) NOT NULL DEFAULT 0,
 unit VARCHAR(30) NOT NULL DEFAULT '',
 target_date DATE NULL,
 status ENUM('active','completed','paused') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_fitness_goals_user FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_member_goals_status(member_id,status)
) ENGINE=InnoDB;

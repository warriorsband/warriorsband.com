CREATE TABLE `users`
(
    `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` INT(1) UNSIGNED DEFAULT 0 NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` CHAR(128) NOT NULL,
    `last_login` DATETIME,
    `last_login_attempt` DATETIME NOT NULL,
    `login_attempts` TINYINT UNSIGNED DEFAULT 0 NOT NULL,
    `user_type` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL,
    `last_name` VARCHAR(64) NOT NULL,
    `first_name` VARCHAR(64) NOT NULL,
    `program` VARCHAR(64),
    `term` TINYINT UNSIGNED DEFAULT 0 NOT NULL,
    `instrument` TINYINT UNSIGNED DEFAULT 0 NOT NULL,
    `fun_fact` VARCHAR(255),
    `on_campus` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL,
    PRIMARY KEY (`user_id`)
);

CREATE TABLE `events`
(
    `event_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` INT(1) UNSIGNED DEFAULT 0 NOT NULL,
    `creator_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `date` DATE,
    `start_time` TIME,
    `end_time` TIME,
    `location` VARCHAR(255),
    `details` TEXT,
    PRIMARY KEY (`event_id`)
);

CREATE TABLE `event_responses`
(
    `response_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `event_id` INT UNSIGNED NOT NULL,
    `response` TINYINT(1) NOT NULL,
    `comment` TEXT,
    PRIMARY KEY (`response_id`)
);

INSERT INTO users (`status`, `email`, `password`, `last_name`, `first_name`, `last_login_attempt`, `user_type`) VALUES
(
    1,
    "admin@admin.com",
    "3e01644d09a9925e6731ed13206e503aeee6db77f82ec04d39b907b0ba96634df97f2f168ecd77e2fd0422726b1b790589091a60906a0862dfb1fb45092a811e",
    "Admin",
    "Admin",
    NOW(),
    4
);

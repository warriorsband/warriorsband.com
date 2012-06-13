CREATE TABLE users
(
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    status INT(1) UNSIGNED DEFAULT 0 NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password CHAR(128) NOT NULL,
    last_login DATETIME,
    last_login_attempt DATETIME NOT NULL,
    login_attempts INT UNSIGNED DEFAULT 0 NOT NULL,
    user_type INT(1) UNSIGNED DEFAULT 1 NOT NULL,
    PRIMARY KEY (user_id)
)

CREATE TABLE users
(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    last_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password CHAR(128) NOT NULL,
    last_login DATETIME,
    last_login_attempt DATETIME,
    login_attempts INT UNSIGNED,
    user_type INT(1) UNSIGNED DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
)

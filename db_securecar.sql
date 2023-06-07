DROP DATABASE if EXISTS db_securecar;

CREATE DATABASE if NOT EXISTS db_securecar;

USE db_securecar;

CREATE TABLE if NOT EXISTS tb_user(	
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	
	`name` VARCHAR(155) NOT NULL,
	birth DATE NOT NULL,
	cpf CHAR(11) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	`password` CHAR(64) NOT NULL,
	is_validated TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE if NOT EXISTS tb_user_validation(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	validation_code SMALLINT,
	
	user_id INTEGER,
	CONSTRAINT fk_user_validation FOREIGN KEY (user_id) 
	REFERENCES tb_user (id)
);

CREATE TABLE if NOT EXISTS tb_user_sessions(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	session_datetime DATETIME NOT NULL DEFAULT NOW(),
	session_token CHAR(64) NOT NULL,
	
	user_id INTEGER,
	CONSTRAINT fk_user_session FOREIGN KEY (user_id) 
	REFERENCES tb_user (id)
);

CREATE TABLE if NOT EXISTS tb_server_error_log(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	`date` DATETIME DEFAULT NOW(),
	`error` TEXT
);

CREATE TABLE if NOT EXISTS tb_api_token(
	api_token VARCHAR(128) PRIMARY KEY
);
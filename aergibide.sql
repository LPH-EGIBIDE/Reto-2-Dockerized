-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema docker
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema docker
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `docker` DEFAULT CHARACTER SET utf8 ;
USE `docker` ;

-- -----------------------------------------------------
-- Table `docker`.`attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`attachments` ;

CREATE TABLE IF NOT EXISTS `docker`.`attachments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `filename` VARCHAR(128) NOT NULL,
  `filepath` VARCHAR(64) NOT NULL,
  `content_type` VARCHAR(128) NOT NULL DEFAULT 'binary/octet-stream',
  `uploaded_at` DATETIME NOT NULL,
  `uploaded_by` INT NULL,
  `public` TINYINT(1) NOT NULL DEFAULT 1,
  `is_tutorial` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_attachments_users_idx` (`uploaded_by` ASC) VISIBLE,
  CONSTRAINT `fk_attachments_users`
    FOREIGN KEY (`uploaded_by`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`users` ;

CREATE TABLE IF NOT EXISTS `docker`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `password` VARCHAR(256) NOT NULL,
  `type` INT NOT NULL DEFAULT 0,
  `profile_pic` INT NOT NULL DEFAULT -1,
  `profile_description` VARCHAR(1024) NOT NULL DEFAULT '',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `points` INT NOT NULL DEFAULT 0,
  `mfa_type` INT NOT NULL DEFAULT 0,
  `mfa_data` VARCHAR(256) NOT NULL DEFAULT '',
  `password_reset_token` VARCHAR(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE,
  INDEX `fk_users_attachments1_idx` (`profile_pic` ASC) VISIBLE,
  CONSTRAINT `fk_users_attachments1`
    FOREIGN KEY (`profile_pic`)
    REFERENCES `docker`.`attachments` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`achievements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`achievements` ;

CREATE TABLE IF NOT EXISTS `docker`.`achievements` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(256) NOT NULL,
  `points_awarded` INT NOT NULL,
  `photo` INT NOT NULL,
  `requirements` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_achievements_attachments1_idx` (`photo` ASC) VISIBLE,
  CONSTRAINT `fk_achievements_attachments1`
    FOREIGN KEY (`photo`)
    REFERENCES `docker`.`attachments` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`user_achievements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`user_achievements` ;

CREATE TABLE IF NOT EXISTS `docker`.`user_achievements` (
  `user` INT NOT NULL,
  `achievement` INT NOT NULL,
  PRIMARY KEY (`user`, `achievement`),
  INDEX `fk_users_has_achievements_achievements1_idx` (`achievement` ASC) VISIBLE,
  INDEX `fk_users_has_achievements_users1_idx` (`user` ASC) VISIBLE,
  CONSTRAINT `fk_users_has_achievements_users1`
    FOREIGN KEY (`user`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_has_achievements_achievements1`
    FOREIGN KEY (`achievement`)
    REFERENCES `docker`.`achievements` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`post_topics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`post_topics` ;

CREATE TABLE IF NOT EXISTS `docker`.`post_topics` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`posts` ;

CREATE TABLE IF NOT EXISTS `docker`.`posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(256) NOT NULL,
  `description` TEXT NOT NULL,
  `views` INT NOT NULL DEFAULT 0,
  `topic` INT NOT NULL,
  `author` INT NOT NULL,
  `active` INT NOT NULL DEFAULT 1,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_posts_post_topics1_idx` (`topic` ASC) VISIBLE,
  INDEX `fk_posts_users1_idx` (`author` ASC) VISIBLE,
  CONSTRAINT `fk_posts_post_topics1`
    FOREIGN KEY (`topic`)
    REFERENCES `docker`.`post_topics` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_users1`
    FOREIGN KEY (`author`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`post_answers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`post_answers` ;

CREATE TABLE IF NOT EXISTS `docker`.`post_answers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `author` INT NOT NULL,
  `post` INT NOT NULL,
  `message` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_post_answers_users1_idx` (`author` ASC) VISIBLE,
  INDEX `fk_post_answers_posts1_idx` (`post` ASC) VISIBLE,
  CONSTRAINT `fk_post_answers_users1`
    FOREIGN KEY (`author`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_post_answers_posts1`
    FOREIGN KEY (`post`)
    REFERENCES `docker`.`posts` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`post_answer_attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`post_answer_attachments` ;

CREATE TABLE IF NOT EXISTS `docker`.`post_answer_attachments` (
  `attachments_id` INT NOT NULL,
  `post_answers_id` INT NOT NULL,
  PRIMARY KEY (`attachments_id`, `post_answers_id`),
  INDEX `fk_attachments_has_post_answers_post_answers1_idx` (`post_answers_id` ASC) VISIBLE,
  INDEX `fk_attachments_has_post_answers_attachments1_idx` (`attachments_id` ASC) VISIBLE,
  CONSTRAINT `fk_attachments_has_post_answers_attachments1`
    FOREIGN KEY (`attachments_id`)
    REFERENCES `docker`.`attachments` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_attachments_has_post_answers_post_answers1`
    FOREIGN KEY (`post_answers_id`)
    REFERENCES `docker`.`post_answers` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`post_attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`post_attachments` ;

CREATE TABLE IF NOT EXISTS `docker`.`post_attachments` (
  `posts_id` INT NOT NULL,
  `attachments_id` INT NOT NULL,
  PRIMARY KEY (`posts_id`, `attachments_id`),
  INDEX `fk_posts_has_attachments_attachments1_idx` (`attachments_id` ASC) VISIBLE,
  INDEX `fk_posts_has_attachments_posts1_idx` (`posts_id` ASC) VISIBLE,
  CONSTRAINT `fk_posts_has_attachments_posts1`
    FOREIGN KEY (`posts_id`)
    REFERENCES `docker`.`posts` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_has_attachments_attachments1`
    FOREIGN KEY (`attachments_id`)
    REFERENCES `docker`.`attachments` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`user_favourite_answers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`user_favourite_answers` ;

CREATE TABLE IF NOT EXISTS `docker`.`user_favourite_answers` (
  `answer` INT NOT NULL,
  `user` INT NOT NULL,
  PRIMARY KEY (`answer`, `user`),
  INDEX `fk_post_answers_has_users_users1_idx` (`user` ASC) VISIBLE,
  INDEX `fk_post_answers_has_users_post_answers1_idx` (`answer` ASC) VISIBLE,
  CONSTRAINT `fk_post_answers_has_users_post_answers1`
    FOREIGN KEY (`answer`)
    REFERENCES `docker`.`post_answers` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_post_answers_has_users_users1`
    FOREIGN KEY (`user`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`post_upvotes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`post_upvotes` ;

CREATE TABLE IF NOT EXISTS `docker`.`post_upvotes` (
  `user` INT NOT NULL,
  `post_answer` INT NOT NULL,
  PRIMARY KEY (`user`),
  INDEX `fk_users_has_posts_users1_idx` (`user` ASC) VISIBLE,
  INDEX `fk_post_upvotes_post_answers1_idx` (`post_answer` ASC) VISIBLE,
  CONSTRAINT `fk_users_has_posts_users1`
    FOREIGN KEY (`user`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_post_upvotes_post_answers1`
    FOREIGN KEY (`post_answer`)
    REFERENCES `docker`.`post_answers` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docker`.`notifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docker`.`notifications` ;

CREATE TABLE IF NOT EXISTS `docker`.`notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(256) NOT NULL DEFAULT '',
  `dismissed` TINYINT(1) NOT NULL DEFAULT 0,
  `href` VARCHAR(512) NOT NULL DEFAULT '',
  `type` INT NOT NULL DEFAULT 0,
  `user` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_notifications_users1_idx` (`user` ASC) VISIBLE,
  CONSTRAINT `fk_notifications_users1`
    FOREIGN KEY (`user`)
    REFERENCES `docker`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Data for table `docker`.`attachments`
-- -----------------------------------------------------
START TRANSACTION;
USE `docker`;
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (-1, 'default.png', 'default.png', 'image/png', '2022-11-11 10:03:12', NULL, -1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (1, 'AlexPfp.gif', 'AlexPfp.gif', 'image/gif', '2022-11-11 10:03:12', 1, 1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (2, 'DamianPfp.gif', 'DamianPfp.gif', 'image/gif', '2022-11-11 10:03:12', 2, 1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (3, 'JulenPfp.jpg', 'JulenPfp.jpg', 'image/jpg', '2022-11-11 10:03:12', 3, 1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (5, 'LauraPfp.gif', 'LauraPfp.gif', 'image/gif', '2022-11-11 10:03:12', 5, 1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (6, 'RaquelPfp.webp', 'RaquelPfp.webp', 'image/webp', '2022-11-11 10:03:12', 6, 1, 0);
INSERT INTO `docker`.`attachments` (`id`, `filename`, `filepath`, `content_type`, `uploaded_at`, `uploaded_by`, `public`, `is_tutorial`) VALUES (4, 'YerayPfp.jpeg', 'YerayPfp.jpeg', 'image/jpeg', '2022-11-11 10:03:12', 4, 1, 0);

COMMIT;


-- -----------------------------------------------------
-- Data for table `docker`.`users`
-- -----------------------------------------------------
START TRANSACTION;
USE `docker`;
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (1, 'alex', 'alex.cortes@ikasle.egibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 1, 'Full-stack developer para AERGIBIDE', 1, 1, 0, 1, 'UHQUFWGGHFL5NHO2', DEFAULT);
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (2, 'damian', 'damian.romero@ikasle.egibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 2, 'Backend developer para AERGIBIDE', 1, 1, 0, 2, DEFAULT, DEFAULT);
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (3, 'julen', 'julen.martinez@ikasle.egibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 3, 'Frontend developer para AERGIBIDE', 1, 1, 0, 0, DEFAULT, DEFAULT);
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (4, 'yeray', 'yeray.bote@ikasle.egibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 4, 'Supervisor del departamento de I+D', 1, 0, 0, 0, '', DEFAULT);
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (5, 'laura', 'laura.espinosa@aergibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 5, 'Empleada del equipo de I+D', 1, 0, 0, 0, '', DEFAULT);
INSERT INTO `docker`.`users` (`id`, `username`, `email`, `password`, `type`, `profile_pic`, `profile_description`, `active`, `email_verified`, `points`, `mfa_type`, `mfa_data`, `password_reset_token`) VALUES (6, 'raquel', 'raquel.martin@aergibide.org', '$2y$10$pAO5CX2Ima57UDp7h98uouLxthK/.wlpyZ0kgtf1VxeIhPM5eimVW', 0, 6, 'Becaria del departamento de I+D', 1, 0, 0, 0, '', DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `docker`.`achievements`
-- -----------------------------------------------------
START TRANSACTION;
USE `docker`;
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, '¡Ayuda!', 'Has creado tu primer post', 5, -1, '{\"type\" : \"postQuantity\", \"data\" : 1}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Dudoso ocasional', 'Has creado cinco posts', 10, -1, '{\"type\" : \"postQuantity\", \"data\" : 5}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Dudoso compulsivo', 'Has creado diez posts', 25, -1, '{\"type\" : \"postQuantity\", \"data\" : 10}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'El Dudas', 'Has creado veinte posts', 40, -1, '{\"type\" : \"postQuantity\", \"data\" : 20}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'No se preocupen, yo le pregunté', 'Has creado cincuenta posts', 100, -1, '{\"type\" : \"postQuantity\", \"data\" : 50}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'No hay de qué', 'Has respondido a tu primera pregunta', 5, -1, '{\"type\" : \"postAnswerQuantity\", \"data\" : 1}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Asistente amateur', 'Has respondido a cinco preguntas', 10, -1, '{\"type\" : \"postAnswerQuantity\", \"data\" : 5}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Asistente experto', 'Has respondido a diez preguntas', 25, -1, '{\"type\" : \"postAnswerQuantity\", \"data\" : 10}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Rey de los asistentes', 'Has respondido a veinte preguntas', 40, -1, '{\"type\" : \"postAnswerQuantity\", \"data\" : 20}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Stackoverflow', 'Has respondido a cincuenta preguntas', 100, -1, '{\"type\" : \"postAnswerQuantity\", \"data\" : 50}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, '¡Soy famoso!', 'Has recibido tu primer upvote', 5, -1, '{\"type\" : \"upvoteQuantity\", \"data\" : 1}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Subiendo como espuma', 'Has recibido cinco upvotes', 10, -1, '{\"type\" : \"upvoteQuantity\", \"data\" : 5}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Mamá, soy famoso', 'Has recibido diez upvotes', 25, -1, '{\"type\" : \"upvoteQuantity\", \"data\" : 10}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'Estrella del blog', 'Has recibido veinte upvotes', 40, -1, '{\"type\" : \"upvoteQuantity\", \"data\" : 20}');
INSERT INTO `docker`.`achievements` (`id`, `title`, `description`, `points_awarded`, `photo`, `requirements`) VALUES (DEFAULT, 'El Ibai de WTFAQ', 'Has recibido cincuenta upvotes', 100, -1, '{\"type\" : \"upvoteQuantity\", \"data\" : 50}');

COMMIT;


-- -----------------------------------------------------
-- Data for table `docker`.`post_topics`
-- -----------------------------------------------------
START TRANSACTION;
USE `docker`;
INSERT INTO `docker`.`post_topics` (`id`, `name`, `description`) VALUES (1, 'Informática', 'Tema con todo lo relacionado con la informática de las oficinas (Impresoras, equipos...)');
INSERT INTO `docker`.`post_topics` (`id`, `name`, `description`) VALUES (2, 'Software', 'Tema acerca de todo el software que utilizamos en las oficinas');
INSERT INTO `docker`.`post_topics` (`id`, `name`, `description`) VALUES (3, 'Domótica', 'Tema que cubre la domótica recientemente instalada de las oficinas');
INSERT INTO `docker`.`post_topics` (`id`, `name`, `description`) VALUES (4, 'Offtopic', 'Tema para todo lo que no cubren el resto de temas');

COMMIT;


-- -----------------------------------------------------
-- Data for table `docker`.`posts`
-- -----------------------------------------------------
START TRANSACTION;
USE `docker`;
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (1, '¿Cómo funciona la nueva impresora de la oficina?', 'Hola soy Raquel, la becaria. Me acaban de transferir al departamento de I+D y me preguntaba cómo se utilizaba la nueva impresora, ya que al intentar iniciar sesión con mi cuenta, dice que mi contraseña es incorrecta.', 12, 1, 6, 1, '2022-11-11 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (2, 'Licencia Office expirada', 'Buenos días, soy Laura, empleada del equipo de I+D. Hace cosa de 2 días mi licencia de Office expiró y me siguen saliendo \'pop-ups\'. Al darles a aceptar, me piden una clave de licencia la cuál no tengo. Gracias', 14, 2, 5, 1, '2022-11-14 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (3, 'Máquina de café averiada', 'Soy Alex, el desarrollador full-stack. Ponía este post para preguntar si habíais solucionado lo de la máquina de café del primer piso. La última vez que pasé por allí, escuchaba cómo caían monedas sin parar porque alguien había pagado con el llavero NFC que tenemos todos. Cualquier respuesta es bienvenida, gracias.', 32, 4, 1, 1, '2022-11-08 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (4, '¿Cómo se apaga el ordenador?', 'Soy Julen, el programador frontend. Parecerá que es una broma, pero al cambiar los equipos de las oficinas a los últimos modelos de Mac, no acabo de encontrar dónde se encuentra el botón de apagar. Cada vez que quiero apagarlo, tengo que darle al botón físico detrás de la pantalla', 7, 1, 3, 1, '2022-11-20 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (5, 'Credenciales inválidas', 'Buenos días, soy Damián. Estoy teniendo problemas a la hora de validarme en mi puesto. Al introducir las credenciales para fichar y que contabilice que he entrado al trabajo, me salta un error de verificación que no me deja iniciar sesión.', 5, 3, 2, 0, '2022-11-23 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (6, '¿Cómo guardo mis archivos en AERGIBIDE?', 'Hola, soy Yeray, el supervisor de I+D. Ha llegado a mis oídos información de que próximamente en nuestro perfil, tendremos un espacio para guardas nuestros propios archivos y tenerlos guardados en la nube. Me preguntaba si esos rumores eran ciertos y para cuándo estaría, ya que me agilizaría mucho el trabajo. Gracias de antemano', 2, 2, 4, 1, '2022-11-24 10:03:12');
INSERT INTO `docker`.`posts` (`id`, `title`, `description`, `views`, `topic`, `author`, `active`, `date`) VALUES (7, 'Interferencias entre redes', 'Hola, soy Laura de nuevo. Mando este post porque me preguntaba cuál es la distribución de la redes, ya que al intentar conectarme al NAS, me devuelve un error de conexión y no me deja iniciar sesión. Al intentar hacer ping, no me devuelve nada. Sospecho que el problema es que tenga la IP errónea, así que agradecería un esquema mostrando toda la red. Muchas gracias de antemano.', 56, 2, 5, 1, '2022-11-06 10:03:12');

COMMIT;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

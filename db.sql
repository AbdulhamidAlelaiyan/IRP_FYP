ALTER TABLE `books_authors` DROP FOREIGN KEY `books_authors_ibfk_1`;
ALTER TABLE `books_authors` DROP FOREIGN KEY `books_authors_ibfk_2`;
ALTER TABLE `books_content` DROP FOREIGN KEY `books_content_ibfk_1`;
ALTER TABLE `books_content` DROP FOREIGN KEY `books_content_ibfk_2`;
ALTER TABLE `books_information` DROP FOREIGN KEY `books_information_ibfk_1`;
ALTER TABLE `posts` DROP FOREIGN KEY `posts_ibfk_1`;
ALTER TABLE `posts` DROP FOREIGN KEY `posts_ibfk_2`;
ALTER TABLE `posts_points` DROP FOREIGN KEY `posts_points_ibfk_1`;
ALTER TABLE `posts_points` DROP FOREIGN KEY `posts_points_ibfk_2`;
ALTER TABLE `quizes` DROP FOREIGN KEY `quizes_ibfk_1`;
ALTER TABLE `quizes_questions` DROP FOREIGN KEY `quizes_questions_ibfk_1`;
ALTER TABLE `quizes_questions` DROP FOREIGN KEY `quizes_questions_ibfk_2`;
ALTER TABLE `remembered_logins` DROP FOREIGN KEY `remembered_logins_ibfk_1`;
ALTER TABLE `replies` DROP FOREIGN KEY `replies_ibfk_1`;
ALTER TABLE `replies` DROP FOREIGN KEY `replies_ibfk_2`;
ALTER TABLE `replies_points` DROP FOREIGN KEY `replies_points_ibfk_1`;
ALTER TABLE `replies_points` DROP FOREIGN KEY `replies_points_ibfk_2`;
ALTER TABLE `scores` DROP FOREIGN KEY `scores_ibfk_1`;
ALTER TABLE `scores` DROP FOREIGN KEY `scores_ibfk_2`;

DROP INDEX `ISBN` ON `books_authors`;
DROP INDEX `isbn` ON `books_content`;
DROP INDEX `quiz_id` ON `books_content`;
DROP INDEX `quiz_id` ON `books_information`;
DROP INDEX `user_id` ON `posts`;
DROP INDEX `isbn` ON `posts`;
DROP INDEX `user_id` ON `posts_points`;
DROP INDEX `post_id` ON `posts_points`;
DROP INDEX `isbn` ON `quizes`;
DROP INDEX `quiz_id` ON `quizes_questions`;
DROP INDEX `question_id` ON `quizes_questions`;
DROP INDEX `user_id` ON `remembered_logins`;
DROP INDEX `post_id` ON `replies`;
DROP INDEX `user_id` ON `replies`;
DROP INDEX `user_id` ON `replies_points`;
DROP INDEX `reply_id` ON `replies_points`;
DROP INDEX `user_id` ON `scores`;
DROP INDEX `question_id` ON `scores`;
DROP INDEX `email` ON `users`;
DROP INDEX `password_reset_hash` ON `users`;
DROP INDEX `activation_hash` ON `users`;

DROP TABLE `books_authors`;
DROP TABLE `books_content`;
DROP TABLE `books_information`;
DROP TABLE `posts`;
DROP TABLE `posts_points`;
DROP TABLE `questions`;
DROP TABLE `quizes`;
DROP TABLE `quizes_questions`;
DROP TABLE `remembered_logins`;
DROP TABLE `replies`;
DROP TABLE `replies_points`;
DROP TABLE `scores`;
DROP TABLE `users`;

CREATE TABLE `books_authors` (
`id` int(11) NOT NULL,
`ISBN` char(10) NULL DEFAULT NULL,
PRIMARY KEY (`id`) ,
INDEX `ISBN` (`ISBN` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `books_content` (
`isbn` char(10) NOT NULL,
`chapter` tinyint(4) NOT NULL,
`title` varchar(255) NOT NULL,
`content` longtext NOT NULL,
`video_url` varchar(255) NOT NULL,
`quiz_id` int(11) NOT NULL,
INDEX `isbn` (`isbn` ASC) USING BTREE,
INDEX `quiz_id` (`quiz_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `books_information` (
`isbn` char(10) NOT NULL,
`title` varchar(255) NOT NULL,
`publication_date` datetime NOT NULL,
`edition` tinyint(4) NOT NULL,
`quiz_id` int(11) NOT NULL,
PRIMARY KEY (`isbn`) ,
INDEX `quiz_id` (`quiz_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `posts` (
`id` int(11) NOT NULL,
`isbn` char(10) NOT NULL,
`title` varchar(255) NOT NULL,
`body` text NOT NULL,
`user_id` int(11) NOT NULL,
`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`) ,
INDEX `user_id` (`user_id` ASC) USING BTREE,
INDEX `isbn` (`isbn` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `posts_points` (
`user_id` int(11) NOT NULL,
`post_id` int(11) NOT NULL,
`point` tinyint(1) NOT NULL,
INDEX `user_id` (`user_id` ASC) USING BTREE,
INDEX `post_id` (`post_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `questions` (
`id` int(11) NOT NULL,
`question` varchar(255) NOT NULL,
`choice-1` varchar(255) NOT NULL,
`choice-2` varchar(255) NOT NULL,
`choice-3` varchar(255) NULL DEFAULT NULL,
`choice-4` varchar(255) NULL DEFAULT NULL,
`answer` tinyint(4) NOT NULL,
PRIMARY KEY (`id`) 
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `quizes` (
`id` int(11) NOT NULL,
`isbn` char(10) NOT NULL,
`chapter` tinyint(4) NULL DEFAULT NULL,
PRIMARY KEY (`id`) ,
INDEX `isbn` (`isbn` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `quizes_questions` (
`quiz_id` int(11) NOT NULL,
`question_id` int(11) NOT NULL,
INDEX `quiz_id` (`quiz_id` ASC) USING BTREE,
INDEX `question_id` (`question_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `remembered_logins` (
`token_hash` varchar(64) NOT NULL,
`user_id` int(11) NOT NULL,
`expires_at` datetime NOT NULL,
PRIMARY KEY (`token_hash`) ,
INDEX `user_id` (`user_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
ROW_FORMAT = dynamic;
CREATE TABLE `replies` (
`id` int(11) NOT NULL,
`text` text NOT NULL,
`post_id` int(11) NOT NULL,
`user_id` int(11) NULL DEFAULT NULL,
`created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`) ,
INDEX `post_id` (`post_id` ASC) USING BTREE,
INDEX `user_id` (`user_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `replies_points` (
`user_id` int(11) NOT NULL,
`reply_id` int(11) NOT NULL,
`point` tinyint(1) NOT NULL,
INDEX `user_id` (`user_id` ASC) USING BTREE,
INDEX `reply_id` (`reply_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `scores` (
`user_id` int(11) NOT NULL,
`question_id` int(11) NOT NULL,
`score` tinyint(1) NOT NULL,
INDEX `user_id` (`user_id` ASC) USING BTREE,
INDEX `question_id` (`question_id` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 0
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
ROW_FORMAT = dynamic;
CREATE TABLE `users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL,
`email` varchar(255) NOT NULL,
`password_hash` varchar(255) NOT NULL,
`password_reset_hash` varchar(64) NULL DEFAULT NULL,
`password_reset_expires_at` datetime NULL DEFAULT NULL,
`activation_hash` varchar(64) NULL DEFAULT NULL,
`is_active` tinyint(1) NOT NULL DEFAULT 0,
`type` varchar(255) NOT NULL DEFAULT 'reader',
PRIMARY KEY (`id`) ,
UNIQUE INDEX `email` (`email` ASC) USING BTREE,
UNIQUE INDEX `password_reset_hash` (`password_reset_hash` ASC) USING BTREE,
UNIQUE INDEX `activation_hash` (`activation_hash` ASC) USING BTREE
)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
ROW_FORMAT = dynamic;

ALTER TABLE `books_authors` ADD CONSTRAINT `books_authors_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `books_authors` ADD CONSTRAINT `books_authors_ibfk_2` FOREIGN KEY (`ISBN`) REFERENCES `books_information` (`isbn`) ON UPDATE CASCADE;
ALTER TABLE `books_content` ADD CONSTRAINT `books_content_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books_information` (`isbn`) ON UPDATE CASCADE;
ALTER TABLE `books_content` ADD CONSTRAINT `books_content_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizes` (`id`) ON UPDATE CASCADE;
ALTER TABLE `books_information` ADD CONSTRAINT `books_information_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizes` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts` ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `posts` ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books_information` (`isbn`) ON UPDATE CASCADE;
ALTER TABLE `posts_points` ADD CONSTRAINT `posts_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts_points` ADD CONSTRAINT `posts_points_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE;
ALTER TABLE `quizes` ADD CONSTRAINT `quizes_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books_information` (`isbn`) ON UPDATE CASCADE;
ALTER TABLE `quizes_questions` ADD CONSTRAINT `quizes_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizes` (`id`) ON UPDATE CASCADE;
ALTER TABLE `quizes_questions` ADD CONSTRAINT `quizes_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON UPDATE CASCADE;
ALTER TABLE `remembered_logins` ADD CONSTRAINT `remembered_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `replies` ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE;
ALTER TABLE `replies` ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `replies_points` ADD CONSTRAINT `replies_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `replies_points` ADD CONSTRAINT `replies_points_ibfk_2` FOREIGN KEY (`reply_id`) REFERENCES `replies` (`id`) ON UPDATE CASCADE;
ALTER TABLE `scores` ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
ALTER TABLE `scores` ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON UPDATE CASCADE;


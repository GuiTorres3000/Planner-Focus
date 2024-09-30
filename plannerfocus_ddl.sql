
CREATE TABLE blockpages
(
  id_blockpage int(10)      NOT NULL AUTO_INCREMENT,
  title        varchar(50)  NOT NULL,
  description  varchar(255) NULL     DEFAULT NULL,
  background   varchar(255) NULL     DEFAULT NULL,
  id_user      int(10)      NULL     DEFAULT NULL,
  PRIMARY KEY (id_blockpage)
);

CREATE TABLE blocksites
(
  id_blocksite int(10) NOT NULL AUTO_INCREMENT,
  url          text    NOT NULL,
  id_user      int(10) NOT NULL,
  id_category  int(10) NOT NULL,
  PRIMARY KEY (id_blocksite)
);

CREATE TABLE categories
(
  id_category int(10)      NOT NULL AUTO_INCREMENT,
  title       varchar(255) NOT NULL,
  color       varchar(10)  NOT NULL,
  id_user     int(10)      NOT NULL,
  PRIMARY KEY (id_category)
);

CREATE TABLE tags
(
  id_tag  int(10)     NOT NULL AUTO_INCREMENT,
  title   varchar(30) NOT NULL,
  color   varchar(7)  NOT NULL,
  id_user int(10)     NOT NULL,
  PRIMARY KEY (id_tag)
);

CREATE TABLE tasks
(
  id_task     int(10)      NOT NULL AUTO_INCREMENT,
  title       varchar(255) NOT NULL,
  date        date         NULL     DEFAULT NULL,
  time        time         NULL     DEFAULT NULL,
  description text         NULL     DEFAULT NULL,
  id_user     int(10)      NOT NULL,
  PRIMARY KEY (id_task)
);

CREATE TABLE tasks_tags
(
  id_task int(10) NOT NULL,
  id_tag  int(10) NOT NULL,
  PRIMARY KEY (id_task, id_tag)
);

CREATE TABLE users
(
  id_user  int(10)      NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  email    varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  picture  varchar(255) NULL     DEFAULT NULL,
  PRIMARY KEY (id_user)
);

ALTER TABLE users
  ADD CONSTRAINT UQ_email UNIQUE (email);

ALTER TABLE blockpages
  ADD CONSTRAINT FK_users_TO_blockpages
    FOREIGN KEY (id_user)
    REFERENCES users (id_user);

ALTER TABLE tasks_tags
  ADD CONSTRAINT FK_tags_TO_tasks_tags
    FOREIGN KEY (id_tag)
    REFERENCES tags (id_tag);

ALTER TABLE tags
  ADD CONSTRAINT FK_users_TO_tags
    FOREIGN KEY (id_user)
    REFERENCES users (id_user);

ALTER TABLE tasks_tags
  ADD CONSTRAINT FK_tasks_TO_tasks_tags
    FOREIGN KEY (id_task)
    REFERENCES tasks (id_task);

ALTER TABLE categories
  ADD CONSTRAINT FK_users_TO_categories
    FOREIGN KEY (id_user)
    REFERENCES users (id_user);

ALTER TABLE blocksites
  ADD CONSTRAINT FK_users_TO_blocksites
    FOREIGN KEY (id_user)
    REFERENCES users (id_user);

ALTER TABLE tasks
  ADD CONSTRAINT FK_users_TO_tasks
    FOREIGN KEY (id_user)
    REFERENCES users (id_user);

ALTER TABLE blocksites
  ADD CONSTRAINT FK_categories_TO_blocksites
    FOREIGN KEY (id_category)
    REFERENCES categories (id_category);

CREATE TABLE category (id BIGINT AUTO_INCREMENT, title VARCHAR(255) NOT NULL, description VARCHAR(255), slug VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX sluggable_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE document_version (id BIGINT, title VARCHAR(255) NOT NULL, description LONGTEXT, file VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, version BIGINT, PRIMARY KEY(id, version)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE document_index (id BIGINT, keyword VARCHAR(200), field VARCHAR(50), position BIGINT, PRIMARY KEY(id, keyword, field, position)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE document (id BIGINT AUTO_INCREMENT, title VARCHAR(255) NOT NULL, description LONGTEXT, file VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, version BIGINT, slug VARCHAR(255), UNIQUE INDEX sluggable_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE document_category (document_id BIGINT, category_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(document_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE document_tag (document_id BIGINT, tag_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(document_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE tag (id BIGINT AUTO_INCREMENT, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255), UNIQUE INDEX sluggable_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE user (id BIGINT AUTO_INCREMENT, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255), UNIQUE INDEX sluggable_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE user_category (user_id BIGINT, category_id BIGINT, subscribe TINYINT(1), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
CREATE TABLE user_document (document_id BIGINT, user_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(document_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB;
ALTER TABLE document_version ADD CONSTRAINT document_version_id_document_id FOREIGN KEY (id) REFERENCES document(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE document_index ADD CONSTRAINT document_index_id_document_id FOREIGN KEY (id) REFERENCES document(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE document_category ADD CONSTRAINT document_category_document_id_document_id FOREIGN KEY (document_id) REFERENCES document(id);
ALTER TABLE document_category ADD CONSTRAINT document_category_category_id_category_id FOREIGN KEY (category_id) REFERENCES category(id);
ALTER TABLE document_tag ADD CONSTRAINT document_tag_tag_id_tag_id FOREIGN KEY (tag_id) REFERENCES tag(id);
ALTER TABLE document_tag ADD CONSTRAINT document_tag_document_id_document_id FOREIGN KEY (document_id) REFERENCES document(id);
ALTER TABLE user_category ADD CONSTRAINT user_category_user_id_user_id FOREIGN KEY (user_id) REFERENCES user(id);
ALTER TABLE user_category ADD CONSTRAINT user_category_category_id_category_id FOREIGN KEY (category_id) REFERENCES category(id);
ALTER TABLE user_document ADD CONSTRAINT user_document_user_id_user_id FOREIGN KEY (user_id) REFERENCES user(id);
ALTER TABLE user_document ADD CONSTRAINT user_document_document_id_document_id FOREIGN KEY (document_id) REFERENCES document(id);

-- -----------------------------------------------------
-- Agasti Core - Full DB Schema
-- v1
-- 20100712
-- -----------------------------------------------------



-- -----------------------------------------------------
-- Table ag_person
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person (
  person_uid BIGINT(20) NOT NULL ,
  created_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'All person types (staff, volunteer, client, victim, etc.)';


-- -----------------------------------------------------
-- Table ag_account_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_account_status (
  account_status_uid SMALLINT(6) NOT NULL ,
  account_status VARCHAR(30) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (account_status_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_account_status_unq ON ag_account_status (account_status ASC) ;


-- -----------------------------------------------------
-- Table ag_accounts
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_accounts (
  account_uuid INT(11) NOT NULL AUTO_INCREMENT ,
  account_name VARCHAR(64) NOT NULL ,
  account_status_uid SMALLINT(6) NOT NULL ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  created_on DATETIME NOT NULL ,
  PRIMARY KEY (account_uuid) ,
  CONSTRAINT fk_ag_accounts_1
    FOREIGN KEY (account_status_uid )
    REFERENCES ag_account_status (account_status_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_accounts_unq ON ag_accounts (account_name ASC) ;

CREATE INDEX fk_ag_accounts_1 ON ag_accounts (account_status_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_account_mj_ag_person
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_account_mj_ag_person (
  account_uid INT(11) NOT NULL ,
  person_uid BIGINT(20) NOT NULL ,
  PRIMARY KEY (account_uid, person_uid) ,
  CONSTRAINT fk_ag_account_mj_ag_person_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_account_mj_ag_person_2
    FOREIGN KEY (account_uid )
    REFERENCES ag_accounts (account_uuid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_account_mj_ag_person_1 ON ag_account_mj_ag_person (person_uid ASC) ;

CREATE INDEX fk_ag_account_mj_ag_person_2 ON ag_account_mj_ag_person (account_uid ASC) ;


-- -----------------------------------------------------
-- Table sf_guard_user
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS sf_guard_user (
  id INT(11) NOT NULL AUTO_INCREMENT ,
  username VARCHAR(128) NOT NULL ,
  algorithm VARCHAR(128) NOT NULL DEFAULT 'sha1' ,
  salt VARCHAR(128) NULL DEFAULT NULL ,
  password VARCHAR(128) NULL DEFAULT NULL ,
  is_active TINYINT(4) NULL DEFAULT '0' ,
  is_super_admin TINYINT(4) NULL DEFAULT '0' ,
  last_login DATETIME NULL DEFAULT NULL ,
  created_at DATETIME NOT NULL ,
  updated_at DATETIME NOT NULL ,
  PRIMARY KEY (id) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Table from symfony\'s sfguardplugin.';


-- -----------------------------------------------------
-- Table ag_account_to_sf_guard_user
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_account_to_sf_guard_user (
  account_uid INT(11) NOT NULL ,
  sf_guard_user_id INT(11) NOT NULL ,
  PRIMARY KEY (account_uid) ,
  CONSTRAINT fk_ag_account_to_sf_guard_user_1
    FOREIGN KEY (account_uid )
    REFERENCES ag_accounts (account_uuid ),
  CONSTRAINT fk_ag_account_to_sf_guard_user_2
    FOREIGN KEY (sf_guard_user_id )
    REFERENCES sf_guard_user (id )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_account_to_sf_guard_user_unq ON ag_account_to_sf_guard_user (sf_guard_user_id ASC) ;

CREATE INDEX fk_ag_account_to_sf_guard_user_1 ON ag_account_to_sf_guard_user (account_uid ASC) ;

CREATE INDEX fk_ag_account_to_sf_guard_user_2 ON ag_account_to_sf_guard_user (sf_guard_user_id ASC) ;


-- -----------------------------------------------------
-- Table ag_nation
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_nation (
  nation_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  nation VARCHAR(128) NOT NULL ,
  nationality VARCHAR(128) NULL DEFAULT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (nation_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_country_unq ON ag_nation (nation ASC) ;


-- -----------------------------------------------------
-- Table ag_address_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_address_type (
  address_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  address_type VARCHAR(64) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL COMMENT 'Country, City, street address 1, province, municipal' ,
  description VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (address_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin
COMMENT = 'Home, work, recreational home, etc.';


-- -----------------------------------------------------
-- Table ag_address_format
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_address_format (
  country_uid SMALLINT(6) NOT NULL ,
  address_type_uid SMALLINT(6) NOT NULL ,
  line_sequence TINYINT(4) NOT NULL ,
  inline_sequence TINYINT(4) NOT NULL ,
  ending_delimiter CHAR(1) NOT NULL ,
  PRIMARY KEY (country_uid, address_type_uid) ,
  CONSTRAINT fk_ag_address_format_1
    FOREIGN KEY (country_uid )
    REFERENCES ag_nation (nation_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_address_format_2
    FOREIGN KEY (address_type_uid )
    REFERENCES ag_address_type (address_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'International address layout.';

CREATE INDEX fk_ag_address_format_1 ON ag_address_format (country_uid ASC) ;

CREATE INDEX fk_ag_address_format_2 ON ag_address_format (address_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_audit_message
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_audit_message (
  audit_message_uid INT(11) NOT NULL AUTO_INCREMENT ,
  audit_message TEXT NOT NULL ,
  PRIMARY KEY (audit_message_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table ag_audit_sql
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_audit_sql (
  audit_sql_uid INT(11) NOT NULL AUTO_INCREMENT ,
  audit_sql TEXT NOT NULL ,
  PRIMARY KEY (audit_sql_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table ag_audit
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_audit (
  ag_audit_uid INT(11) NOT NULL AUTO_INCREMENT ,
  audit_message_uid INT(11) NOT NULL ,
  audit_sql_uid INT(11) NOT NULL ,
  created_on DATETIME NOT NULL ,
  created_by BIGINT(20) NOT NULL ,
  PRIMARY KEY (ag_audit_uid) ,
  CONSTRAINT fk_audit_1
    FOREIGN KEY (audit_message_uid )
    REFERENCES ag_audit_message (audit_message_uid ),
  CONSTRAINT fk_audit_2
    FOREIGN KEY (audit_sql_uid )
    REFERENCES ag_audit_sql (audit_sql_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_audit_1 ON ag_audit (audit_message_uid ASC) ;

CREATE INDEX fk_audit_2 ON ag_audit (audit_sql_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_email_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_email_contact_type (
  email_contact_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  email_contact_type VARCHAR(30) NOT NULL ,
  app_display BIT(1) NOT NULL ,
  PRIMARY KEY (email_contact_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_email_contact_type_unq ON ag_email_contact_type (email_contact_type ASC) ;


-- -----------------------------------------------------
-- Table ag_ethnicity
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_ethnicity (
  ethnicity_uid INT(11) NOT NULL AUTO_INCREMENT ,
  ethnicity VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (ethnicity_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_ethnicity_unq ON ag_ethnicity (ethnicity ASC) ;


-- -----------------------------------------------------
-- Table ag_server
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_server (
  server_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  server_name VARCHAR(64) NOT NULL ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (server_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_server_unq ON ag_server (server_name ASC) ;


-- -----------------------------------------------------
-- Table ag_ip_address_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_ip_address_type (
  ip_address_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  ip_address_type VARCHAR(32) NOT NULL ,
  view BIT(1) NOT NULL DEFAULT b'1' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (ip_address_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_ip_address_type_unq ON ag_ip_address_type (ip_address_type ASC) ;


-- -----------------------------------------------------
-- Table ag_server_ip_address
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_server_ip_address (
  server_uid SMALLINT(6) NOT NULL ,
  ip_address_type_uid SMALLINT(6) NOT NULL ,
  ip_address VARCHAR(32) NOT NULL ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (server_uid, ip_address) ,
  CONSTRAINT fk_server_ip_address_1
    FOREIGN KEY (server_uid )
    REFERENCES ag_server (server_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_server_ip_address_3
    FOREIGN KEY (ip_address_type_uid )
    REFERENCES ag_ip_address_type (ip_address_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_server_ip_address_1 ON ag_server_ip_address (server_uid ASC) ;

CREATE INDEX fk_server_ip_address_3 ON ag_server_ip_address (ip_address_type_uid ASC) ;

CREATE INDEX ag_server_ip_address_ip_address_type_uid_idx ON ag_server_ip_address (ip_address_type_uid ASC) ;

CREATE INDEX ag_server_ip_address_ip_address_idx ON ag_server_ip_address (ip_address ASC) ;


-- -----------------------------------------------------
-- Table ag_global_param_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_global_param_type (
  global_param_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  global_param_type VARCHAR(32) NOT NULL COMMENT 'apache, agasti core, nyc module, etc.' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (global_param_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX version_type_unq ON ag_global_param_type (global_param_type ASC) ;


-- -----------------------------------------------------
-- Table ag_global_param
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_global_param (
  server_uid SMALLINT(6) NOT NULL ,
  ip_address VARCHAR(32) NOT NULL ,
  global_param_type_uid SMALLINT(6) NOT NULL ,
  value VARCHAR(128) NOT NULL ,
  updated_on DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (server_uid, global_param_type_uid, ip_address) ,
  CONSTRAINT fk_ag_global_param_1
    FOREIGN KEY (server_uid , ip_address )
    REFERENCES ag_server_ip_address (server_uid , ip_address )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_global_param_2
    FOREIGN KEY (global_param_type_uid )
    REFERENCES ag_global_param_type (global_param_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Instance specific info: server info, DB and software version';

CREATE INDEX fk_ag_global_param_1 ON ag_global_param (server_uid ASC, ip_address ASC) ;

CREATE INDEX fk_ag_global_param_2 ON ag_global_param (global_param_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_identification_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_identification_type (
  identification_type_uid INT(11) NOT NULL AUTO_INCREMENT ,
  identification_type VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL ,
  PRIMARY KEY (identification_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_identification_type_unq ON ag_identification_type (identification_type ASC) ;


-- -----------------------------------------------------
-- Table ag_im_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_im_contact_type (
  im_contact_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  im_contact_type VARCHAR(30) NOT NULL ,
  server_address VARCHAR(255) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (im_contact_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_im_contact_type_unq ON ag_im_contact_type (im_contact_type ASC) ;

CREATE UNIQUE INDEX ag_im_contact_type_server_address_unq ON ag_im_contact_type (server_address ASC) ;


-- -----------------------------------------------------
-- Table ag_import_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_import_type (
  import_type_uid TINYINT(4) NOT NULL ,
  import_type VARCHAR(128) NOT NULL ,
  view BIT(1) NOT NULL DEFAULT b'1' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (import_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_import_type_unq ON ag_import_type (import_type ASC) ;


-- -----------------------------------------------------
-- Table ag_import
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_import (
  import_uid INT(11) NOT NULL AUTO_INCREMENT ,
  filename INT(11) NOT NULL ,
  import_type_uid TINYINT(4) NOT NULL ,
  uploaded_by INT(11) NOT NULL ,
  uploaded_on DATETIME NOT NULL ,
  PRIMARY KEY (import_uid) ,
  CONSTRAINT fk_import_3
    FOREIGN KEY (import_type_uid )
    REFERENCES ag_import_type (import_type_uid )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_import_unq ON ag_import (import_type_uid ASC, filename ASC) ;

CREATE INDEX fk_import_3 ON ag_import (import_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_language
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_language (
  language_uid INT(11) NOT NULL AUTO_INCREMENT ,
  language VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (language_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_language_unq ON ag_language (language ASC) ;


-- -----------------------------------------------------
-- Table ag_marital_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_marital_status (
  marital_status_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  marital_status VARCHAR(30) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (marital_status_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_marital_status_unq ON ag_marital_status (marital_status ASC) ;


-- -----------------------------------------------------
-- Table ag_occupation
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_occupation (
  occupation_uid INT(11) NOT NULL AUTO_INCREMENT ,
  occupation VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (occupation_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_occupation_unq ON ag_occupation (occupation ASC) ;


-- -----------------------------------------------------
-- Table ag_person_date_of_birth
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_date_of_birth (
  person_uid BIGINT(20) NOT NULL ,
  date_of_birth DATE NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_date_of_birth_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_date_of_birth_1 ON ag_person_date_of_birth (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_external
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_external (
  person_uid BIGINT(20) NOT NULL ,
  ext_person_uuid VARCHAR(50) NOT NULL ,
  created_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_external_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Synchronize person with external database.';

CREATE INDEX fk_ag_person_external_1 ON ag_person_external (person_uid ASC) ;

CREATE INDEX ag_person_external_idx ON ag_person_external (ext_person_uuid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_type (
  person_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  person_type VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (person_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX person_type_UNIQUE ON ag_person_type (person_type ASC) ;


-- -----------------------------------------------------
-- Table ag_person_deleted
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_deleted (
  person_uid BIGINT(20) NOT NULL ,
  ext_person_uuid VARCHAR(50) NOT NULL ,
  person_type_uid SMALLINT(6) NOT NULL ,
  deleted_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, ext_person_uuid, person_type_uid) ,
  CONSTRAINT fk_ag_person_deleted_1
    FOREIGN KEY (ext_person_uuid )
    REFERENCES ag_person_external (ext_person_uuid ),
  CONSTRAINT fk_ag_person_deleted_2
    FOREIGN KEY (person_type_uid )
    REFERENCES ag_person_type (person_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX ag_person_deleted_person_uid_idx ON ag_person_deleted (person_uid ASC) ;

CREATE INDEX ag_person_deleted_ext_person_uid_idx ON ag_person_deleted (ext_person_uuid ASC) ;

CREATE INDEX fk_ag_person_deleted_1 ON ag_person_deleted (ext_person_uuid ASC) ;

CREATE INDEX fk_ag_person_deleted_2 ON ag_person_deleted (person_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_ethnicity
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_ethnicity (
  person_uid BIGINT(20) NOT NULL ,
  ethnicity_uid INT(11) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_ethnicity_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_ethnicity_2
    FOREIGN KEY (ethnicity_uid )
    REFERENCES ag_ethnicity (ethnicity_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_ethnicity_1 ON ag_person_ethnicity (person_uid ASC) ;

CREATE INDEX fk_ag_person_ethnicity_2 ON ag_person_ethnicity (ethnicity_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_import
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_import (
  person_uid BIGINT(20) NOT NULL ,
  import_uid INT(11) NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_import_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_import_1 ON ag_person_import (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_marital_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_marital_status (
  person_uid BIGINT(20) NOT NULL ,
  marital_status_uid SMALLINT(6) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_marital_status_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_marital_status_2
    FOREIGN KEY (marital_status_uid )
    REFERENCES ag_marital_status (marital_status_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_marital_status_1 ON ag_person_marital_status (person_uid ASC) ;

CREATE INDEX fk_ag_person_marital_status_2 ON ag_person_marital_status (marital_status_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_account
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_account (
  person_uid BIGINT(20) NOT NULL ,
  account_uid INT(11) NOT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, account_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_account_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_account_1 ON ag_person_mj_ag_account (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_account_person_uid_idx ON ag_person_mj_ag_account (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_email_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_email_contact_type (
  mj_person_email_uid INT(11) NOT NULL AUTO_INCREMENT ,
  person_uid BIGINT(20) NOT NULL ,
  email_contact_type_uid SMALLINT(6) NOT NULL ,
  email_contact VARCHAR(255) NOT NULL ,
  update_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_email_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_email_contact_type_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_email_contact_type_2
    FOREIGN KEY (email_contact_type_uid )
    REFERENCES ag_email_contact_type (email_contact_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_mj_ag_email_contact_type_unq ON ag_person_mj_ag_email_contact_type (person_uid ASC, email_contact_type_uid ASC, email_contact ASC) ;

CREATE INDEX ag_person_mj_ag_email_contact_type_person_uid_idx ON ag_person_mj_ag_email_contact_type (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_email_contact_type_email_contact_type_uid_idx ON ag_person_mj_ag_email_contact_type (email_contact_type_uid ASC) ;

CREATE INDEX ag_person_mj_ag_email_contact_type_email_contact_idx ON ag_person_mj_ag_email_contact_type (email_contact ASC) ;

CREATE INDEX fk_ag_person_mj_ag_email_contact_type_1 ON ag_person_mj_ag_email_contact_type (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_email_contact_type_2 ON ag_person_mj_ag_email_contact_type (email_contact_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_identification_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_identification_type (
  person_uid BIGINT(20) NOT NULL ,
  identification_type_uid INT(11) NOT NULL ,
  identification VARCHAR(50) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, identification_type_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_identification_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_identification_2
    FOREIGN KEY (identification_type_uid )
    REFERENCES ag_identification_type (identification_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Person\'s unique id (passport number, driver license, etc.)';

CREATE INDEX ag_person_mj_ag_identification_person_uid_unq ON ag_person_mj_ag_identification_type (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_identification_identification_type_uid_idx ON ag_person_mj_ag_identification_type (identification_type_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_identification_1 ON ag_person_mj_ag_identification_type (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_identification_2 ON ag_person_mj_ag_identification_type (identification_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_im_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_im_contact_type (
  mj_person_im_uid INT(11) NOT NULL ,
  person_uid BIGINT(20) NOT NULL ,
  im_contact_type_uid SMALLINT(6) NOT NULL ,
  im_contact VARCHAR(128) NOT NULL ,
  update_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_im_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_im_contact_type_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_im_contact_type_2
    FOREIGN KEY (im_contact_type_uid )
    REFERENCES ag_im_contact_type (im_contact_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_perosn_mj_ag_im_contact_type_unq ON ag_person_mj_ag_im_contact_type (person_uid ASC, im_contact_type_uid ASC, im_contact ASC) ;

CREATE INDEX fk_ag_person_mj_ag_im_contact_type_1 ON ag_person_mj_ag_im_contact_type (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_im_contact_type_2 ON ag_person_mj_ag_im_contact_type (im_contact_type_uid ASC) ;

CREATE INDEX ag_person_mj_ag_im_contact_type_person_uid_idx ON ag_person_mj_ag_im_contact_type (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_im_contact_type_im_contact_type_uid_idx ON ag_person_mj_ag_im_contact_type (im_contact_type_uid ASC) ;

CREATE INDEX ag_person_mj_ag_im_contact_type_im_contact_idx ON ag_person_mj_ag_im_contact_type (im_contact ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_language
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_language (
  person_uid BIGINT(20) NOT NULL ,
  language_uid INT(11) NOT NULL ,
  read_lang BIT(1) NOT NULL ,
  write_lang BIT(1) NOT NULL ,
  speak_lang BIT(1) NOT NULL ,
  primary_lang BIT(1) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, language_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_language_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_language_2
    FOREIGN KEY (language_uid )
    REFERENCES ag_language (language_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_language_1 ON ag_person_mj_ag_language (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_language_2 ON ag_person_mj_ag_language (language_uid ASC) ;

CREATE INDEX ag_person_mj_ag_language_person_uid_idx ON ag_person_mj_ag_language (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_nation
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_nation (
  person_uid BIGINT(20) NOT NULL ,
  nation_uid SMALLINT(6) NOT NULL COMMENT 'Sense of belonging to and legally protected by the nation.' ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, nation_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_nation_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_nation_2
    FOREIGN KEY (nation_uid )
    REFERENCES ag_nation (nation_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_nation_1 ON ag_person_mj_ag_nation (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_nation_2 ON ag_person_mj_ag_nation (nation_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_occupation
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_occupation (
  person_uid BIGINT(20) NOT NULL ,
  occupation_uid INT(11) NOT NULL ,
  PRIMARY KEY (person_uid, occupation_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_occupation_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_occupation_2
    FOREIGN KEY (occupation_uid )
    REFERENCES ag_occupation (occupation_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_occupation_1 ON ag_person_mj_ag_occupation (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_occupation_2 ON ag_person_mj_ag_occupation (occupation_uid ASC) ;

CREATE INDEX ag_person_mj_ag_occupation_person_uid_idx ON ag_person_mj_ag_occupation (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_name_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_name_type (
  person_name_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  person_name_type VARCHAR(30) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (person_name_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Name parts:  first, last, middle, maiden, alias, prefix, etc';

CREATE UNIQUE INDEX ag_person_name_type_unq ON ag_person_name_type (person_name_type ASC) ;


-- -----------------------------------------------------
-- Table ag_person_name
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_name (
  person_name_uid BIGINT(20) NOT NULL ,
  person_name VARCHAR(30) NOT NULL ,
  PRIMARY KEY (person_name_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_name_unq ON ag_person_name (person_name ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_person_name
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_person_name (
  mj_person_name_uid INT(11) NOT NULL AUTO_INCREMENT ,
  person_uid BIGINT(20) NOT NULL ,
  person_name_uid BIGINT(20) NOT NULL ,
  person_name_type_uid SMALLINT(6) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_name_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_person_name_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_person_name_2
    FOREIGN KEY (person_name_type_uid )
    REFERENCES ag_person_name_type (person_name_type_uid )
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_person_name_3
    FOREIGN KEY (person_name_uid )
    REFERENCES ag_person_name (person_name_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_mj_ag_person_name_unq ON ag_person_mj_ag_person_name (person_uid ASC, person_name_uid ASC, person_name_type_uid ASC) ;

CREATE INDEX ag_person_mj_ag_person_name_person_uid_idx ON ag_person_mj_ag_person_name (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_person_name_person_name_type_idx ON ag_person_mj_ag_person_name (person_name_type_uid ASC) ;

CREATE INDEX ag_person_mj_ag_person_name_person_name_idx ON ag_person_mj_ag_person_name (person_name_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_person_name_1 ON ag_person_mj_ag_person_name (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_person_name_2 ON ag_person_mj_ag_person_name (person_name_type_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_person_name_3 ON ag_person_mj_ag_person_name (person_name_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_phone_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_phone_contact_type (
  phone_contact_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  phone_contact_type VARCHAR(30) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (phone_contact_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_phone_contact_type_unq ON ag_phone_contact_type (phone_contact_type ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_phone_contact_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_phone_contact_type (
  mj_person_phone_uid INT(11) NOT NULL AUTO_INCREMENT ,
  person_uid BIGINT(20) NOT NULL ,
  phone_contact_type_uid SMALLINT(6) NOT NULL ,
  phone_contact INT(11) NOT NULL ,
  update_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_phone_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_phone_contact_type_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_phone_contact_type_2
    FOREIGN KEY (phone_contact_type_uid )
    REFERENCES ag_phone_contact_type (phone_contact_type_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_mj_ag_phone_contact_type_unq ON ag_person_mj_ag_phone_contact_type (person_uid ASC, phone_contact_type_uid ASC, phone_contact ASC) ;

CREATE INDEX ag_person_mj_ag_phone_contact_type_person_uid_idx ON ag_person_mj_ag_phone_contact_type (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_phone_contact_type_phone_contact_type_uid_idx ON ag_person_mj_ag_phone_contact_type (phone_contact_type_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_phone_contact_type_1 ON ag_person_mj_ag_phone_contact_type (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_phone_contact_type_2 ON ag_person_mj_ag_phone_contact_type (phone_contact_type_uid ASC) ;

CREATE INDEX ag_person_mj_phone_contact_type_phone_contact_idx ON ag_person_mj_ag_phone_contact_type (phone_contact ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_physicality
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_physicality (
  person_uid BIGINT(20) NOT NULL ,
  physicality_uid INT(11) NOT NULL ,
  PRIMARY KEY (person_uid, physicality_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_physicality_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_physicality_1 ON ag_person_mj_ag_physicality (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_religion
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_religion (
  religion_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  religion VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (religion_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_religion_unq ON ag_religion (religion ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_religion
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_religion (
  person_uid BIGINT(20) NOT NULL ,
  religion_uid SMALLINT(6) NOT NULL ,
  PRIMARY KEY (person_uid, religion_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_religion_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_religion_2
    FOREIGN KEY (religion_uid )
    REFERENCES ag_religion (religion_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_religion_1 ON ag_person_mj_ag_religion (person_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_religion_2 ON ag_person_mj_ag_religion (religion_uid ASC) ;

CREATE INDEX ag_person_mj_ag_religion_person_uid_idx ON ag_person_mj_ag_religion (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_role
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_role (
  person_uid BIGINT(20) NOT NULL ,
  role_uid INT(11) NOT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, role_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_role_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_role_1 ON ag_person_mj_ag_role (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_role_person_uid_idx ON ag_person_mj_ag_role (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_site
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site (
  site_uid INT(11) NOT NULL AUTO_INCREMENT ,
  created_on DATETIME NOT NULL ,
  PRIMARY KEY (site_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'All Sites: med/control/food/supply center, shelter, etc.';


-- -----------------------------------------------------
-- Table ag_person_mj_ag_site
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_site (
  mj_person_address_uid INT(11) NOT NULL AUTO_INCREMENT ,
  person_uid BIGINT(20) NOT NULL ,
  site_uid INT(11) NOT NULL ,
  address_type_uid SMALLINT(6) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_address_uid) ,
  CONSTRAINT fk_ag_person_mj_ag_site_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_site_2
    FOREIGN KEY (site_uid )
    REFERENCES ag_site (site_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_mj_ag_site_3
    FOREIGN KEY (address_type_uid )
    REFERENCES ag_address_type (address_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_mj_ag_site_unq ON ag_person_mj_ag_site (person_uid ASC, site_uid ASC, address_type_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_site_1 ON ag_person_mj_ag_site (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_site_person_uid_idx ON ag_person_mj_ag_site (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_site_site_uid_idx ON ag_person_mj_ag_site (site_uid ASC) ;

CREATE INDEX ag_person_mj_ag_site_address_type_idx ON ag_person_mj_ag_site (address_type_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_site_2 ON ag_person_mj_ag_site (site_uid ASC) ;

CREATE INDEX fk_ag_person_mj_ag_site_3 ON ag_person_mj_ag_site (address_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_mj_ag_special_needs
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_mj_ag_special_needs (
  person_uid BIGINT(20) NOT NULL ,
  special_needs INT(11) NOT NULL ,
  PRIMARY KEY (person_uid, special_needs) ,
  CONSTRAINT fk_ag_person_mj_ag_special_needs_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_mj_ag_special_needs_1 ON ag_person_mj_ag_special_needs (person_uid ASC) ;

CREATE INDEX ag_person_mj_ag_special_needs_person_uid_idx ON ag_person_mj_ag_special_needs (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_address
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_address (
  mj_person_address_uid INT(11) NOT NULL ,
  person_uid BIGINT(20) NOT NULL ,
  site_uid INT(11) NOT NULL ,
  address_type_uid INT(11) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (mj_person_address_uid) ,
  CONSTRAINT fk_ag_person_primary_address_1
    FOREIGN KEY (mj_person_address_uid )
    REFERENCES ag_person_mj_ag_site (mj_person_address_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_primary_address ON ag_person_primary_address (person_uid ASC, site_uid ASC, address_type_uid ASC) ;

CREATE INDEX fk_ag_person_primary_address_1 ON ag_person_primary_address (mj_person_address_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_email_contact
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_email_contact (
  person_uid BIGINT(20) NOT NULL ,
  email_contact_type_uid SMALLINT(6) NOT NULL ,
  mj_person_email_uid INT(11) NOT NULL ,
  email_contact VARCHAR(255) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (email_contact_type_uid, person_uid) ,
  CONSTRAINT fk_ag_person_primary_email_contact_1
    FOREIGN KEY (mj_person_email_uid )
    REFERENCES ag_person_mj_ag_email_contact_type (mj_person_email_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX ag_person_primary_email_contact_person_uid_idx ON ag_person_primary_email_contact (person_uid ASC) ;

CREATE INDEX ag_person_primary_email_contact_email_contact_type_uid ON ag_person_primary_email_contact (email_contact_type_uid ASC) ;

CREATE INDEX fk_ag_person_primary_email_contact_1 ON ag_person_primary_email_contact (mj_person_email_uid ASC) ;

CREATE INDEX ag_person_primary_email_contact_email_contact_type_idx ON ag_person_primary_email_contact (email_contact_type_uid ASC) ;

CREATE INDEX ag_person_primary_email_contact_idx ON ag_person_primary_email_contact (email_contact ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_im_contact
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_im_contact (
  person_uid BIGINT(20) NOT NULL ,
  im_contact_type_uid SMALLINT(6) NOT NULL ,
  mj_person_im_uid INT(11) NOT NULL ,
  im_contact VARCHAR(128) NOT NULL ,
  update_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, im_contact_type_uid) ,
  CONSTRAINT fk_ag_person_primary_im_contact_1
    FOREIGN KEY (mj_person_im_uid )
    REFERENCES ag_person_mj_ag_im_contact_type (mj_person_im_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX ag_person_primary_im_contact_person_uid_idx ON ag_person_primary_im_contact (person_uid ASC) ;

CREATE INDEX fk_ag_person_primary_im_contact_1 ON ag_person_primary_im_contact (mj_person_im_uid ASC) ;

CREATE INDEX ag_person_primary_im_contact_im_contact_type_idx ON ag_person_primary_im_contact (im_contact_type_uid ASC) ;

CREATE INDEX ag_person_primary_im_contact_idx ON ag_person_primary_im_contact (im_contact ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_language
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_language (
  person_uid BIGINT(20) NOT NULL ,
  language_uid INT(11) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_primary_language_1
    FOREIGN KEY (person_uid , language_uid )
    REFERENCES ag_person_mj_ag_language (person_uid , language_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_primary_language_1 ON ag_person_primary_language (person_uid ASC, language_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_name
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_name (
  person_uid BIGINT(20) NOT NULL ,
  person_name_type_uid SMALLINT(6) NOT NULL ,
  mj_person_name_uid INT(11) NOT NULL ,
  person_name_uid BIGINT(20) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, person_name_type_uid) ,
  CONSTRAINT fk_ag_person_primary_name_1
    FOREIGN KEY (mj_person_name_uid )
    REFERENCES ag_person_mj_ag_person_name (mj_person_name_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX ag_person_primary_name_person_uid_idx ON ag_person_primary_name (person_uid ASC) ;

CREATE INDEX ag_person_primary_name_name_type_uid_idx ON ag_person_primary_name (person_name_type_uid ASC) ;

CREATE INDEX ag_person_primary_name_person_name_uid_idx ON ag_person_primary_name (person_name_uid ASC) ;

CREATE INDEX fk_ag_person_primary_name_1 ON ag_person_primary_name (mj_person_name_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_primary_phone_contact
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_primary_phone_contact (
  person_uid BIGINT(20) NOT NULL ,
  phone_contact_type_uid SMALLINT(6) NOT NULL ,
  mj_person_phone_uid INT(11) NOT NULL ,
  phone_contact INT(11) NOT NULL ,
  update_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, phone_contact_type_uid) ,
  CONSTRAINT fk_ag_person_primary_phone_contact_1
    FOREIGN KEY (mj_person_phone_uid )
    REFERENCES ag_person_mj_ag_phone_contact_type (mj_person_phone_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_primary_phone_contact_1 ON ag_person_primary_phone_contact (mj_person_phone_uid ASC) ;

CREATE INDEX ag_person_primary_phone_contact_person_uid_idx ON ag_person_primary_phone_contact (person_uid ASC) ;

CREATE INDEX ag_person_primary_phone_contact_phone_contact_type_uid_idx ON ag_person_primary_phone_contact (phone_contact_type_uid ASC) ;

CREATE INDEX ag_person_primary_phone_contact_idx ON ag_person_primary_phone_contact (phone_contact ASC) ;


-- -----------------------------------------------------
-- Table ag_residential_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_residential_status (
  residential_status_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  residential_status VARCHAR(30) NOT NULL COMMENT 'I.E. citizen, illegal alien, travellers, etc.' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (residential_status_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE UNIQUE INDEX ag_residential_status_unq ON ag_residential_status (residential_status ASC) ;


-- -----------------------------------------------------
-- Table ag_person_residential_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_residential_status (
  person_uid BIGINT(20) NOT NULL ,
  country_uid SMALLINT(6) NOT NULL COMMENT 'Physically located at at time of registration.' ,
  residential_status_uid SMALLINT(6) NOT NULL COMMENT 'Residential status in relation to the country_uid field.' ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_residential_status_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_residential_status_2
    FOREIGN KEY (residential_status_uid )
    REFERENCES ag_residential_status (residential_status_uid ),
  CONSTRAINT fk_ag_person_residential_status_3
    FOREIGN KEY (country_uid )
    REFERENCES ag_nation (nation_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_residential_status_1 ON ag_person_residential_status (person_uid ASC) ;

CREATE INDEX fk_ag_person_residential_status_2 ON ag_person_residential_status (residential_status_uid ASC) ;

CREATE INDEX fk_ag_person_residential_status_3 ON ag_person_residential_status (country_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_sex
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_sex (
  sex_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  sex VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL ,
  PRIMARY KEY (sex_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_sex_unq ON ag_sex (sex ASC) ;


-- -----------------------------------------------------
-- Table ag_person_sex
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_sex (
  person_uid BIGINT(20) NOT NULL ,
  sex_uid SMALLINT(6) NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_sex_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_sex_2
    FOREIGN KEY (sex_uid )
    REFERENCES ag_sex (sex_uid )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_sex_1 ON ag_person_sex (person_uid ASC) ;

CREATE INDEX fk_ag_person_sex_2 ON ag_person_sex (sex_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_skills
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_skills (
  skills_uid INT(11) NOT NULL AUTO_INCREMENT ,
  skill VARCHAR(128) NOT NULL COMMENT 'Doctor, CPR, Registered Nurse, Sign Language, etc.' ,
  app_display BIT(1) NOT NULL DEFAULT b'1' COMMENT 'Practice daily, weekly, monthly, yearly, within last 5 yrs, etc.' ,
  PRIMARY KEY (skills_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_skills_unq ON ag_skills (skill ASC) ;


-- -----------------------------------------------------
-- Table ag_skills_frequency
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_skills_frequency (
  skills_frequency_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  skills_frequency VARCHAR(30) NOT NULL COMMENT 'Practice daily, weekly, monthly, yearly, within last 5 yrs, etc.' ,
  app_display BIT(1) NULL DEFAULT b'1' ,
  PRIMARY KEY (skills_frequency_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table ag_person_skills
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_skills (
  person_uid BIGINT(20) NOT NULL ,
  skills_uid INT(11) NOT NULL ,
  skills_frequency_uid SMALLINT(6) NOT NULL ,
  PRIMARY KEY (person_uid, skills_uid) ,
  CONSTRAINT fk_ag_person_skills_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_person_skills_2
    FOREIGN KEY (skills_uid )
    REFERENCES ag_skills (skills_uid ),
  CONSTRAINT fk_ag_person_skills_3
    FOREIGN KEY (skills_frequency_uid )
    REFERENCES ag_skills_frequency (skills_frequency_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX ag_person_skills_person_idx ON ag_person_skills (person_uid ASC) ;

CREATE INDEX ag_person_skills_skills_idx ON ag_person_skills (skills_uid ASC) ;

CREATE INDEX ag_person_skills_frequency_idx ON ag_person_skills (skills_frequency_uid ASC) ;

CREATE INDEX fk_ag_person_skills_1 ON ag_person_skills (person_uid ASC) ;

CREATE INDEX fk_ag_person_skills_2 ON ag_person_skills (skills_uid ASC) ;

CREATE INDEX fk_ag_person_skills_3 ON ag_person_skills (skills_frequency_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_person_to_sf_guard_user
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_to_sf_guard_user (
  person_uid BIGINT(20) NOT NULL ,
  sf_guard_user_id INT(11) NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_to_sf_guard_user_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid ),
  CONSTRAINT fk_ag_person_to_sf_guard_user_2
    FOREIGN KEY (sf_guard_user_id )
    REFERENCES sf_guard_user (id )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_person_to_sf_guard_user_unq ON ag_person_to_sf_guard_user (sf_guard_user_id ASC) ;

CREATE INDEX fk_ag_person_to_sf_guard_user_1 ON ag_person_to_sf_guard_user (person_uid ASC) ;

CREATE INDEX fk_ag_person_to_sf_guard_user_2 ON ag_person_to_sf_guard_user (sf_guard_user_id ASC) ;


-- -----------------------------------------------------
-- Table ag_person_to_user
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_person_to_user (
  person_uid BIGINT(20) NOT NULL ,
  user_id INT(11) NOT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_person_to_user_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_person_to_user_1 ON ag_person_to_user (person_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_scenario
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_scenario (
  scenario_uid INT(11) NOT NULL AUTO_INCREMENT ,
  scenario VARCHAR(64) NOT NULL COMMENT 'Scenario should be unique.' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (scenario_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Types of Disaster, such as hurricane, earthquake, typhoon, e';

CREATE UNIQUE INDEX ag_scenario_unq ON ag_scenario (scenario ASC) ;


-- -----------------------------------------------------
-- Table ag_staff_role
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_staff_role (
  staff_role_uid INT(11) NOT NULL ,
  staff_role VARCHAR(64) NOT NULL COMMENT 'Operator, Medical Nurse, Medical Other' ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (staff_role_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_staff_role_unq ON ag_staff_role (staff_role ASC) ;


-- -----------------------------------------------------
-- Table ag_scenario_staff
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_scenario_staff (
  person_uid BIGINT(20) NOT NULL ,
  scenario_uid INT(11) NOT NULL ,
  staff_role_uid INT(11) NOT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  PRIMARY KEY (person_uid, scenario_uid) ,
  CONSTRAINT fk_ag_scenario_staff_1
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_scenario_staff_2
    FOREIGN KEY (scenario_uid )
    REFERENCES ag_scenario (scenario_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_scenario_staff_3
    FOREIGN KEY (staff_role_uid )
    REFERENCES ag_staff_role (staff_role_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Potcential Staffs for any scenario.';

CREATE INDEX fk_ag_scenario_staff_1 ON ag_scenario_staff (person_uid ASC) ;

CREATE INDEX fk_ag_scenario_staff_2 ON ag_scenario_staff (scenario_uid ASC) ;

CREATE INDEX fk_ag_scenario_staff_3 ON ag_scenario_staff (staff_role_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_sessions
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_sessions (
  sessions_uid INT(11) NOT NULL ,
  sess_key VARCHAR(64) NOT NULL ,
  secret VARCHAR(64) NOT NULL ,
  inactive_expiry BIGINT(20) NOT NULL ,
  expiry BIGINT(20) NOT NULL ,
  info TEXT NULL DEFAULT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  sf_guard_user_id INT(11) NOT NULL ,
  PRIMARY KEY (sessions_uid) ,
  CONSTRAINT fk_ag_sessions_1
    FOREIGN KEY (sf_guard_user_id )
    REFERENCES sf_guard_user (id )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_sessions_1 ON ag_sessions (sf_guard_user_id ASC) ;


-- -----------------------------------------------------
-- Table ag_site_external
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site_external (
  site_uid INT(11) NOT NULL ,
  ext_site_uuid VARCHAR(50) NOT NULL ,
  created_on DATETIME NOT NULL ,
  PRIMARY KEY (site_uid) ,
  CONSTRAINT fk_ag_site_external_1
    FOREIGN KEY (site_uid )
    REFERENCES ag_site (site_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_site_external_uid_unq ON ag_site_external (ext_site_uuid ASC) ;

CREATE INDEX fk_ag_site_external_1 ON ag_site_external (site_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_site_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site_type (
  site_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  site_type VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (site_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_site_type_unq ON ag_site_type (site_type ASC) ;


-- -----------------------------------------------------
-- Table ag_site_deleted
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site_deleted (
  ext_site_uid VARCHAR(50) NOT NULL ,
  site_uid INT(11) NOT NULL ,
  site_type_uid SMALLINT(6) NOT NULL ,
  deleted_on DATETIME NOT NULL ,
  PRIMARY KEY (ext_site_uid, site_uid, site_type_uid) ,
  CONSTRAINT fk_ag_site_deleted_1
    FOREIGN KEY (ext_site_uid , site_uid )
    REFERENCES ag_site_external (ext_site_uuid , site_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_site_deleted_2
    FOREIGN KEY (site_type_uid )
    REFERENCES ag_site_type (site_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_site_deleted_1 ON ag_site_deleted (ext_site_uid ASC, site_uid ASC) ;

CREATE INDEX fk_ag_site_deleted_2 ON ag_site_deleted (site_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_site_geo_location
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site_geo_location (
  site_uid INT(11) NOT NULL ,
  geo_location_uid INT(11) NOT NULL ,
  PRIMARY KEY (site_uid) ,
  CONSTRAINT fk_ag_site_geo_location_1
    FOREIGN KEY (site_uid )
    REFERENCES ag_site (site_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_site_geo_location_unq ON ag_site_geo_location (geo_location_uid ASC) ;

CREATE INDEX fk_ag_site_geo_location_1 ON ag_site_geo_location (site_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_site_mailing_location
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_site_mailing_location (
  site_uid INT(11) NOT NULL ,
  nation_uid SMALLINT(6) NOT NULL ,
  address_type_uid SMALLINT(6) NOT NULL ,
  value VARCHAR(128) NOT NULL ,
  PRIMARY KEY (site_uid, nation_uid, address_type_uid) ,
  CONSTRAINT fk_ag_site_mailing_location_1
    FOREIGN KEY (site_uid )
    REFERENCES ag_site (site_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_ag_site_mailing_location_2
    FOREIGN KEY (nation_uid )
    REFERENCES ag_nation (nation_uid ),
  CONSTRAINT fk_ag_site_mailing_location_3
    FOREIGN KEY (address_type_uid )
    REFERENCES ag_address_type (address_type_uid ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_site_mailing_location_1 ON ag_site_mailing_location (site_uid ASC) ;

CREATE INDEX fk_ag_site_mailing_location_2 ON ag_site_mailing_location (nation_uid ASC) ;

CREATE INDEX fk_ag_site_mailing_location_3 ON ag_site_mailing_location (address_type_uid ASC) ;


-- -----------------------------------------------------
-- Table ag_source
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_source (
  source_uuid INT(11) NOT NULL AUTO_INCREMENT ,
  source VARCHAR(64) NOT NULL ,
  created_on DATETIME NOT NULL ,
  updated_on DATETIME NOT NULL ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (source_uuid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_source_unq ON ag_source (source ASC) ;


-- -----------------------------------------------------
-- Table ag_staff_affiliation
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_staff_affiliation (
  affiliation_uid INT(11) NOT NULL AUTO_INCREMENT ,
  affiliation VARCHAR(64) NOT NULL COMMENT 'NYC OEM, Sahana, Red Cross, Walk-in Volunteers, etc.' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (affiliation_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Staff organization';

CREATE UNIQUE INDEX ag_organization_unq ON ag_staff_affiliation (affiliation ASC) ;


-- -----------------------------------------------------
-- Table ag_staff_status
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_staff_status (
  staff_status_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  staff_status VARCHAR(30) NOT NULL COMMENT 'Active, Inactive, retire, stand-by, etc.' ,
  availabilty BIT(1) NOT NULL DEFAULT b'1' COMMENT '1 = Available, 2 = Not Available' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (staff_status_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX ag_staff_status_unq ON ag_staff_status (staff_status ASC) ;


-- -----------------------------------------------------
-- Table ag_staff_details
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS ag_staff_details (
  person_uid BIGINT(20) NOT NULL ,
  affiliation_uid INT(11) NOT NULL ,
  staff_status_uid SMALLINT(6) NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_ag_staff_details_1
    FOREIGN KEY (staff_status_uid )
    REFERENCES ag_staff_status (staff_status_uid )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ag_staff_details_2
    FOREIGN KEY (affiliation_uid )
    REFERENCES ag_staff_affiliation (affiliation_uid )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ag_staff_details_3
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_ag_staff_details_1 ON ag_staff_details (staff_status_uid ASC) ;

CREATE INDEX fk_ag_staff_details_2 ON ag_staff_details (affiliation_uid ASC) ;

CREATE INDEX fk_ag_staff_details_3 ON ag_staff_details (person_uid ASC) ;


-- -----------------------------------------------------
-- Table nyc_staff_details_type
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS nyc_staff_details_type (
  staff_details_type_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  staff_details_type VARCHAR(64) NOT NULL COMMENT 'Title, Civil Title, etc.' ,
  app_display BIT(1) NOT NULL DEFAULT b'1' ,
  description VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (staff_details_type_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX nyc_staff_details_type_unq ON nyc_staff_details_type (staff_details_type ASC) ;


-- -----------------------------------------------------
-- Table nyc_staff_details
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS nyc_staff_details (
  person_uid BIGINT(20) NOT NULL ,
  staff_details_type_uid SMALLINT(6) NOT NULL ,
  value VARCHAR(64) NOT NULL ,
  PRIMARY KEY (person_uid) ,
  CONSTRAINT fk_nyc_staff_details_1
    FOREIGN KEY (staff_details_type_uid )
    REFERENCES nyc_staff_details_type (staff_details_type_uid ),
  CONSTRAINT fk_nyc_staff_details_2
    FOREIGN KEY (person_uid )
    REFERENCES ag_person (person_uid )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Staff details used by NYC OEM.';

CREATE INDEX fk_nyc_staff_details_1 ON nyc_staff_details (staff_details_type_uid ASC) ;

CREATE INDEX fk_nyc_staff_details_2 ON nyc_staff_details (person_uid ASC) ;


-- -----------------------------------------------------
-- Table nyc_state
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS nyc_state (
  state_uid SMALLINT(6) NOT NULL AUTO_INCREMENT ,
  state VARCHAR(64) NOT NULL ,
  app_display BIT(1) NOT NULL ,
  PRIMARY KEY (state_uid) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX state_unq ON nyc_state (state ASC) ;


-- -----------------------------------------------------
-- Table sf_guard_group
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS sf_guard_group (
  id INT(11) NOT NULL AUTO_INCREMENT ,
  name VARCHAR(255) NULL DEFAULT NULL ,
  description TEXT NULL DEFAULT NULL ,
  created_at DATETIME NOT NULL ,
  updated_at DATETIME NOT NULL ,
  PRIMARY KEY (id) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table sf_guard_group_mj_ag_staff_role
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS sf_guard_group_mj_ag_staff_role (
  sf_guard_group_id INT(11) NOT NULL ,
  ag_staff_role_uid INT(11) NOT NULL ,
  PRIMARY KEY (sf_guard_group_id, ag_staff_role_uid) ,
  CONSTRAINT fk_sf_guard_group_mj_ag_staff_role_1
    FOREIGN KEY (ag_staff_role_uid )
    REFERENCES ag_staff_role (staff_role_uid ),
  CONSTRAINT fk_sf_guard_group_mj_ag_staff_role_2
    FOREIGN KEY (sf_guard_group_id )
    REFERENCES sf_guard_group (id )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE INDEX fk_sf_guard_group_mj_ag_staff_role_1 ON sf_guard_group_mj_ag_staff_role (ag_staff_role_uid ASC) ;

CREATE INDEX fk_sf_guard_group_mj_ag_staff_role_2 ON sf_guard_group_mj_ag_staff_role (sf_guard_group_id ASC) ;



-- -----------------------------------------------------
-- End of Agasti Core - Full DB Schema
-- -----------------------------------------------------

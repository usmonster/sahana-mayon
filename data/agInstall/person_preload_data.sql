-- The data in this file is for testing purposes.
-- This file includes pre-loaded data in definition tables and sample entries of person's data to be used by the view below.
-- Testing sql view.
-- 7/12/10

-- Preload data
#INSERT INTO ag_person_name_type (person_name_type_uid, person_name_type) VALUES (1, 'First Name');
#INSERT INTO ag_person_name_type (person_name_type_uid, person_name_type) VALUES (2, 'Middle Name');
#INSERT INTO ag_person_name_type (person_name_type_uid, person_name_type) VALUES (3, 'Last Name');
#INSERT INTO ag_person_name_type (person_name_type_uid, person_name_type) VALUES (4, 'Other');

#INSERT INTO ag_phone_contact_type (phone_contact_type_uid, phone_contact_type) VALUES (1, 'Home');
#INSERT INTO ag_phone_contact_type (phone_contact_type_uid, phone_contact_type) VALUES (2, 'Work');
#INSERT INTO ag_phone_contact_type (phone_contact_type_uid, phone_contact_type) VALUES (3, 'Other');

#INSERT INTO ag_email_contact_type (email_contact_type_uid, email_contact_type) VALUES (1, 'Home');
#INSERT INTO ag_email_contact_type (email_contact_type_uid, email_contact_type) VALUES (2, 'Work');
#INSERT INTO ag_email_contact_type (email_contact_type_uid, email_contact_type) VALUES (3, 'Other');

#INSERT INTO ag_sex (sex_uid, sex) VALUES (1, 'Male');
#INSERT INTO ag_sex (sex_uid, sex) VALUES (2, 'Female');
#INSERT INTO ag_sex (sex_uid, sex) VALUES (3, 'Other');

#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (1, 'Mixed Race');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (2, 'Arctic (Siberian, Eskimo)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (3, 'Caucasian (European)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (4, 'Caucasian (Indian)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (5, 'Caucasian (Middle East)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (6, 'Caucasian (North American, other)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (7, 'Indigenous Australian');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (8, 'North East Asian (Mongol, Tibetan, Korean, Japanese, etc)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (9, 'Pacific (Polynesian, Micronesian, etc)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (10, 'South East Asian (Chinese, Thai, Malay, Filipino, etc)');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (11, 'West African, Bushmen, Ethiopian');
#INSERT INTO ag_ethnicity (ethnicity_uid, ethnicity) VALUES (12, 'Other');


#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (1, 'Single');
#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (2, 'Married');
#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (3, 'Divorced');
#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (4, 'Separated');
#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (5, 'Widow');
#INSERT INTO ag_marital_status (marital_status_uid, marital_status) VALUES (6, 'Other');



-- Test cases
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (1, 'Jane');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (2, 'John');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (3, 'Victoria');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (4, 'William');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (5, 'White');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (6, 'Seto');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (7, 'Smith');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (8, 'Sam');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (9, 'Reba');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (10, 'Lance');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (11, 'Lawrence');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (12, 'Chan');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (13, 'Cadman');
#INSERT INTO ag_person_name (person_name_uid, person_name) VALUES (14, 'Silverspoon');


#INSERT INTO ag_person (person_uid, created_on) VALUES (1, now());
#INSERT INTO ag_person (person_uid, created_on) VALUES (2, now());

INSERT INTO ag_person_mj_ag_person_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (1, 1, 3, 1);
INSERT INTO ag_person_mj_ag_person_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (2, 1, 9, 2);
INSERT INTO ag_person_mj_ag_person_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (3, 1, 9, 3);
INSERT INTO ag_person_mj_ag_person_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (4, 2, 10, 1);
INSERT INTO ag_person_mj_ag_person_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (5, 2, 7, 3);

INSERT INTO ag_person_primary_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (1, 1, 3, 1);
INSERT INTO ag_person_primary_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (2, 1, 9, 2);
INSERT INTO ag_person_primary_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (3, 1, 9, 3);
INSERT INTO ag_person_primary_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (4, 2, 10, 1);
INSERT INTO ag_person_primary_name (mj_person_name_uid, person_uid, person_name_uid, person_name_type_uid) VALUES (5, 2, 7, 3);

INSERT INTO ag_person_mj_ag_phone_contact_type (mj_person_phone_uid, person_uid, phone_contact_type_uid, phone_contact) VALUES (1, 1, 1, 7778889999);
INSERT INTO ag_person_mj_ag_phone_contact_type (mj_person_phone_uid, person_uid, phone_contact_type_uid, phone_contact) VALUES (2, 1,2, 2223334444);
INSERT INTO ag_person_mj_ag_phone_contact_type (mj_person_phone_uid, person_uid, phone_contact_type_uid, phone_contact) VALUES (3, 2, 2, 2226660000);

INSERT INTO ag_person_primary_phone_contact (mj_person_phone_uid, person_uid, phone_contact_type_uid, phone_contact) VALUES (1, 1, 1, 7778889999);
INSERT INTO ag_person_primary_phone_contact (mj_person_phone_uid, person_uid, phone_contact_type_uid, phone_contact) VALUES (3, 2, 2, 2226660000);

INSERT INTO ag_person_sex (person_uid, sex_uid) VALUES (2, 1);

INSERT INTO ag_person_date_of_birth (person_uid, date_of_birth) VALUES (1, '19860423');
INSERT INTO ag_person_date_of_birth (person_uid, date_of_birth) VALUES (2, '19671203');


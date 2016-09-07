#
# Table structure for table 'tx_bankaccountexample_domain_model_account'
#
CREATE TABLE tx_bankaccountexample_domain_model_account (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	closed tinyint(1) unsigned DEFAULT '0' NOT NULL,
	iban varchar(255) DEFAULT '' NOT NULL,
	account_holder varchar(255) DEFAULT '' NOT NULL,
	balance double(11,2) DEFAULT '0.00' NOT NULL,
	transactions int(11) unsigned DEFAULT '0' NOT NULL,

	overdraft_limit double(11,2) DEFAULT '0.00' NOT NULL,
	overdraft_rate double(11,2) DEFAULT '0.00' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)

);

#
# Table structure for table 'tx_bankaccountexample_domain_model_transaction'
#
CREATE TABLE tx_bankaccountexample_domain_model_transaction (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	type varchar(255) DEFAULT '' NOT NULL,
	account int(11) unsigned DEFAULT '0' NOT NULL,

	transaction_id varchar(36) DEFAULT NULL,
	entry_date datetime DEFAULT '0000-00-00 00:00:00',
	availability_date datetime DEFAULT '0000-00-00 00:00:00',
	reference varchar(255) DEFAULT '' NOT NULL,
	money double(11,2) DEFAULT '0.00' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)

);



#
# Table structure for table 'tx_bankaccountexample_projection_iban'
#
CREATE TABLE tx_bankaccountexample_projection_iban (

	national_code varchar(2) DEFAULT '' NOT NULL,
	branch_code varchar(10) DEFAULT '' NOT NULL,
	subsidiary_code varchar(10) DEFAULT '' NOT NULL,
	account_number varchar(16) DEFAULT '' NOT NULL,
	iban varchar(34) DEFAULT '' NOT NULL,

	PRIMARY KEY (national_code, branch_code, subsidiary_code, iban)

);

#
# Table structure for table 'tx_bankaccountexample_projection_account'
#
CREATE TABLE tx_bankaccountexample_projection_account (

	iban varchar(34) DEFAULT '' NOT NULL,
	closed tinyint(1) unsigned DEFAULT '0' NOT NULL,
	account_holder varchar(255) DEFAULT '' NOT NULL,
	balance double(11,2) DEFAULT '0.00' NOT NULL,
	overdraft_limit double(11,2) DEFAULT '0.00' NOT NULL,
	overdraft_rate double(11,2) DEFAULT '0.00' NOT NULL,

	PRIMARY KEY (iban)

);

#
# Table structure for table 'tx_bankaccountexample_projection_transaction'
#
CREATE TABLE tx_bankaccountexample_projection_transaction (

	transaction_id varchar(36) DEFAULT NULL,
	transaction_type varchar(255) DEFAULT '' NOT NULL,
	iban varchar(34) DEFAULT '' NOT NULL,
	money double(11,2) DEFAULT '0.00' NOT NULL,
	reference varchar(50) DEFAULT '' NOT NULL,
	entry_date datetime DEFAULT '0000-00-00 00:00:00',
	availability_date datetime DEFAULT '0000-00-00 00:00:00',

	PRIMARY KEY (transaction_id)

);

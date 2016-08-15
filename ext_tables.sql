#
# Table structure for table 'sys_event_store'
#
CREATE TABLE sys_event_store (
	uid int(11) unsigned NOT NULL auto_increment,
	event_stream varchar(128) NOT NULL,
	event_categories varchar(256) DEFAULT NULL,
	event_uuid varchar(36) NOT NULL,
	event_name varchar(256) NOT NULL,
	event_date TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
	data mediumtext,
	metadata text,
  PRIMARY KEY (uid)
);

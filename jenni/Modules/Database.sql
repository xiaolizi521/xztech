# $Id: Database.sql,v 1.1 2002/03/03 05:57:25 jenni Exp $
#
# Table structure for table 'definitions'
#

CREATE TABLE definitions (
  definitionid mediumint(9) NOT NULL auto_increment,
  word varchar(64) NOT NULL default '',
  definition text NOT NULL,
  userid mediumint(9) NOT NULL default '0',
  modts datetime NOT NULL default '0000-00-00 00:00:00',
  addts datetime NOT NULL default '0000-00-00 00:00:00',
  numhits smallint(6) NOT NULL default '0',
  PRIMARY KEY  (definitionid),
  UNIQUE KEY word (word)
) TYPE=MyISAM;


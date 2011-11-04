# $Id: LastSeen.sql,v 1.1 2002/03/03 19:13:38 jenni Exp $
#
# Table structure for table 'lastseen'
#

CREATE TABLE lastseen (
  userid mediumint(9) NOT NULL default '0',
  lastts datetime NOT NULL default '0000-00-00 00:00:00',
  firstts datetime NOT NULL default '0000-00-00 00:00:00',
  lastwords tinytext NOT NULL,
  linecount mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (userid)
) TYPE=MyISAM;


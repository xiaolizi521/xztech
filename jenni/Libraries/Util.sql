# $Id: Util.sql,v 1.1 2002/03/03 06:59:58 jenni Exp $
#
# Table structure for table 'users'
#

CREATE TABLE users (
  userid mediumint(9) NOT NULL auto_increment,
  nick varchar(30) NOT NULL default '',
  ident varchar(10) NOT NULL default '',
  host varchar(100) NOT NULL default '',
  PRIMARY KEY  (userid),
  UNIQUE KEY nick (nick,ident,host)
) TYPE=MyISAM;


# $Id: Administration.sql,v 1.1 2002/03/03 00:52:51 jenni Exp $
#
# Table structure for table 'admins'
#

CREATE TABLE admins (
  adminid tinyint(4) NOT NULL auto_increment,
  mask char(100) NOT NULL default '',
  userid mediumint(9) NOT NULL default '0',
  ts timestamp(14) NOT NULL,
  PRIMARY KEY  (adminid),
  UNIQUE KEY mask (mask)
) TYPE=MyISAM;

#
# Table structure for table 'channels'
#

CREATE TABLE channels (
  channelid tinyint(4) NOT NULL auto_increment,
  channel varchar(50) NOT NULL default '',
  userid mediumint(9) NOT NULL default '0',
  ts timestamp(14) NOT NULL,
  PRIMARY KEY  (channelid),
  UNIQUE KEY channel (channel)
) TYPE=MyISAM;


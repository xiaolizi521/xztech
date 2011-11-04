# $Id: Modules.sql,v 1.1 2002/03/03 06:20:58 jenni Exp $
#
# Table structure for table 'modules'
#

CREATE TABLE modules (
  moduleid tinyint(4) NOT NULL auto_increment,
  module char(40) NOT NULL default '',
  userid mediumint(9) NOT NULL default '0',
  ts timestamp(14) NOT NULL,
  PRIMARY KEY  (moduleid),
  UNIQUE KEY module (module)
) TYPE=MyISAM;


# $Id: Quotes.sql,v 1.1 2002/03/03 02:31:14 jenni Exp $
#
# Table structure for table 'quotes'
#

CREATE TABLE quotes (
  quoteid mediumint(9) NOT NULL auto_increment,
  userid mediumint(9) NOT NULL default '0',
  ts timestamp(14) NOT NULL,
  quote text NOT NULL,
  PRIMARY KEY  (quoteid)
) TYPE=MyISAM;


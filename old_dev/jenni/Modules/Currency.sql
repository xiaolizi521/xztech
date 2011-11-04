# $Id: Currency.sql,v 1.1 2002/03/03 00:26:14 jenni Exp $
#
# Table structure for table 'currency'
#

CREATE TABLE currency (
  currencyid smallint(6) NOT NULL auto_increment,
  symbol char(3) NOT NULL default '',
  currency char(30) NOT NULL default '',
  plural char(1) NOT NULL default 'Y',
  PRIMARY KEY  (currencyid),
  KEY currency (currency),
  KEY symbol (symbol)
) TYPE=MyISAM;

#
# Dumping data for table 'currency'
#

INSERT INTO currency VALUES (1,'USD','US Dollar','Y');
INSERT INTO currency VALUES (2,'EUR','Euro','Y');
INSERT INTO currency VALUES (3,'GBP','UK Pound','Y');
INSERT INTO currency VALUES (4,'CAD','Canadian Dollar','Y');
INSERT INTO currency VALUES (5,'DEM','German Deutsche Mark','Y');
INSERT INTO currency VALUES (6,'FRF','French Franc','Y');
INSERT INTO currency VALUES (7,'JPY','Japanese Yen','N');
INSERT INTO currency VALUES (8,'NLG','Dutch Guilder','Y');
INSERT INTO currency VALUES (9,'ITL','Italian Lira','N');
INSERT INTO currency VALUES (10,'CHF','Swiss Franc','Y');
INSERT INTO currency VALUES (11,'AUD','Australian Dollar','Y');
INSERT INTO currency VALUES (12,'DZD','Algerian Dinar','Y');
INSERT INTO currency VALUES (13,'ARP','Argentinan Peso','Y');
INSERT INTO currency VALUES (14,'ATS','Austrian Schilling','Y');
INSERT INTO currency VALUES (15,'BSD','Bahamas Dollar','Y');
INSERT INTO currency VALUES (16,'BBD','Barbados Dollar','Y');
INSERT INTO currency VALUES (17,'BEF','Belgian Franc','Y');
INSERT INTO currency VALUES (18,'BMD','Bermuda Dollar','Y');
INSERT INTO currency VALUES (19,'BRL','Brazilian Real','N');
INSERT INTO currency VALUES (20,'BGL','Bulgarian Lev','N');
INSERT INTO currency VALUES (21,'CLP','Chilean Peso','Y');
INSERT INTO currency VALUES (22,'CNY','China Yuan Renmimbi','N');
INSERT INTO currency VALUES (23,'CYP','Cyprus Pound','Y');
INSERT INTO currency VALUES (24,'CZK','Czech Koruna','N');
INSERT INTO currency VALUES (25,'DKK','Danish Kroner','N');
INSERT INTO currency VALUES (26,'XCD','Eastern Caribbean Dollar','Y');
INSERT INTO currency VALUES (27,'EGP','Egyptian Pound','Y');
INSERT INTO currency VALUES (28,'FJD','Fijian Dollar','Y');
INSERT INTO currency VALUES (29,'FIM','Finnish Markka','N');
INSERT INTO currency VALUES (30,'XAU','Gold Ounce','Y');
INSERT INTO currency VALUES (31,'GRD','Grecian Drachma','Y');
INSERT INTO currency VALUES (32,'HKD','Hong Kong Dollar','Y');
INSERT INTO currency VALUES (33,'HUF','Hungarian Forint','N');
INSERT INTO currency VALUES (34,'XDR','IMF Special Drawing Right','N');
INSERT INTO currency VALUES (35,'ISK','Iceland Krona','N');
INSERT INTO currency VALUES (36,'INR','Indian Rupee','Y');
INSERT INTO currency VALUES (37,'IDR','Indonesian Rupiah','N');
INSERT INTO currency VALUES (38,'IEP','Irish Punt','N');
INSERT INTO currency VALUES (39,'ILS','Israel New Shekel','Y');
INSERT INTO currency VALUES (40,'JMD','Jamaican Dollar','Y');
INSERT INTO currency VALUES (41,'JOD','Jordan Dinar','N');
INSERT INTO currency VALUES (42,'KRW','Korea (South) Won','N');
INSERT INTO currency VALUES (43,'LBP','Lebanon Pound','Y');
INSERT INTO currency VALUES (44,'LUF','Luxembourg Franc','Y');
INSERT INTO currency VALUES (45,'MYR','Malaysian Ringgit','N');
INSERT INTO currency VALUES (46,'MXP','Mexican Peso','Y');
INSERT INTO currency VALUES (47,'NZD','New Zealand Dollar','Y');
INSERT INTO currency VALUES (48,'NOK','Norwegian Kroner','N');
INSERT INTO currency VALUES (49,'PKR','Pakistan Rupee','Y');
INSERT INTO currency VALUES (50,'PHP','Philippine Peso','Y');
INSERT INTO currency VALUES (51,'XPT','Platinum Ounce','Y');
INSERT INTO currency VALUES (52,'PLZ','Poland Zloty','N');
INSERT INTO currency VALUES (53,'PTE','Portuguese Escudo','Y');
INSERT INTO currency VALUES (54,'ROL','Romanian Leu','N');
INSERT INTO currency VALUES (55,'RUR','Russian Ruble','Y');
INSERT INTO currency VALUES (56,'SAR','Saudi Arabian Riyal','N');
INSERT INTO currency VALUES (57,'XAG','Silver Ounce','Y');
INSERT INTO currency VALUES (58,'SGD','Singapore Dollar','Y');
INSERT INTO currency VALUES (59,'SKK','Slovakian Koruna','N');
INSERT INTO currency VALUES (60,'ZAR','South African Rand','N');
INSERT INTO currency VALUES (61,'ESP','Spanish Peseta','Y');
INSERT INTO currency VALUES (62,'SDD','Sudan Dinar','N');
INSERT INTO currency VALUES (63,'SEK','Swedish Krona','N');
INSERT INTO currency VALUES (64,'TWD','Taiwan Dollar','Y');
INSERT INTO currency VALUES (65,'THB','Thailand Baht','N');
INSERT INTO currency VALUES (66,'TTD','Trinidad and Tobago Dollar','Y');
INSERT INTO currency VALUES (67,'TRL','Turkish Lira','N');
INSERT INTO currency VALUES (68,'VEB','Venezuelan Bolivar','N');
INSERT INTO currency VALUES (69,'ZMK','Zambia Kwacha','N');


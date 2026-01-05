#
# Table structure for table 'tx_kursanmeldung_domain_model_setup'
#
CREATE TABLE tx_kursanmeldung_domain_model_setup (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  propname varchar(255) DEFAULT '' NOT NULL,
  propvalue text NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_kursanmeldung_domain_model_orte'
#
CREATE TABLE tx_kursanmeldung_domain_model_orte (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  ort varchar(255) DEFAULT '' NOT NULL,
  plz varchar(255) DEFAULT '' NOT NULL,
  adresse text NOT NULL,
  longi double(11,2) DEFAULT '0' NOT NULL,
  lati double(11,2) DEFAULT '0' NOT NULL,
  beschreibung text NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_orte_mm'
#
CREATE TABLE tx_kursanmeldung_domain_model_orte_mm (
   uid_local int(11) DEFAULT '0' NOT NULL,
   uid_foreign int(11) DEFAULT '0' NOT NULL,
   sorting int(11) DEFAULT '0' NOT NULL,
   sorting_foreign int(11) DEFAULT '0' NOT NULL,

   KEY uid_local (uid_local),
   KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_uploads'
#
CREATE TABLE tx_kursanmeldung_domain_model_uploads (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  kurs int(11) DEFAULT '0' NOT NULL,
  kat varchar(255) DEFAULT '' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  pfad varchar(255) DEFAULT '' NOT NULL,
  datein varchar(255) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_uploads_mm'
#
CREATE TABLE tx_kursanmeldung_domain_model_uploads_mm (
   uid_local int(11) DEFAULT '0' NOT NULL,
   uid_foreign int(11) DEFAULT '0' NOT NULL,
   sorting int(11) DEFAULT '0' NOT NULL,
   sorting_foreign int(11) DEFAULT '0' NOT NULL,

   KEY uid_local (uid_local),
   KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_hotel'
#
CREATE TABLE tx_kursanmeldung_domain_model_hotel (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  hotel varchar(255) DEFAULT '' NOT NULL,
  beschreibung text NOT NULL,
  ezpreis double(11,2) DEFAULT '0' NOT NULL,
  ezpreiserm double(11,2) DEFAULT '0' NOT NULL,
  dzpreis double(11,2) DEFAULT '0' NOT NULL,
  dzpreiserm double(11,2) DEFAULT '0' NOT NULL,
  dz2preis double(11,2) DEFAULT '0' NOT NULL,
  dz2preiserm double(11,2) DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_hotel_mm'
#
CREATE TABLE tx_kursanmeldung_domain_model_hotel_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  sorting_foreign int(11) DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_kurs'
#
CREATE TABLE tx_kursanmeldung_domain_model_kurs (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  aktiv tinyint(4) unsigned DEFAULT '0' NOT NULL,
  kursnr varchar(255) DEFAULT '' NOT NULL,
  instrument varchar(255) DEFAULT '' NOT NULL,
  kurszeitstart datetime default NULL,
  kurszeitend datetime default NULL,
  anreisedate datetime default NULL,
  kursort int(11) DEFAULT '0' NOT NULL,
  professor int(11) DEFAULT '0' NOT NULL,
  gebuehr int(11) DEFAULT '0' NOT NULL,
  gebuehrcom text NOT NULL,
  orchstudio int(11) DEFAULT '0' NOT NULL,
  aktivtn int(11) DEFAULT '0' NOT NULL,
  passivtn int(11) DEFAULT '0' NOT NULL,
  hotel int(11) DEFAULT '0' NOT NULL,
  maxupload int(11) DEFAULT '0' NOT NULL,
  weblink tinyint(4) unsigned DEFAULT '0' NOT NULL,
  youtube tinyint(4) unsigned DEFAULT '0' NOT NULL,
  vita tinyint(4) unsigned DEFAULT '0' NOT NULL,
  stipendien tinyint(4) unsigned DEFAULT '0' NOT NULL,
  duo tinyint(4) unsigned DEFAULT '0' NOT NULL,
  duosel text NOT NULL,
  ensemble varchar(255) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_gebuehren'
#
CREATE TABLE tx_kursanmeldung_domain_model_gebuehren (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  anmeldung double(11,2) DEFAULT '0' NOT NULL,
  anmeldungerm double(11,2) DEFAULT '0' NOT NULL,
  aktivengeb double(11,2) DEFAULT '0' NOT NULL,
  aktivengeberm double(11,2) DEFAULT '0' NOT NULL,
  passivgeb double(11,2) DEFAULT '0' NOT NULL,
  passivgeberm double(11,2) DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_prof'
#
CREATE TABLE tx_kursanmeldung_domain_model_prof (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  link varchar(255) DEFAULT '' NOT NULL,
  image varchar(255) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_teilnehmer'
#
CREATE TABLE tx_kursanmeldung_domain_model_teilnehmer (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  vorname varchar(255) DEFAULT '' NOT NULL,
  nachname varchar(255) DEFAULT '' NOT NULL,
  anrede int(11) DEFAULT '0' NOT NULL,
  titel varchar(255) DEFAULT '' NOT NULL,
  matrikel varchar(255) DEFAULT '' NOT NULL,
  gebdate datetime NULL,
  sprache varchar(255) DEFAULT '' NOT NULL,
  nation varchar(255) DEFAULT '' NOT NULL,
  adresse1 varchar(255) DEFAULT '' NOT NULL,
  hausnr varchar(255) DEFAULT '' NOT NULL,
  adresse2 varchar(255) DEFAULT '' NOT NULL,
  plz varchar(255) DEFAULT '' NOT NULL,
  ort varchar(255) DEFAULT '' NOT NULL,
  land varchar(255) DEFAULT '' NOT NULL,
  telefon varchar(255) DEFAULT '' NOT NULL,
  mobil varchar(255) DEFAULT '' NOT NULL,
  telefax varchar(255) DEFAULT '' NOT NULL,
  email varchar(255) DEFAULT '' NOT NULL,
  datein datetime NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_kursanmeldung_domain_model_uploads'
#
CREATE TABLE tx_kursanmeldung_domain_model_uploads (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  kurs int(11) DEFAULT '0' NOT NULL,
  kat varchar(255) DEFAULT '' NOT NULL,
  name varchar(255) DEFAULT '' NOT NULL,
  pfad varchar(255) DEFAULT '' NOT NULL,
  datein datetime NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_kursanmeldung'
#
CREATE TABLE tx_kursanmeldung_domain_model_kursanmeldung (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  deflang tinyint(4) unsigned DEFAULT NULL,
  tn int(11) DEFAULT '0' NOT NULL,
  kurs int(11) DEFAULT '0' NOT NULL,
  uploads int(11) DEFAULT '0' NOT NULL,
  bezahlt int(11) DEFAULT '0' NOT NULL,
  bezahltag int(11) DEFAULT '0' NOT NULL,
  zahlart varchar(255) DEFAULT '' NOT NULL,
  zahltbis datetime DEFAULT NULL,
  gezahlt varchar(255) DEFAULT '' NOT NULL,
  gezahltag varchar(255) DEFAULT '' NOT NULL,
  gezahltos varchar(255) DEFAULT '' NOT NULL,
  hotel int(11) DEFAULT '0' NOT NULL,
  room varchar(255) DEFAULT '' NOT NULL,
  roomwith varchar(255) DEFAULT '' NOT NULL,
  roomfrom varchar(255) DEFAULT '' NOT NULL,
  roomto varchar(255) DEFAULT '' NOT NULL,
  gebuehr varchar(255) DEFAULT '' NOT NULL,
  gebuehreingang datetime DEFAULT NULL,
  gebuehrag varchar(255) DEFAULT '' NOT NULL,
  gebuehrdat datetime DEFAULT NULL,
  datein datetime DEFAULT NULL,
  teilnahmeart varchar(255) DEFAULT '' NOT NULL,
  anmeldestatus tinyint(4) unsigned DEFAULT NULL,
  profstatus tinyint(4) unsigned DEFAULT NULL,
  programm text NOT NULL,
  orchesterstudio text NOT NULL,
  duo tinyint(4) DEFAULT '0' NOT NULL,
  duosel varchar(255) DEFAULT '' NOT NULL,
  duoname text NOT NULL,
  comment text NOT NULL,
  agb tinyint(4) DEFAULT '0' NOT NULL,
  datenschutz tinyint(4) DEFAULT '0' NOT NULL,
  savedata tinyint(4) DEFAULT '0' NOT NULL,
  salt varchar(255) DEFAULT '' NOT NULL,
  registrationkey text NOT NULL,
  doitime datetime DEFAULT NULL,
  novalnettid varchar(255) DEFAULT '' NOT NULL,
  novalnettidag varchar(255) DEFAULT '' NOT NULL,
  novalnetcno varchar(255) DEFAULT '' NOT NULL,
  notice text NOT NULL,
  ensemble int(11) DEFAULT '0' NOT NULL,
  stipendiat tinyint(4) DEFAULT '0' NOT NULL,
  studentship tinyint(4) DEFAULT '0' NOT NULL,
  studystat tinyint(4) DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_kursanmeldung_domain_model_anmeldestatus'
#
CREATE TABLE tx_kursanmeldung_domain_model_anmeldestatus (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  anmeldestatus varchar(255) DEFAULT '' NOT NULL,
  kurz varchar(255) DEFAULT '' NOT NULL,
  beschreibung text NOT NULL,
  reducetnart tinyint(4) DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_mailhist'
#
CREATE TABLE tx_kursanmeldung_domain_model_mailhist (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  subject varchar(255) DEFAULT '' NOT NULL,
  sendername varchar(255) DEFAULT '' NOT NULL,
  sendermail varchar(255) DEFAULT '' NOT NULL,
  pageid varchar(255) DEFAULT '' NOT NULL,
  mailtype varchar(255) DEFAULT '' NOT NULL,
  nachricht text NOT NULL,
  recipients int(11) DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_mailhistrecipients'
#
CREATE TABLE tx_kursanmeldung_domain_model_mailhistrecipients (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  mailuid int(11) unsigned DEFAULT '0' NOT NULL,
  recipient varchar(255) DEFAULT '' NOT NULL,
  datesend int(11) unsigned DEFAULT '0' NOT NULL,
  regid int(11) unsigned DEFAULT '0' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_mailhistrecipients_mm'
#
CREATE TABLE tx_kursanmeldung_domain_model_mailhistrecipients_mm (
 uid_local int(11) DEFAULT '0' NOT NULL,
 uid_foreign int(11) DEFAULT '0' NOT NULL,
 sorting int(11) DEFAULT '0' NOT NULL,
 sorting_foreign int(11) DEFAULT '0' NOT NULL,

 KEY uid_local (uid_local),
 KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_exportlist'
#
CREATE TABLE tx_kursanmeldung_domain_model_exportlist (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  name varchar(255) DEFAULT '' NOT NULL,
  tables text NOT NULL,
  notice text NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_ensemble'
#
CREATE TABLE tx_kursanmeldung_domain_model_ensemble (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  enconf int(11) unsigned DEFAULT '0' NOT NULL,
  enname varchar(255) DEFAULT '' NOT NULL,
  engrdate date DEFAULT NULL,
  entype varchar(255) DEFAULT '' NOT NULL,
  engrplace varchar(255) DEFAULT '' NOT NULL,
  entn int(11) unsigned DEFAULT '0' NOT NULL,
  enfirstn varchar(255) DEFAULT '' NOT NULL,
  enlastn varchar(255) DEFAULT '' NOT NULL,
  eninstru varchar(255) DEFAULT '' NOT NULL,
  engebdate date DEFAULT NULL,
  ennatio varchar(255) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);

#
# Table structure for table 'tx_kursanmeldung_domain_model_ensemble_mm'
#
CREATE TABLE tx_kursanmeldung_domain_model_ensemble_mm (
 uid_local int(11) DEFAULT '0' NOT NULL,
 uid_foreign int(11) DEFAULT '0' NOT NULL,
 sorting int(11) DEFAULT '0' NOT NULL,
 sorting_foreign int(11) DEFAULT '0' NOT NULL,

 KEY uid_local (uid_local),
 KEY uid_foreign (uid_foreign)
);

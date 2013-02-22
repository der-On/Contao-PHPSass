-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_phpsass`
-- 

CREATE TABLE `tl_phpsass` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `sass_dir` varchar(1024) NOT NULL default '',
  `css_dir` varchar(1024) NOT NULL default '',
  `extensions_dir` varchar(1024) NOT NULL default '',
  `images_dir` varchar(1024) NOT NULL default '',
  `javascripts_dir` varchar(1024) NOT NULL default '',
  `output_style` varchar(1024) NOT NULL default '',
  `disable` char(1) NOT NULL default ''
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


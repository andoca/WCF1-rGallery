-- 
-- Table structure for table `wcf1_rGallery_cats`
-- 

CREATE TABLE `wcf1_rGallery_cats` (
  `catID` int(11) unsigned NOT NULL auto_increment,
  `catName` varchar(255) NOT NULL,
  `catAuthorized_group` int(11) default NULL,
  `catComment` text NOT NULL,
  `catWriteable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`catID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `wcf1_rGallery_cats` (`catName`,`catWriteable`,`catComment` ) VALUES ('default','1','');

-- 
-- Table structure for table `wcf1_rGallery_comments`
-- 

CREATE TABLE `wcf1_rGallery_comments` (
  `commentID` int(11) unsigned NOT NULL auto_increment,
  `commentText` text NOT NULL,
  `commentAddedDate` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`commentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_comments_user`
-- 

CREATE TABLE `wcf1_rGallery_comments_user` (
  `commentID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `userName` varchar(255) NOT NULL,
  KEY `commentID` (`commentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items`
-- 

CREATE TABLE `wcf1_rGallery_items` (
  `itemID` int(11) unsigned NOT NULL auto_increment,
  `itemName` varchar(64) NOT NULL,
  `itemOrigName` varchar(255) NOT NULL,
  `itemAddedDate` int(10) unsigned NOT NULL,
  `itemModDate` int(10) unsigned NOT NULL DEFAULT '0',
  `itemPath` varchar(37) NOT NULL,
  `itemOrigExtension` varchar(4) NOT NULL,
  `itemSize` int(10) unsigned NOT NULL,
  `itemResizedSize` int(10) unsigned NOT NULL,
  `itemDimW` int(5) unsigned NOT NULL,
  `itemDimH` int(5) unsigned NOT NULL,
  `itemOrigDimW` int(5) unsigned NOT NULL,
  `itemOrigDimH` int(5) unsigned NOT NULL,
  `itemType` varchar(255) NOT NULL,
  `itemMime` varchar(255) NOT NULL,
  `itemOrigMime` varchar(255) NOT NULL,
  `itemComment` text NOT NULL,
  `itemClicks` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`itemID`),
  UNIQUE KEY `itemPath` (`itemPath`),
  FULLTEXT KEY `itemName` (`itemName`,`itemComment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items_cat`
-- 

CREATE TABLE `wcf1_rGallery_items_cat` (
  `itemID` int(11) unsigned NOT NULL,
  `catID` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`itemID`),
  KEY `catID` (`catID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items_comment`
-- 

CREATE TABLE `wcf1_rGallery_items_comment` (
  `itemID` int(11) unsigned NOT NULL,
  `commentID` int(11) unsigned NOT NULL,
  KEY `itemID` (`itemID`,`commentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items_owner`
-- 

CREATE TABLE `wcf1_rGallery_items_owner` (
  `itemID` int(11) unsigned NOT NULL,
  `ownerID` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`itemID`),
  KEY `ownerID` (`ownerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items_tag`
-- 

CREATE TABLE `wcf1_rGallery_items_tag` (
  `itemID` int(11) unsigned NOT NULL,
  `tagID` int(11) unsigned NOT NULL,
  KEY `itemID` (`itemID`,`tagID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_tags`
-- 

CREATE TABLE `wcf1_rGallery_tags` (
  `tagID` int(11) unsigned NOT NULL auto_increment,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY  (`tagID`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `wcf1_rGallery_items_rating`
-- 

CREATE TABLE `wcf1_rGallery_items_rating` (
  `ratingID` int(11) unsigned NOT NULL auto_increment,
  `itemID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL,
  `ratingValue` int(1) unsigned NOT NULL,
  PRIMARY KEY  (`ratingID`),
  KEY `itemID` (`itemID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
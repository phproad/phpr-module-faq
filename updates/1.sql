CREATE TABLE `faq_questions` (
  `id` int(11) NOT NULL auto_increment,
  `created_at` datetime default NULL,
  `category_id` int(11) default NULL,
  `title` varchar(255) default NULL,
  `description` text,
  `views` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `published` tinyint(4) DEFAULT '1',
  PRIMARY KEY  (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `faq_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `name` varchar(255) default NULL,
  `description` text,
  `code` varchar(50) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created_user_id` int(11) default NULL,
  `updated_user_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `faq_categories` (`id`, `name`, `description`, `code`, `sort_order`, `created_at`, `created_user_id`) VALUES
(1, 'General', 'Category for assorted questions', 'general', 1, now(), 1);

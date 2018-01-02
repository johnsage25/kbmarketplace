CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller` (
  `id_seller` int(10) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_default_lang` int(10) NULL DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `product_limit_wout_approval` tinyint(6) NOT NULL DEFAULT '0',
  `approval_request_limit` tinyint(6) NOT NULL DEFAULT '0',
  `phone_number` varchar(20) DEFAULT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `notification_type` enum('0','1','2') NOT NULL DEFAULT '1',
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `id_country` int(10) unsigned DEFAULT NULL,
  `payment_info` text DEFAULT NULL,
  `fb_link` varchar(255) DEFAULT NULL,
  `gplus_link` varchar(255) DEFAULT NULL,
  `twit_link` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller`),
  INDEX (`phone_number`),
  FOREIGN KEY (id_customer) references _PREFIX_customer (id_customer) ON DELETE CASCADE,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_default_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL,
  FOREIGN KEY (id_country) references _PREFIX_country (id_country) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_lang` (
  `id_seller_lang` int(10) unsigned NOT NULL auto_increment,
  `id_lang` int(11) NULL DEFAULT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `meta_keyword` text,
  `meta_description` text,
  `profile_url` text DEFAULT NULL,
  `return_policy` text,
  `shipping_policy` text,
  PRIMARY KEY (`id_seller_lang`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_product` (
  `id_seller_product` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_product` int(10) unsigned DEFAULT NULL,
  `approved` enum('0','1','2') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_product`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_product) references _PREFIX_product (id_product) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_product_tracking` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_product) references _PREFIX_product (id_product) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_review` (
  `id_seller_review` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NULL DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `comment` text,
  `rating` tinyint(1) DEFAULT '0',
  `approved` enum('0','1','2') NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_review`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_customer) references _PREFIX_customer (id_customer) ON DELETE SET NULL,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_product_review` (
  `id_seller_product_review` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_customer` int(10) unsigned NULL DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `id_product` int(10) unsigned NULL DEFAULT NULL,
  `id_product_comment` int(10) unsigned DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_product_review`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_customer) references _PREFIX_customer (id_customer) ON DELETE SET NULL,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL,
  FOREIGN KEY (id_product) references _PREFIX_product (id_product) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_category_request` (
  `id_seller_category_request` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `id_category` int(10) unsigned NULL DEFAULT NULL,
  `approved` enum('0','1','2') NOT NULL DEFAULT '0',
  `comment` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_category_request`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL,
  FOREIGN KEY (id_category) references _PREFIX_category (id_category) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_config` (
  `id_seller_config` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `use_global` enum('0','1') NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_config`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_category` (
  `id_seller_category` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NOT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_category` int(10) unsigned NULL DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_category`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_category) references _PREFIX_category (id_category) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_category_tracking` (
  `id_seller` int(10) unsigned NOT NULL,
  `id_category` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_product) references _PREFIX_product (id_product) ON DELETE CASCADE,
  FOREIGN KEY (id_category) references _PREFIX_category (id_category) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_reasons` (
  `id_reason` int(10) unsigned NOT NULL auto_increment,
  `reason_type` tinyint(2) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `id_seller_product` int(10) unsigned DEFAULT NULL,
  `id_seller_product_review` int(10) unsigned DEFAULT NULL,
  `id_seller_review` int(10) unsigned DEFAULT NULL,
  `id_seller_category_request` int(10) unsigned DEFAULT NULL,
  `id_employee` int(10) unsigned NULL DEFAULT NULL,
  `comment` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_reason`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_seller_product) references _PREFIX_kb_mp_seller_product (id_seller_product) ON DELETE CASCADE,
  FOREIGN KEY (id_seller_product_review) references _PREFIX_kb_mp_seller_product_review (id_seller_product_review) ON DELETE CASCADE,
  FOREIGN KEY (id_seller_review) references _PREFIX_kb_mp_seller_review (id_seller_review) ON DELETE CASCADE,
  FOREIGN KEY (id_seller_category_request) references _PREFIX_kb_mp_seller_category_request (id_seller_category_request) ON DELETE CASCADE,
  FOREIGN KEY (id_employee) references _PREFIX_employee (id_employee) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_earning` (
  `id_seller_earning` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned NULL DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_order` int(10) unsigned DEFAULT NULL,
  `product_count` int(10) unsigned DEFAULT NULL,
  `commission_percent` decimal(16,6) NOT NULL,
  `total_earning` decimal(16,6) NOT NULL,
  `seller_earning` decimal(16,6) NOT NULL,
  `admin_earning` decimal(16,6) NOT NULL,
  `can_handle_order` tinyint(1) NOT NULL DEFAULT '0',
  `is_canceled` enum('0','1') NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_earning`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE SET NULL,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_order) references _PREFIX_orders (id_order) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_order_detail` (
`id_seller_order_detail` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned DEFAULT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_category` int(10) unsigned DEFAULT NULL,
  `id_product` int(10) unsigned DEFAULT NULL,
  `id_order_detail` int(10) unsigned NOT NULL,
  `commission_percent` decimal(16,6) unsigned NOT NULL,
  `total_earning` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `seller_earning` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `admin_earning` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `unit_price` decimal(16,6) NOT NULL DEFAULT '0.000000',
  `qty` int(10) unsigned NOT NULL DEFAULT '0',
  `is_consider` enum('0','1') DEFAULT '1',
  `is_canceled` enum('0','1') NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_order_detail`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE SET NULL,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_order) references _PREFIX_orders (id_order) ON DELETE CASCADE,
  FOREIGN KEY (id_category) references _PREFIX_category (id_category) ON DELETE SET NULL,
  FOREIGN KEY (id_product) references _PREFIX_product (id_product) ON DELETE SET NULL,
  FOREIGN KEY (id_order_detail) references _PREFIX_order_detail (id_order_detail) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_transaction` (
  `id_seller_transaction` int(10) unsigned NOT NULL auto_increment,
  `id_seller` int(10) unsigned DEFAULT NULL,
  `id_shop` int(11) NULL DEFAULT NULL,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `transaction_number` varchar(255) NOT NULL,
  `amount` decimal(16,6) NOT NULL,
  `transaction_type` enum('0','1') NOT NULL DEFAULT '0',
  `comment` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_transaction`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE SET NULL,
  FOREIGN KEY (id_shop) references _PREFIX_shop (id_shop) ON DELETE SET NULL,
  FOREIGN KEY (id_employee) references _PREFIX_employee (id_employee) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_shipping` (
  `id_seller_shipping` int(10) unsigned NOT NULL auto_increment,
  `id_carrier` int(10) unsigned NOT NULL,
  `id_reference` int(10) unsigned NOT NULL,
  `id_seller` int(10) unsigned NOT NULL,
  `is_default_shipping` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_shipping`),
  INDEX (`id_reference`),
  FOREIGN KEY (id_seller) references _PREFIX_kb_mp_seller (id_seller) ON DELETE CASCADE,
  FOREIGN KEY (id_carrier) references _PREFIX_carrier (id_carrier) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_email_template` (
  `id_email_template` int(10) unsigned NOT NULL auto_increment,
  `end` enum('f','b') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_email_template`),
  INDEX (`name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_email_template_lang` (
  `id_email_template` int(10) unsigned NOT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text,
  FOREIGN KEY (id_email_template) references _PREFIX_kb_mp_email_template (id_email_template) ON DELETE CASCADE,
  FOREIGN KEY (id_lang) references _PREFIX_lang (id_lang) ON DELETE SET NULL
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_menu` (
  `id_seller_menu` int(10) unsigned NOT NULL auto_increment,
  `module_name` varchar(100) NOT NULL,
  `controller_name` varchar(100) NOT NULL,
  `position` int(10) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `css_class` varchar(100) DEFAULT NULL,
  `show_badge` tinyint(1) NOT NULL DEFAULT '1',
  `badge_class` varchar(100) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_seller_menu`),
  INDEX (`controller_name`),
  INDEX (`module_name`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_mp_seller_menu_lang` (
  `id_seller_menu` int(10) unsigned NOT NULL,
  `id_lang` int(10) NOT NULL,
  `label` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  FOREIGN KEY (id_seller_menu) references _PREFIX_kb_mp_seller_menu (id_seller_menu) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

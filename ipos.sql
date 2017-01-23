create database if not exists ipos;
use ipos;

create table if not exists login 
(
person_id int unsigned not null,
dt datetime not null,
ip varbinary(16) not null default '0',
proxy text not null,
os varchar(20) not null default ''
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists login_tmp
(
person_id int unsigned not null,
dt datetime not null,
err_cnt tinyint unsigned not null default '0',
ok tinyint(1) not null default '0',
ip varbinary(16) not null default '0',
session_id varchar(40) not null default '',
proxy text not null,
os varchar(20) not null default '',
PRIMARY KEY (person_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

CREATE TABLE if not exists `employees` (
  `usrname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `role` varchar(255) NOT NULL,
  UNIQUE KEY (`usrname`),
  KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

INSERT INTO employees (usrname, password, person_id, deleted, role) VALUES ('test', '$2a$06$9qNZF/ce7SrGMm3/NbUbje4IKOVHUHir4EyyUtWIM8QU2r3XiwBIq', '1', '0', 'admin');

CREATE TABLE  if not exists `modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY (`desc_lang_key`),
  UNIQUE KEY (`name_lang_key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_config', 'module_config_desc', '100', 'config'),
('module_customers', 'module_customers_desc', '10', 'customers'),
('module_employees', 'module_employees_desc', '80', 'employees'),
('module_giftcards', 'module_giftcards_desc', '90', 'giftcards'),
('module_items', 'module_items_desc', '20', 'items'),
('module_item_kits', 'module_item_kits_desc', '30', 'item_kits'),
('module_receivings', 'module_receivings_desc', '60', 'receivings'),
('module_reports', 'module_reports_desc', '50', 'reports'),
('module_sales', 'module_sales_desc', '70', 'sales'),
('module_suppliers', 'module_suppliers_desc', '40', 'suppliers');

-- --------------------------------------------------------
CREATE TABLE  if not exists `permissions` (
  `permission_id` varchar(255) NOT NULL,
  `num` smallint NOT NULL UNIQUE AUTO_INCREMENT,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `num`) VALUES
('reports', '0'),
('reports_giftcard', '1'),
('reports_suppliers', '2'),
('reports_categories', '3'),
('reports_payments', '4'),
('customers', '5'),
('customers_delete', '6'),
('customers_update', '7'),
('customers_insert', '8'),
('employees', '9'),
('employees_delete', '10'),
('employees_update', '11'),
('employees_insert', '12'),
('giftcards', '13'),
('giftcards_delete', '14'),
('giftcards_update', '15'),
('giftcards_insert', '16'),
('items', '17'),
('items_delete', '18'),
('items_update', '19'),
('items_insert', '20'),
('item_kits', '21'),
('item_kits_delete', '22'),
('item_kits_update', '23'),
('item_kits_insert', '24'),
('receivings', '25'),
('receivings_delete', '26'),
('receivings_update', '27'),
('receivings_insert', '28'),
('sales', '29'),
('sales_delete', '30'),
('sales_update', '31'),
('sales_insert', '32'),
('suppliers', '33'),
('suppliers_delete', '34'),
('suppliers_update', '35'),
('suppliers_insert', '36'),
('config', '37'),
('stock', '38'),
('grants', '39');


-- --------------------------------------------------------

--
-- Table structure for table `grants`
--

CREATE TABLE  if not exists `grants` (
  `role` varchar(255) NOT NULL,
  `permission` binary(8) NOT NULL DEFAULT 0x0000000000000000,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Dumping data for table `grants`
--
-- --------------------------------------------------------

INSERT INTO `grants` (`role`, `permission`) VALUES
('admin', 0xffffffffffffffff);


CREATE TABLE  if not exists `app_config` (
  `k` varchar(255) NOT NULL,
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Dumping data for table `app_config`
--

INSERT INTO `app_config` (`k`, `val`) VALUES
-- app
('kg_barcode', '5'),
-- general
('company_logo', ''),
('company', 'ipos'),
('address', '123 Nowhere street'),
('website', ''),
('email', 'admin@126.com'),
('phone', '555-555-5555'),
('fax', ''),
('return_policy', 'Test'),
('default_tax_1_name', ''),
('default_tax_1_rate', ''),
('default_tax_2_name', ''),
('default_tax_2_rate', ''),
('tax_included', '0'),
('default_sales_discount', '0'),
('receiving_calculate_average_price', '0'),
('company_start', '20150101'),
-- locale
('currency_symbol', '$'),
('currency_side', '0'),
('currency_decimals', '2'),
('kg_decimals', '2'),
('tax_decimals', '2'),
('decimal_point', '.'),
('thousands_separator', ','),
('timezone', 'America/New_York'),
('dateformat', 'm/d/Y'),
('timeformat', 'H:i:s'),
-- barcode
('barcode_type', 'Code128'),
('barcode_quality', '100'),
('barcode_width', '250'),
('barcode_height', '50'),
('barcode_font', 'Arial'),
('barcode_font_size', '10'),
('barcode_content', 'id'),
('barcode_generate_if_empty', '0'),
('barcode_first_row', 'category'),
('barcode_second_row', 'item_code'),
('barcode_third_row', 'unit_price'),
('barcode_num_in_row', '2'),
('barcode_page_width', '100'),      
('barcode_page_cellspacing', '20'),
-- stock
('stock_location_1', ''),
-- receipt
('invoice_default_comments', 'This is a default comment'),
('receipt_show_taxes', '0'),
('show_total_discount', '1'),
('print_silently', '1'),
('print_header', '0'),
('print_footer', '0'),
('sales_invoice_format', ''),
('recv_invoice_format', ''),
('order_invoice_format', ''),
('ret_invoice_format', '');
-- --------------------------------------------------------

--
-- Table structure for table `stock_locations`
--

CREATE TABLE  if not exists `stock_locations` (
  `location_id` int(10) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`),
  UNIQUE KEY (`location_name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

INSERT INTO `stock_locations` ( `deleted`, `location_name` ) VALUES ('0', 'stock');


CREATE TABLE  if not exists `person` (
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` int(1) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`first_name`, `last_name`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`) VALUES
('Mr', 'test', 1, '555-555-5555', 'test@test.com', 'Address 1', '', '', '', '', '', '', 0);

-- --------------------------------------------------------


CREATE TABLE  if not exists `customers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `discount` int(3) NOT NULL DEFAULT '0',
  `taxable` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY (`account_number`),
  KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


--
-- Table structure for table `suppliers`
--

CREATE TABLE  if not exists `suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `agency_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `account_number` (`account_number`, `company_name`),
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

-- --------------------------------------------------------
--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`person_id`, `company_name`, `agency_name`, `account_number`, `deleted`) VALUES
(1, 'self', '', '', 0);

--
-- Table structure for table `items`
-- 

CREATE TABLE  if not exists `items` (
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `supplier_id` int(10) NOT NULL DEFAULT '1',
  `item_number` varchar(18) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `cost_discount` int(3) NOT NULL DEFAULT '0',
  `sale_discount` int(3) NOT NULL DEFAULT '0',
  `reorder_level` int(10) NOT NULL DEFAULT '0',
  `pic` char(40) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `tax_name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY (`item_number`),
  KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
-- --------------------------------------------------------

--
-- Table structure for table `items_taxes`
--

CREATE TABLE  if not exists `items_taxes` (
  `name` varchar(255) NOT NULL,
  `percent` decimal(6,3) NOT NULL,
  UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
-- --------------------------------------------------------

--
-- Table structure for table `item_kits`
-- 

CREATE TABLE  if not exists `item_kits` (
  `item_kit_id` int(10) NOT NULL,
  `discount` int(3) NOT NULL DEFAULT '0',
  `item_number` varchar(18) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_kit_id`),
  UNIQUE KEY (`item_number`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

-- --------------------------------------------------------

CREATE TABLE  if not exists `item_kit_items` (
  `item_kit_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  PRIMARY KEY (`item_kit_id`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `item_quantities`
--

CREATE TABLE IF NOT EXISTS `item_quantities` (
  `item_id` int(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `receivings`
--

CREATE TABLE IF NOT EXISTS `recv` (
  `recv_id` int(10) NOT NULL AUTO_INCREMENT,
  `recv_date` datetime NOT NULL,
  `order_person` int(10) NOT NULL,
  `recv_person` int(10) NOT NULL DEFAULT '-1',
  `invoice_number` varchar(32) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`recv_id`),
  KEY `order_person` (`order_person`),
  UNIQUE KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


CREATE TABLE IF NOT EXISTS `recv_items` (
  `recv_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `line` int(3) NOT NULL,
  `order_quantity` int(10) NOT NULL DEFAULT '1',
  `recv_quantity` int(10) NOT NULL DEFAULT '0',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0',
  `discount` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`recv_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `giftcards`
--

CREATE TABLE IF NOT EXISTS `giftcards` (
  `record_time` datetime NOT NULL,
  `giftcard_id` int(10) NOT NULL AUTO_INCREMENT,
  `giftcard_number` char(18) NOT NULL,
  `val` decimal(10,2) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `person_id` int(10) NOT NULL DEFAULT '-1',
  `emp_id` int(10) NOT NULL,
  PRIMARY KEY (`giftcard_id`),
  UNIQUE KEY `giftcard_number` (`giftcard_number`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

CREATE TABLE IF NOT EXISTS `giftcard_charge` (
  `record_time` datetime NOT NULL,
  `giftcard_id` int(10) NOT NULL,
  `val` int(10) NOT NULL,
  `person_id` int(10) NOT NULL DEFAULT '-1',
  `emp_id` int(10) NOT NULL,
  KEY (`giftcard_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;


--
-- Table structure for table `sales`
--

CREATE TABLE IF NOT EXISTS `sales` (
  `sale_date` datetime NOT NULL,
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `cm_id` int(10) DEFAULT NULL,
  `emp_id` int(10) NOT NULL,
  `comment` text DEFAULT NULL,
  `invoice_number` varchar(32) NOT NULL,
  PRIMARY KEY (`sale_id`),
  KEY `cm_id` (`cm_id`),
  KEY `emp_id` (`emp_id`),
  KEY `sale_date` (`sale_date`),
  UNIQUE KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `sales_items`
--

CREATE TABLE IF NOT EXISTS `sale_items` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `quantity` decimal(10,2) NOT NULL DEFAULT '0',
  `cost_price` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  KEY `sale_id` (`sale_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `sales_items_taxes`
--

CREATE TABLE IF NOT EXISTS `sale_item_tax` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(6,3) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`name`),
  KEY `sale_id` (`sale_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

--
-- Table structure for table `sales_payments`
--

CREATE TABLE IF NOT EXISTS `sale_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `invoice` varchar(32) NOT NULL DEFAULT '',
  `payment_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`payment_type`),
  KEY `sale_id` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

CREATE TABLE IF NOT EXISTS `sale_suspend` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`val` text NOT NULL,
	KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
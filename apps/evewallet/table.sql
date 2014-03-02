/* SYNTAX: mysql -useandb -pPASSWORD < ~/proj/apps/evewallet/table.sql */
USE seandb;

/* used by the server to execute push commands: write the user it's being sent to */
CREATE TABLE `eve_wallet_history` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key_id` INT(40),	/* UID, effectively. cannot be changed(?), whereas the vcode can be. */
    `txn_date` varchar(20) NOT NULL,
    `ref_id` bigint(20) NOT NULL,
    `ref_type` varchar(255) NOT NULL,
    `owner_name_1` varchar(255) NOT NULL,
    `owner_name_2` varchar(255) NOT NULL,
    `arg_name_1` varchar(255) NOT NULL,
    `amount` float(20),
    `balance` float(20),
    `reason` varchar(255) NOT NULL,
    `tax_receiver_id` int(20) NOT NULL,
    `tax_amount` float(20),
    `last_activity_ts`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `hash` varchar(40),
    PRIMARY KEY `id` (`id`),
    UNIQUE KEY `hash` (`hash`),
    KEY `key_id` (`key_id`)
) ENGINE=InnoDB;

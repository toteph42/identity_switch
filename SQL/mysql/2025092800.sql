--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
--
--  Created with phpmyadmin

ALTER TABLE `identity_switch` DISABLE KEYS;
ALTER TABLE `identity_switch`
	CHANGE `imap_pwd` `imap_pwd` varchar(128) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `imap_user`,
	ADD `smtp_user` varchar(64) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `notify_timeout`,
	ADD `smtp_pwd` varchar(128) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `smtp_user`;
UPDATE `identity_switch` SET `smtp_user` = `imap_user`, `smtp_pwd` = `imap_pwd`;
ALTER TABLE `identity_switch` ENABLE KEYS;	
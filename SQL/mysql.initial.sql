--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch2/blob/master/LICENSE
--
--  Created with phpmyadmin
--  Optimized with: https://sqli.to/

SET @s = (SELECT IF(
 EXISTS(
 SELECT 1
 FROM INFORMATION_SCHEMA.COLUMNS
 WHERE table_name = 'identities'
 AND table_schema = DATABASE()
 AND column_name = 'identity_switch_prefs'
 ),
 'SELECT 1',
 'ALTER TABLE `identities` ADD `identity_switch_prefs` LONGTEXT COLLATE utf8mb4_unicode_ci NULL AFTER `html_signature`'
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

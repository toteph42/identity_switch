--
-- 	Identity switch RoundCube Bundlee
--
--	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch2/blob/master/LICENSE
--
--  Created with phpmyadmin
--  Optimized with: https://sqli.to/

ALTER TABLE `identities` ADD `identity_switch_prefs` LONGTEXT COLLATE 'utf8mb4_unicode_ci' NULL AFTER `html_signature`;

--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 Forian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
--
--  Created with phpmyadmin

ALTER TABLE
	`identity_switch`
CHANGE `imap_pwd` `imap_pwd` VARCHAR(128);
	
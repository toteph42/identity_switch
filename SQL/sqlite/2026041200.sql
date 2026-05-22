--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch2/blob/master/LICENSE
--
-- Created with: https://brunocassol.com/mysql2sqlite/
-- Optimized with: https://sqli.to/

BEGIN TRANSACTION;
ALTER TABLE `identities` ADD COLUMN `identity_switch_prefs` TEXT NULL;
COMMIT;
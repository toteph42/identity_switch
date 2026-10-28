--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch2/blob/master/LICENSE
--
-- Created with: https://www.sqlines.com/online
-- Optimized with: https://sqli.to/

DO $$
BEGIN
 IF NOT EXISTS (
 SELECT 1 FROM information_schema.columns 
 WHERE table_name = 'identities' 
 AND table_schema = current_schema() 
 AND column_name = 'identity_switch_prefs'
 ) THEN
 ALTER TABLE identities ADD COLUMN identity_switch_prefs TEXT COLLATE "utf8mb4_unicode_ci" NULL;
 END IF;
END$$;
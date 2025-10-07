--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
--
-- Created with: https://sqliteonline.com/

ALTER TABLE identity_switch
    RENAME COLUMN imap_pwd TO imap_pwd,
    ADD smtp_user TEXT,
    ADD smtp_pwd TEXT;

UPDATE identity_switch SET smtp_user = imap_user, smtp_pwd = imap_pwd;
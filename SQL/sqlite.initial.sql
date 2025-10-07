--
-- 	Identity switch RoundCube Bundle
--
--	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
-- 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
--
-- Created with: https://sqliteonline.com/
-- Optimized with: https://sqli.to/

CREATE TABLE IF NOT EXISTS `identity_switch` (
 `id` INTEGER PRIMARY KEY AUTOINCREMENT,
 `user_id` INTEGER NOT NULL,
 `iid` INTEGER NOT NULL,
 `label` TEXT NOT NULL,
 `flags` INTEGER NOT NULL DEFAULT 0,
 `imap_user` TEXT,
 `imap_pwd` TEXT,
 `imap_host` TEXT,
 `imap_port` INTEGER DEFAULT NULL,
 `imap_delim` TEXT,
 `newmail_check` INTEGER DEFAULT 300,
 `notify_timeout` INTEGER DEFAULT 10,
 `smtp_user` TEXT,
 `smtp_pwd` TEXT,
 `smtp_host` TEXT,
 `smtp_port` INTEGER DEFAULT NULL,
 `drafts` TEXT DEFAULT '',
 `sent` TEXT DEFAULT '',
 `junk` TEXT DEFAULT '',
 `trash` TEXT DEFAULT '',
 UNIQUE (`user_id`, `label`),
 FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (`iid`) REFERENCES `identities`(`identity_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
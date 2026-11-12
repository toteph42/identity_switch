<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['isw.common.label'] = 'Name of identity in selection box';
$labels['idsw.common.noedit'] = 'Identity information cannot be entered until the record has been saved for the first time';
$labels['isw.notify.caption'] = 'New messages';
$labels['isw.notify.basic'] = 'Display browser notifications for new messages';
$labels['isw.notify.desktop'] = 'Display desktop notifications for new messages';
$labels['isw.notify.timeout'] = 'Close desktop notification';
$labels['isw.notify.sound'] = 'Play sound for new messages';
$labels['isw.notify.test'] = 'Test';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Identity';
$labels['isw.notify.title'] = 'New Emails';
$labels['isw.notify.msg'] = '%d for %s';
$labels['isw.notify.err.autoplay'] = 'Cannot play sound file!'."\r\n".
								 'Please check your "Autoplay" settings for this page in your browser.';
$labels['isw.notify.err.notification'] = 'Cannot send notification!'."\r\n".
									 'Please check your "Notification" settings for this page in your browser.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Username';
$labels['idsw.imap.pwd'] = 'Password';
$labels['idsw.imap.host'] = 'Server hostname';
$labels['idsw.imap.port'] = 'Port';
$labels['idsw.imap.pass'] = 'Encryption';
$labels['idsw.imap.pass.none'] = 'None';
$labels['idsw.imap.pass.ssl'] = 'SSL';
$labels['idsw.imap.pass.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Folder hierarchy delimiter';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Username';
$labels['idsw.smtp.pwd'] = 'Password';
$labels['idsw.smtp.host'] = 'Server hostname';
$labels['idsw.smtp.tls'] = 'Secure connection (TLS)';
$labels['idsw.smtp.port'] = 'Port';
$labels['idsw.smtp.pass'] = 'Encryption';
$labels['idsw.smtp.pass.none'] = 'None';
$labels['idsw.smtp.pass.ssl'] = 'SSL';
$labels['idsw.smtp.pass.tls'] = 'TLS';


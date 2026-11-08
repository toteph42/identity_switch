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
$labels['idsw.common.noedit'] = 'Informațiile de identitate nu pot fi introduse până când înregistrarea nu a fost salvată pentru prima dată';
$labels['isw.notify.caption'] = 'Mesaje noi';
$labels['isw.notify.basic'] = 'Afișează notificări în browser pentru mesaje noi';
$labels['isw.notify.desktop'] = 'Afișează notificări pe desktop pentru mesaje noi';
$labels['isw.notify.timeout'] = 'Închide notificarea desktop';
$labels['isw.notify.sound'] = 'Redă sunet la primirea de mesaje noi';
$labels['isw.notify.test'] = 'Test';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Identitate';
$labels['isw.notify.title'] = 'Emailuri noi';
$labels['isw.notify.msg'] = '%d pentru %s';
$labels['isw.notify.err.autoplay'] = 'Fișierul audio nu poate fi redat!'."\r\n".
							'Verifică setările "Autoplay" pentru această pagină în browserul tău.';
$labels['isw.notify.err.notification'] = 'Notificarea nu poate fi trimisă!'."\r\n".
							'Verifică setările "Notificări" pentru această pagină în browserul tău.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Username';
$labels['idsw.imap.pass'] = 'Parolă';
$labels['idsw.imap.host'] = 'Hostname-ul serverului';
$labels['idsw.imap.port'] = 'Port';
$labels['idsw.imap.auth'] = 'Criptare';
$labels['idsw.imap.auth.none'] = 'Niciuna';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Delimitator ierarhie foldere';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Username';
$labels['idsw.smtp.pass'] = 'Parolă';
$labels['idsw.smtp.host'] = 'Hostname-ul serverului';
$labels['idsw.smtp.tls'] = 'Conexiune securizată (TLS)';
$labels['idsw.smtp.port'] = 'Port';
$labels['idsw.smtp.auth'] = 'Criptare';
$labels['idsw.smtp.auth.none'] = 'Niciuna';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

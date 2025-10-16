<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['idsw.common.caption'] = 'Datele identității';
$labels['idsw.common.noedit'] = 'Informațiile de identitate nu pot fi introduse până când înregistrarea nu a fost salvată pentru prima dată';
$labels['idsw.common.enabled'] = 'Activat';
$labels['idsw.common.label'] = 'Etichetă';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Username';
$labels['idsw.imap.pwd'] = 'Parolă';
$labels['idsw.imap.host'] = 'Hostname-ul serverului';
$labels['idsw.imap.port'] = 'Port';
$labels['idsw.imap.auth'] = 'Criptare';
$labels['idsw.imap.auth.none'] = 'Niciuna';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Delimitator ierarhie foldere';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Username';
$labels['idsw.smtp.pwd'] = 'Parolă';
$labels['idsw.smtp.host'] = 'Hostname-ul serverului';
$labels['idsw.smtp.tls'] = 'Conexiune securizată (TLS)';
$labels['idsw.smtp.port'] = 'Port';
$labels['idsw.smtp.auth'] = 'Criptare';
$labels['idsw.smtp.auth.none'] = 'Niciuna';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

$labels['idsw.notify.caption'] = 'Mesaje noi';
$labels['idsw.notify.allfolder'] = 'Verifică toate folderele pentru mesaje noi';
$labels['idsw.notify.basic'] = 'Afișează notificări în browser pentru mesaje noi';
$labels['idsw.notify.desktop'] = 'Afișează notificări pe desktop pentru mesaje noi';
$labels['idsw.notify.timeout'] = 'Închide notificarea desktop';
$labels['idsw.notify.sound'] = 'Redă sunet la primirea de mesaje noi';
$labels['idsw.notify.test'] = 'Test';

$labels['idsw.err.imap.user.miss'] = 'Valoarea din \'Username IMAP\' lipsește.';
$labels['idsw.err.imap.pwd.miss'] = 'Valoarea din \'Parolă IMAP\' lipsește.';
$labels['idsw.err.imap.host.miss'] = 'Valoarea din \'Hostname-ul serverului IMAP\' lipsește.';
$labels['idsw.err.imap.port.num'] = 'Valoarea din câmpul \'Port IMAP\' trebuie să fie numerică.';
$labels['idsw.err.imap.port.range'] = 'Valoarea din câmpul \'Port IMAP\' trebuie să fie între 1 și 65535.';
$labels['idsw.err.imap.delim.miss'] = 'Valoarea din \'Delimitator ierarhie foldere IMAP\' lipsește.';
$labels['idsw.err.smtp.user.miss'] = 'Valoarea din \'Username SMTP\' lipsește.';
$labels['idsw.err.smtp.pwd.miss'] = 'Valoarea din \'Parolă SMTP\' lipsește.';
$labels['idsw.err.smtp.host.miss'] = 'Valoarea din \'Hostname-ul serverului SMTP\' lipsește.';
$labels['idsw.err.smtp.port.num'] = 'Valoarea din câmpul \'Port SMTP\' trebuie să fie numerică.';
$labels['idsw.err.smtp.port.range'] = 'Valoarea din câmpul \'Port SMTP\' trebuie să fie între 1 și 65535.';

$labels['identity'] = 'Identitate';
$labels['notify.title'] = 'Emailuri noi';
$labels['notify.msg'] = '%d pentru %s';
$labels['notify.err.autoplay'] = 'Fișierul audio nu poate fi redat!'."\r\n".
								 'Verifică setările "Autoplay" pentru această pagină în browserul tău.';
$labels['notify.err.notification'] = 'Notificarea nu poate fi trimisă!'."\r\n".
									 'Verifică setările "Notificări" pentru această pagină în browserul tău.';

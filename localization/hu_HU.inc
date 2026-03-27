<?php
declare(strict_types=1);

/*
 * Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany.
 * Minden jog fenntartva
 * @license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['idsw.common.caption'] = 'Azonosító adatai';
$labels['idsw.common.noedit'] = 'Az azonosító adatai nem adhatók meg, amíg a rekordot először el nem mentette';
$labels['idsw.common.enabled'] = 'Engedélyezve';
$labels['idsw.common.label'] = 'Címke';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Felhasználónév';
$labels['idsw.imap.pwd'] = 'Jelszó';
$labels['idsw.imap.host'] = 'Szerver gépneve';
$labels['idsw.imap.port'] = 'Port';
$labels['idsw.imap.auth'] = 'Titkosítás';
$labels['idsw.imap.auth.none'] = 'Nincs';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Mappahierarchia elválasztó';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Felhasználónév';
$labels['idsw.smtp.pwd'] = 'Jelszó';
$labels['idsw.smtp.host'] = 'Szerver gépneve';
$labels['idsw.smtp.tls'] = 'Biztonságos kapcsolat (TLS)';
$labels['idsw.smtp.port'] = 'Port';
$labels['idsw.smtp.auth'] = 'Titkosítás';
$labels['idsw.smtp.auth.none'] = 'Nincs';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

$labels['idsw.notify.caption'] = 'Új üzenetek';
$labels['idsw.notify.allfolder'] = 'Minden mappa ellenőrzése új üzenetek után';
$labels['idsw.notify.basic'] = 'Böngésző értesítések megjelenítése új üzeneteknél';
$labels['idsw.notify.desktop'] = 'Asztali értesítések megjelenítése új üzeneteknél';
$labels['idsw.notify.timeout'] = 'Asztali értesítés bezárása';
$labels['idsw.notify.sound'] = 'Hangjelzés új üzeneteknél';
$labels['idsw.notify.test'] = 'Teszt';

$labels['idsw.err.imap.user.miss'] = 'Az \'IMAP Felhasználónév\' értéke hiányzik.';
$labels['idsw.err.imap.pwd.miss'] = 'Az \'IMAP Jelszó\' értéke hiányzik.';
$labels['idsw.err.imap.host.miss'] = 'Az \'IMAP Szerver gépneve\' értéke hiányzik.';
$labels['idsw.err.imap.port.num'] = 'Az \'IMAP Port\' mező értékének számnak kell lennie.';
$labels['idsw.err.imap.port.range'] = 'Az \'IMAP Port\' mező értékének 1 és 65535 között kell lennie.';
$labels['idsw.err.imap.delim.miss'] = 'Az \'IMAP Mappahierarchia elválasztó\' értéke hiányzik.';
$labels['idsw.err.smtp.user.miss'] = 'Az \'SMTP Felhasználónév\' értéke hiányzik.';
$labels['idsw.err.smtp.pwd.miss'] = 'Az \'SMTP Jelszó\' értéke hiányzik.';
$labels['idsw.err.smtp.host.miss'] = 'Az \'SMTP Szerver gépneve\' értéke hiányzik.';
$labels['idsw.err.smtp.port.num'] = 'Az \'SMTP Port\' mező értékének számnak kell lennie.';
$labels['idsw.err.smtp.port.range'] = 'Az \'SMTP Port\' mező értékének 1 és 65535 között kell lennie.';

$labels['identity'] = 'Azonosító';
$labels['notify.title'] = 'Új e-mailek';
$labels['notify.msg'] = '%d ehhez: %s';
$labels['notify.err.autoplay'] = 'Nem sikerült lejátszani a hangfájlt!'."\r\n".
'Kérjük, ellenőrizze a böngésző "Automatikus lejátszás" beállításait ennél az oldalnál.';
$labels['notify.err.notification'] = 'Nem sikerült elküldeni az értesítést!'."\r\n".
'Kérjük, ellenőrizze a böngésző "Értesítés" beállításait ennél az oldalnál.';

<?php
declare(strict_types=1);

/*
 * Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany.
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
*/
$labels = [];

$labels['isw.common.label'] = 'Az identitás neve a kiválasztó mezőben';
$labels['idsw.common.noedit'] = 'Az azonosító adatai nem adhatók meg, amíg a rekordot először el nem mentette';
$labels['isw.notify.caption'] = 'Új üzenetek';
$labels['isw.notify.basic'] = 'Böngésző értesítések megjelenítése új üzeneteknél';
$labels['isw.notify.desktop'] = 'Asztali értesítések megjelenítése új üzeneteknél';
$labels['isw.notify.timeout'] = 'Asztali értesítés bezárása';
$labels['isw.notify.sound'] = 'Hangjelzés új üzeneteknél';
$labels['isw.notify.test'] = 'Teszt';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Azonosító';
$labels['isw.notify.title'] = 'Új e-mailek';
$labels['isw.notify.msg'] = '%d ehhez: %s';
$labels['isw.notify.err.autoplay'] = 'Nem sikerült lejátszani a hangfájlt!'."\r\n".
					'Kérjük, ellenőrizze a böngésző "Automatikus lejátszás" beállításait ennél az oldalnál.';
$labels['isw.notify.err.notification'] = 'Nem sikerült elküldeni az értesítést!'."\r\n".
					'Kérjük, ellenőrizze a böngésző "Értesítés" beállításait ennél az oldalnál.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Felhasználónév';
$labels['idsw.imap.pass'] = 'Jelszó';
$labels['idsw.imap.host'] = 'Szerver gépneve';
$labels['idsw.imap.port'] = 'Port';
$labels['idsw.imap.pass'] = 'Titkosítás';
$labels['idsw.imap.pass.none'] = 'Nincs';
$labels['idsw.imap.pass.ssl'] = 'SSL';
$labels['idsw.imap.pass.tls'] = 'TLS';
$labels['idsw.imap.delimiter'] = 'Mappahierarchia elválasztó';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Felhasználónév';
$labels['idsw.smtp.pass'] = 'Jelszó';
$labels['idsw.smtp.host'] = 'Szerver gépneve';
$labels['idsw.smtp.tls'] = 'Biztonságos kapcsolat (TLS)';
$labels['idsw.smtp.port'] = 'Port';
$labels['idsw.smtp.pass'] = 'Titkosítás';
$labels['idsw.smtp.pass.none'] = 'Nincs';
$labels['idsw.smtp.pass.ssl'] = 'SSL';
$labels['idsw.smtp.pass.tls'] = 'TLS';

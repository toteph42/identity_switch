<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['isw.common.label'] = 'Nome dell\'identità nella casella di selezione';
$labels['idsw.common.noedit'] = 'Le informazioni sull\'identità non possono essere inserite finché il record non è stato salvato per la prima volta';
$$labels['isw.notify.caption'] = 'Nuovi messaggi';
$labels['isw.notify.basic'] = 'Mostra notifiche del browser per i nuovi messaggi';
$labels['isw.notify.desktop'] = 'Mostra notifiche desktop per i nuovi messaggi';
$labels['isw.notify.timeout'] = 'Chiudi notifica desktop';
$labels['isw.notify.sound'] = 'Riproduci suono per i nuovi messaggi';
$labels['isw.notify.test'] = 'Test';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Identità';
$labels['isw.notify.title'] = 'Nuove email';
$labels['isw.notify.msg'] = '%d per %s';
$labels['isw.notify.err.autoplay'] = 'Impossibile riprodurre il file audio!'."\r\n".
					'Controlla le impostazioni di "Riproduzione automatica" per questa pagina nel tuo browser.';
$labels['isw.notify.err.notification'] = 'Impossibile inviare la notifica!'."\r\n".
					'Controlla le impostazioni di "Notifiche" per questa pagina nel tuo browser.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Nome utente';
$labels['idsw.imap.pass'] = 'Password';
$labels['idsw.imap.host'] = 'Hostname del server';
$labels['idsw.imap.port'] = 'Porta';
$labels['idsw.imap.auth'] = 'Crittografia';
$labels['idsw.imap.auth.none'] = 'Nessuna';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Delimitatore gerarchia cartelle';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Nome utente';
$labels['idsw.smtp.pass'] = 'Password';
$labels['idsw.smtp.host'] = 'Hostname del server';
$labels['idsw.smtp.tls'] = 'Connessione sicura (TLS)';
$labels['idsw.smtp.port'] = 'Porta';
$labels['idsw.smtp.auth'] = 'Crittografia';
$labels['idsw.smtp.auth.none'] = 'Nessuna';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['idsw.common.caption'] = 'Dati dell\'identità';
$labels['idsw.common.noedit'] = 'Le informazioni sull\'identità non possono essere inserite finché il record non è stato salvato per la prima volta';
$labels['idsw.common.enabled'] = 'Abilitato';
$labels['idsw.common.label'] = 'Etichetta';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Nome utente';
$labels['idsw.imap.pwd'] = 'Password';
$labels['idsw.imap.host'] = 'Hostname del server';
$labels['idsw.imap.port'] = 'Porta';
$labels['idsw.imap.auth'] = 'Crittografia';
$labels['idsw.imap.auth.none'] = 'Nessuna';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Delimitatore gerarchia cartelle';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Nome utente';
$labels['idsw.smtp.pwd'] = 'Password';
$labels['idsw.smtp.host'] = 'Hostname del server';
$labels['idsw.smtp.tls'] = 'Connessione sicura (TLS)';
$labels['idsw.smtp.port'] = 'Porta';
$labels['idsw.smtp.auth'] = 'Crittografia';
$labels['idsw.smtp.auth.none'] = 'Nessuna';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

$labels['idsw.notify.caption'] = 'Nuovi messaggi';
$labels['idsw.notify.allfolder'] = 'Controlla tutte le cartelle per nuovi messaggi';
$labels['idsw.notify.basic'] = 'Mostra notifiche del browser per i nuovi messaggi';
$labels['idsw.notify.desktop'] = 'Mostra notifiche desktop per i nuovi messaggi';
$labels['idsw.notify.timeout'] = 'Chiudi notifica desktop';
$labels['idsw.notify.sound'] = 'Riproduci suono per i nuovi messaggi';
$labels['idsw.notify.test'] = 'Test';

$labels['idsw.err.imap.user.miss'] = 'Valore mancante in \'Nome utente IMAP\'.';
$labels['idsw.err.imap.pwd.miss'] = 'Valore mancante in \'Password IMAP\'.';
$labels['idsw.err.imap.host.miss'] = 'Valore mancante in \'Hostname del server IMAP\'.';
$labels['idsw.err.imap.port.num'] = 'Il valore nel campo \'Porta IMAP\' deve essere numerico.';
$labels['idsw.err.imap.port.range'] = 'Il valore nel campo \'Porta IMAP\' deve essere compreso tra 1 e 65535.';
$labels['idsw.err.imap.delim.miss'] = 'Valore mancante in \'Delimitatore gerarchia cartelle IMAP\'.';
$labels['idsw.err.smtp.user.miss'] = 'Valore mancante in \'Nome utente SMTP\'.';
$labels['idsw.err.smtp.pwd.miss'] = 'Valore mancante in \'Password SMTP\'.';
$labels['idsw.err.smtp.host.miss'] = 'Valore mancante in \'Hostname del server SMTP\'.';
$labels['idsw.err.smtp.port.num'] = 'Il valore nel campo \'Porta SMTP\' deve essere numerico.';
$labels['idsw.err.smtp.port.range'] = 'Il valore nel campo \'Porta SMTP\' deve essere compreso tra 1 e 65535.';

$labels['identity'] = 'Identità';
$labels['notify.title'] = 'Nuove email';
$labels['notify.msg'] = '%d per %s';
$labels['notify.err.autoplay'] = 'Impossibile riprodurre il file audio!'."\r\n".
								 'Controlla le impostazioni di "Riproduzione automatica" per questa pagina nel tuo browser.';
$labels['notify.err.notification'] = 'Impossibile inviare la notifica!'."\r\n".
									 'Controlla le impostazioni di "Notifiche" per questa pagina nel tuo browser.';

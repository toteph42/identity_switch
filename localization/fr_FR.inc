<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['isw.common.label'] = 'Nom de l\'identité dans la liste déroulante';
$labels['idsw.common.noedit'] = 'Les informations liées à l\'identité ne peuvent être saisies qu\'après le premier enregistrement';
$labels['isw.notify.caption'] = 'Nouveaux messages';
$labels['isw.notify.basic'] = 'Afficher les notifications du navigateur en cas de nouveau message';
$labels['isw.notify.desktop'] = 'Afficher les notifications sur le bureau en cas de nouveau message';
$labels['isw.notify.timeout'] = 'Fermeture de la notification du bureau';
$labels['isw.notify.sound'] = 'Diffusion d\'un son lors de la réception d\'un nouveau message';
$labels['isw.notify.test'] = 'Test';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Identité';
$labels['isw.notify.title'] = 'Nouveaux messages';
$labels['isw.notify.msg'] = '%d pour %s';
$labels['isw.notify.err.autoplay'] = 'Le fichier son ne peut pas être joué !'."\r\n".
						'Vérifier vos paramètres "Autoplay" pour cette page de votre navigateur.';
$labels['isw.notify.err.notification'] = 'La notification ne peut être envoyée !'."\r\n".
						'Vérifier vos paramètres "Notification"  pour cette page de votre navigateur.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Nom d\'utilisateur';
$labels['idsw.imap.pass'] = 'Mot de passe';
$labels['idsw.imap.host'] = 'Nom du serveur IMAP';
$labels['idsw.imap.port'] = 'Port IMAP';
$labels['idsw.imap.pass'] = 'Chiffrement';
$labels['idsw.imap.pass.none'] = 'None';
$labels['idsw.imap.pass.ssl'] = 'SSL';
$labels['idsw.imap.pass.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Séparateur de dossier';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Nom d\'utilisateur';
$labels['idsw.smtp.pass'] = 'Mot de passe';
$labels['idsw.smtp.host'] = 'Nom du serveur SMTP';
$labels['idsw.smtp.tls'] = 'Connexion sécurisée (TLS)';
$labels['idsw.smtp.port'] = 'Port SMTP';
$labels['idsw.smtp.pass'] = 'Encryption';
$labels['idsw.smtp.pass.none'] = 'None';
$labels['idsw.smtp.pass.ssl'] = 'SSL';
$labels['idsw.smtp.pass.tls'] = 'TLS';

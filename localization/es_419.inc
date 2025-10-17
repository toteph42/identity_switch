<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2025 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['idsw.common.caption'] = 'Datos de identidad';
$labels['idsw.common.noedit'] = 'La información de identidad no puede introducirse hasta que el registro haya sido guardado por primera vez';
$labels['idsw.common.enabled'] = 'Habilitado';
$labels['idsw.common.label'] = 'Etiqueta';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Nombre de usuario';
$labels['idsw.imap.pwd'] = 'Contraseña';
$labels['idsw.imap.host'] = 'Hostname del servidor';
$labels['idsw.imap.port'] = 'Puerto';
$labels['idsw.imap.auth'] = 'Encriptación';
$labels['idsw.imap.auth.none'] = 'Ninguna';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Delimitador de jerarquía de carpetas';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Nombre de usuario';
$labels['idsw.smtp.pwd'] = 'Contraseña';
$labels['idsw.smtp.host'] = 'Hostname del servidor';
$labels['idsw.smtp.tls'] = 'Conexión segura (TLS)';
$labels['idsw.smtp.port'] = 'Puerto';
$labels['idsw.smtp.auth'] = 'Encriptación';
$labels['idsw.smtp.auth.none'] = 'Ninguna';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

$labels['idsw.notify.caption'] = 'Nuevos mensajes';
$labels['idsw.notify.allfolder'] = 'Revisar todas las carpetas para nuevos mensajes';
$labels['idsw.notify.basic'] = 'Mostrar notificaciones del navegador para nuevos mensajes';
$labels['idsw.notify.desktop'] = 'Mostrar notificaciones de escritorio para nuevos mensajes';
$labels['idsw.notify.timeout'] = 'Cerrar la notificación de escritorio';
$labels['idsw.notify.sound'] = 'Reproducir sonido al recibir nuevos mensajes';
$labels['idsw.notify.test'] = 'Probar';

$labels['idsw.err.imap.user.miss'] = 'Falta el valor en \'Nombre de usuario IMAP\'.';
$labels['idsw.err.imap.pwd.miss'] = 'Falta el valor en \'Contraseña IMAP\'.';
$labels['idsw.err.imap.host.miss'] = 'Falta el valor en \'Hostname del servidor IMAP\'.';
$labels['idsw.err.imap.port.num'] = 'El valor del campo \'Puerto IMAP\' debe ser numérico.';
$labels['idsw.err.imap.port.range'] = 'El valor del campo \'Puerto IMAP\' debe estar entre 1 y 65535.';
$labels['idsw.err.imap.delim.miss'] = 'Falta el valor en \'Delimitador de jerarquía de carpetas IMAP\'.';
$labels['idsw.err.smtp.user.miss'] = 'Falta el valor en \'Nombre de usuario SMTP\'.';
$labels['idsw.err.smtp.pwd.miss'] = 'Falta el valor en \'Contraseña SMTP\'.';
$labels['idsw.err.smtp.host.miss'] = 'Falta el valor en \'Hostname del servidor SMTP\'.';
$labels['idsw.err.smtp.port.num'] = 'El valor del campo \'Puerto SMTP\' debe ser numérico.';
$labels['idsw.err.smtp.port.range'] = 'El valor del campo \'Puerto SMTP\' debe estar entre 1 y 65535.';

$labels['identity'] = 'Identidad';
$labels['notify.title'] = 'Nuevos correos';
$labels['notify.msg'] = '%d para %s';
$labels['notify.err.autoplay'] = 'No se puede reproducir el archivo de sonido!'."\r\n".
								 'Por favor, verifica la configuración de "Reproducción automática" de esta página en tu navegador.';
$labels['notify.err.notification'] = 'No se puede enviar la notificación!'."\r\n".
									 'Por favor, verifica la configuración de "Notificaciones" de esta página en tu navegador.';

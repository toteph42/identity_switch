<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */
$labels = [];

$labels['isw.common.label'] = 'Nombre de la identidad en el cuadro de selección';
$labels['isw.notify.caption'] = 'Nuevos mensajes';
$labels['isw.notify.basic'] = 'Mostrar notificaciones del navegador para nuevos mensajes';
$labels['isw.notify.desktop'] = 'Mostrar notificaciones de escritorio para nuevos mensajes';
$labels['isw.notify.timeout'] = 'Cerrar la notificación de escritorio';
$labels['isw.notify.sound'] = 'Reproducir sonido al recibir nuevos mensajes';
$labels['isw.notify.test'] = 'Probar';
$labels['isw.used.prefs'] = 'Preferences managed by dentity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Identidad';
$labels['isw.notify.title'] = 'Nuevos correos';
$labels['isw.notify.msg'] = '%d para %s';
$labels['isw.notify.err.autoplay'] = 'No se puede reproducir el archivo de sonido!'."\r\n".
					'Por favor, verifica la configuración de "Reproducción automática" de esta página en tu navegador.';
$labels['isw.notify.err.notification'] = 'No se puede enviar la notificación!'."\r\n".
					'Por favor, verifica la configuración de "Notificaciones" de esta página en tu navegador.';

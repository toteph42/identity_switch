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
$labels['idsw.common.noedit'] = 'Идентификационные данные нельзя ввести, пока запись не будет сначала сохранена';
$labels['isw.notify.caption'] = 'Новые письма';
$labels['isw.notify.basic'] = 'Показывать уведомления в браузере при новом письме';
$labels['isw.notify.desktop'] = 'Показывать уведомления на рабочем столе при новом письме';
$labels['isw.notify.timeout'] = 'Закрыть уведомление на рабочем столе';
$labels['isw.notify.sound'] = 'Проиграть звук при новом письме';
$labels['isw.notify.test'] = 'Тест';
$labels['isw.used.prefs'] = 'Preferences managed by identity_switch plugin';
$labels['isw.hide.prefs'] = 'Preferences were hidden according to plugin configuration.';
$labels['isw.identity'] = 'Личность';
$labels['isw.notify.title'] = 'Новые письма';
$labels['isw.notify.msg'] = '%d для %s';
$labels['isw.notify.err.autoplay'] = 'Не могу проиграть звуковой файл!'."\r\n".
					'Пожалуйста, проверьте настройки "Автовоспроизведение" для этой страницы в вашем браузере.';
$labels['isw.notify.err.notification'] = 'Не могу отправить уведомление!'."\r\n".
					'Пожалуйста, проверьте настройки "Уведомление" для этой страницы в вашем браузере.';

$labels['idsw.imap.caption'] = 'IMAP';
$labels['idsw.imap.user'] = 'Имя пользователя';
$labels['idsw.imap.pass'] = 'Пароль';
$labels['idsw.imap.host'] = 'Имя сервера';
$labels['idsw.imap.port'] = 'Порт';
$labels['idsw.imap.auth'] = 'Шифрование';
$labels['idsw.imap.auth.none'] = 'Нет';
$labels['idsw.imap.auth.ssl'] = 'SSL';
$labels['idsw.imap.auth.tls'] = 'TLS';
$labels['idsw.imap.delim'] = 'Разделитель папок';

$labels['idsw.smtp.caption'] = 'SMTP';
$labels['idsw.smtp.user'] = 'Имя пользователя';
$labels['idsw.smtp.pass'] = 'Пароль';
$labels['idsw.smtp.host'] = 'Имя сервера';
$labels['idsw.smtp.tls'] = 'Защищенное соединение (TLS)';
$labels['idsw.smtp.port'] = 'Порт';
$labels['idsw.smtp.auth'] = 'Шифрование';
$labels['idsw.smtp.auth.none'] = 'Нет';
$labels['idsw.smtp.auth.ssl'] = 'SSL';
$labels['idsw.smtp.auth.tls'] = 'TLS';

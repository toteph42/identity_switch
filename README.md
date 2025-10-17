# identity switch plugin for Roundcube

![](https://img.shields.io/packagist/v/toteph42/identity_switch.svg)
![](https://img.shields.io/packagist/l/toteph42/identity_switch.svg)
![](https://img.shields.io/packagist/dt/toteph42/identity_switch.svg)

This plugin is based on the [ident_switch](https://github.com/dougluce/ident_switch "ident_switch") plugin. It is completly rewritten and additional features have been added.

This plugin allows users to switch between different accounts in a single Roundcube session like this:

![Screenshot example](./assets/Pic01.png "Identity selection")

### Where to start ###
* In settings interface create new identity.
* For all identities except default you will see new section of settings - "Data of your identity" (see screenshot below). Enter data required to connect to  remote server. Don't forget to check **Enabled** check box.
* After you have created at least one identity with active plugin you will see combobox in the top right corner instead of plain text field with account name. It will allows you to switch to another account.

### Settings ###

![Plugin settings](./assets/Pic02.png "Connection settings")

* **Enabled** - Enables plugin (i.e. account switching) for this identity.
* **Label** - Text that will be displayed in drop down list for this identity. 
* **IMAP**
    * **Username** - User name used for IMAP servers.
    * **Password** - Password used for IMAP servers. It's stored encrypted in database.
    * **Server host name** - Host name for imap server. If left blank 'localhost' will be used.
    * **Encryption** - Connection security (None, SSL or TLS).
    * **Port** - Port on server to connect to. If left blank 143 will be used.
    * **Delimiter** - IMAP folder delimiter.
* **SMTP**
    * **Username** - User name used for SMTP servers. This should normally be the same value as used for IMAP server.
    * **Password** - Password used for SMTP servers. It's stored encrypted in database. This should normally be the same value as used for IMAP password.
    * **Server host name** - Host name for imap server. If left blank 'localhost' will be used.
    * **Encryption** - Connection security (None, SSL or TLS).
    * **Port** - Port on server to connect to. If left blank 25 will be used.
    
### Settings for active identity ###

![Plugin settings](./assets/Pic04.png "New mail checking")

If you've selected an identity (or use the default identity), you may change settings for new-mail check cycle in `Settings` -> `Preference` -> `User Interface`.

![Plugin settings](./assets/Pic05.png "Notification settings")

If you've selected an identity (or use the default identity), you may change settings for notification settings in `Settings` -> `Preference` -> `Mailbox view`.

### Additional settings ###

![Plugin settings](./assets/Pic03.png "Notification settings")

* **Check all folders...** - Select this option, if you want all folders to be check for new mails. 
* **Display browser notification...** - Select this option, if you want to get a changed icon for this site. Please be aware, that the icon will change only one time, even if there a new mails for multiple identities available (until next new-mail check-cycle is started).
* **Display desktop notification...** - Select this option, if you want to get a desktop notification about how many new mails were available. Please be aware, you need to allow your mail server site in your browser configuration to send notifications to your desktop.
* **Close desktop notification** - Specify how many seconds should be visible before it is automatically been closed.
* **Play sound...** - Select this option, if you want a sound notification. Please be aware, that only sound will be played one time only, even if there are new mails for multiple identities available (until next new-mail check cycle is started). If you hear no sound playing, please check your browser settings, if auto-play of sound files is enabled.
* **Refresh...** - Specify the new-mail check cycle in minutes.

![Plugin settings](./assets/Pic06.png "E-Mail notification")

If you receive new mails, the number of new mails will be shown in identity selection menue.

### Configuration ###

There is a file `config.inc.php.dist` in the plugin directory available with mutiple configuration parameters. This file can be used to specify some configuration settings. Please copy file to `config.inc.php` and change there your settings.

If you want to change sound, icon or desktop icon, please checkout `alert.mp3`, `alert.ico` and `alert.gif` in sub-directory `assets`.

### Locking configuration ###

You may use the `dont_override` configuration option in your **RoundCube** configuration file `config/config.inc.php` to lock some options from being overriden. This plugins supports the following options to be protected:

* `draft_mbox`, `sent_mbox`, `junk_mbox`, `trash_mbox` - User cannot override preconfigured special folder name.
* `check_all_folders` - User cannot override preconfigured flag to check all folders.
* `newmail_notifier_basic`, `newmail_notifier_desktop`, `newmail_notifier_sound` - User cannot override preconfigured notification setting.

### Performance ###

New mail checking is performed in background asynchronously. This has the effect that the new mail counter is not always updated immediately after login - it may take some time before this has been performed. It heavily depends on the number of identities you're using.

If you've select **Check all folders**, this has a huge impact on the time new-mail checking need to collect information. If you have hundreds of folders in your mail box, each of the boxes will be check for new mails.

Please don't forget to set `Special Folders` in `Settings` -> `Preferences`. All folders specified there (and their sub-folders) will be excluded from new-mail checking.

### Version compatibility ###

* Versions 1.x / 2.x - for Roundcube v1.6.x requires PHP version >= 8.0.0.

### Limitations ###

This plugin only supports `Classic`, `Elatic`, `Larry` and `Hivemail` skin. If you wan't to get another skin to be supoorted, then please contact me. I can add support for other skin if you buy for it.

### Migration from ident_switch plugin ###

If you've installed the `ident_switch` plugin, there is a migration file available in `SQL` subdirectory which copies the content of the old table to the new table and deletes the `ident_switch` table. To make this happen, you should first install this plugin (during installation a table `identity_switch` will automatically be created) and then you should appy `SQL/migrate.sql`. 

### License ###

This plugin is released under the [GNU General Public License v3.0](./LICENSE).

### Debugging ###

If you encounter problems with plugin, take a look at the option available in `config.inc.php`. 

If you encounter **connection problems**, it is a good idea to enable **RoundCube** available debugging options:

```php
// Log IMAP conversation to <log_dir>/imap.log or to syslog
$config['imap_debug'] = true;
// Log sent messages to <log_dir>/sendmail.log or to syslog
$config['smtp_log'] = true;

// Log SMTP conversation to <log_dir>/smtp or to syslog
$config['smtp_debug'] = true;
```
Then switch to the identity which does not work as expected. In log files you'll see how **RoundCube** is trying to establish connection.

### Donation ###

If you like this software and you want support my work, feel free to send me a donation:

<a href="https://www.paypal.com/donate/?hosted_button_id=DS6VK49NAFHEQ" target="_blank" rel="noopener">   <img src="https://www.paypalobjects.com/en_US/DK/i/btn/btn_donateCC_LG.gif" alt="Donate with PayPal"/> </a>

[[List of changes](./Changes.md)] 

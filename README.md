# Identity switch plugin for Roundcube

![](https://img.shields.io/packagist/v/toteph42/identity_switch.svg)
![](https://img.shields.io/packagist/l/toteph42/identity_switch.svg)
![](https://img.shields.io/packagist/dt/toteph42/identity_switch.svg)

This plugin allows users to switch between different identities in a single **RoundCube** session like this:

![Plugin settings](./assets/Pic06.png "Identity selection")

If you receive new mails, the number of new mails will be shown in identity selection menue.

### Where to start ###
* In settings interface create new identity.
* Be sure to fill in the **Name of identity**. 
* After you have created at least one identity you will see combobox instead of plain text field where your account name was displayed before. There you can switch between the identifities.

### Settings ###

From now on all user preferences and connection settings will belong to the active identity. To indicate for which dentity you're changing preferences, you will see the identity in the header line in format `[ Identity: {Active Identity} ]` in the `Preference` menue. 

There are some new setting available in `Preference` -> `Mailbox view`.

![Plugin settings](./assets/Pic05.png "Notification settings")

* **Check all folders...** - Select this option, if you want all folders to be check for new mails. 
* **Display browser notification...** - Select this option, if you want to get a changed icon for this site. Please be aware, that the icon will change only one time, even if there a new mails for multiple identities available (until next new-mail check-cycle is started).
* **Display desktop notification...** - Select this option, if you want to get a desktop notification about how many new mails were available. Please be aware, you need to allow your mail server site in your browser configuration to send notifications to your desktop.
* **Close desktop notification** - Specify how many seconds should be visible before it is automatically been closed.
* **Play sound...** - Select this option, if you want a sound notification. Please be aware, that only sound will be played one time only, even if there are new mails for multiple identities available (until next new-mail check cycle is started). If you hear no sound playing, please check your browser settings, if auto-play of sound files is enabled.

If you select an identity to edit, then you'll see an additional box name **Preferences managed by identity_switch plugin**  with all user preferences used for this identity. You may edit the seeting and save them to the idenity. 

Please note:
* All passwords were show as ●●●●●●●●. If you enter a new password, it will be stored encrypted.
* If you set the `cfg_hide_pref` to `true`, then all preferences will be hidden. 
* Most of the settings were defined in `default.inc.php`. For a descption about allowed content please refer to`config/default.inc.php`. 
* You may change any of the given preferences or you may also add new user preferences for this identity.
* All preferences will override global **RoundCube** settings.

### Configuration ###

There is a file `default.inc.php` in the plugin directory available. This file can be used to specify some individual preferences. Please copy file to `config.inc.php` and change there your preferences. 

If you want to change sound, icon or desktop icon, please checkout `alert.mp3`, `alert.ico` and `alert.gif` in sub-directory `assets`.

### Locking configuration ###

You may use the `dont_override` configuration option in your **RoundCube** configuration file `config/config.inc.php` to lock most settings from being overriden. This plugins supports all settings which can be locked in **RoundCube**.

### Performance ###

New mail checking is performed in background asynchronously. This has the effect that the new mail counter is not always updated immediately after login - it may take some time before this has been performed. It heavily depends on the number of identities you're using.

If you've select **Check all folders**, this has a huge impact on the time new-mail checking need to collect information. If you have hundreds of folders in your mail box, each of the boxes will be checked for new mails.

### Version compatibility ###

Versions 3.x - for **RoundCube** v1.6.x requires PHP version >= 8.0.0.

### Limitations ###

This plugin only supports `Classic`, `Elatic`, `Larry` and `Hivemail` skin. If you wan't to get another skin to be supoorted, then please contact me. I can add support for other skin if you buy for it.

### License ###

This plugin is released under the [GNU General Public License v3.0](./LICENSE).

### Debugging ###

If you encounter problems with plugin, take a look at the option available in `default.inc.php`. 

If you encounter **connection problems**, it is a good idea to enable **RoundCube** available debugging options.


### Donation ###

If you like this software and you want support my work, feel free to send me a donation:

<a href="https://www.paypal.com/donate/?hosted_button_id=DS6VK49NAFHEQ" target="_blank" rel="noopener">   <img src="https://www.paypalobjects.com/en_US/DK/i/btn/btn_donateCC_LG.gif" alt="Donate with PayPal"/> </a>

[[List of changes](./Changes.md)] 

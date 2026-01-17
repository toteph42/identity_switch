# Changelog identity switch plugin

## Release 2.0.17

* Enabled plugin to use any port available for asynchroneous background system

## Release 2.0.16

* Moved 'interval' parameter to domain configuration
* Fixed minor bug in configuration handling
* Logging modified to get a better insigth in processing
* Support for RoundCube 1.7
* Automatic creation of symlink in public_html directory to enable call of php scripts

## Release 2.0.15

* Added font-size für small dropdown
* Set position in dropdown selection list

## Release 2.0.14

* Bug fix

## Release 2.0.13

* Added switch for dropdown section size

## Release 2.0.12

* Removed Fixed issue #70

## Release 2.0.11

* Fixed issue #70

## Release 2.0.10

* Fixed copatibility errors with PHP 8.4.x

## Release 2.0.9

* Depreciated messages removed

## Release 2.0.8

* Typo in README.md
* Added contribution from Andrei Apostu

## Release 2.0.7

* Error in MySQL databse initial instructions

## Release 2.0.6

* Error in PostGres databse update instructions

## Release 2.0.5

* Error in handling of default user credentials

## Release 2.0.4

* Recover on_smtp_conect() function

## Release 2.0.3

* Fixing data base schemes

## Release 2.0.2

* Fixing data base schemes

## Release 2.0.1

* Now you can use seperate IMAP and SMTP credentials
* Enables saving of disabled identity
* Fixed sqlite problems

## Release 1.1.21

* Fixed issue #38
* Removed length field for MySQL fields

## Release 1.1.20

* Fixed missing 'enable' button

## Release 1.1.19

* Fixed PHP notice

## Release 1.1.18

* Fixed SQL error

## Release 1.1.17

* Changed: Updates on cache
* Fixed: INSERT into table

## Release 1.1.16

* Changed: Locking enhanced
* Changed: Imap user field now accepts 64 characters

## Release 1.1.15

* Fixed: Typo in compser URL
* Changed: Setting "newmail_check" to default, f no value is given

## Release 1.1.14

* Fixed: Temp. not possible to edit / create any identity

## Release 1.1.13

* Added; Work around if login user has no assigned identity

## Release 1.1.12

* Added: Additional inteer cast when loading parameter "refresh_interval" from RC
* Chaged: Removing # of unread mail from selected identity when switching

## Release 1.1.11

* Changed: Active identity is now also show in list of selctable identities

## Release 1.1.10

* Changed: Updates to menu bar

## Release 1.1.9

* Fixed: Typo in identity_switch_newmails.php:88

## Release 1.1.8

* Fixed: Typo in identity_switch_newmails.php:88
* Added: Object check in identity_switch.php:523
* Added: Sleep() call to create_menu() * was too fast

## Release 1.1.7

* Update: Mariadb version 10.6.19 support

## Release 1.1.6

* Fixed: Typo in identity_switch.php

## Release 1.1.5

* Added: Debug section in README.MD
* Added: Configuration parameter 'wait'

## Release 1.1.4

* Fixed: Closing stream error
* Added: Update of unseen couter from leaving identity 
* Added: Cast 'newmail_check' variable to integer

## Release 1.1.3

* Changed: Updating newmail count on identity switch improved

## Release 1.1.2

* Fixed: Typo in catch_newmails()

## Release 1.1.1

* Added: Possibility to create/edit default identity label for drop down menu
* Fixed: Saving notification mode for default identity 

## Release 1.1.0

* Changed: README.MD
* Changed: SQL scripts

## Release 1.1

* Changed: Preconfiguration of identity settings modified
* Fixed: Disabled identities now ignored
* Fixed: Unseen count sometimes wrong, when switching identity
* Fixed: Some typos in code
* Changed renamed from identy_switch to identity_switch
* Fixed: Modification / deletion of default identity in setting handled properly

## Release 1.0.45

* Fixed: 'Delay' not set in identity_switch_newmail.php

## Release 1.0.44

* Added: New mail check delay configuration parameter
* Fixed: Logging in identity_switch_newmail.php

## Release 1.0.43

* Fixed: php8.1-fpm.sock break down resulting in missing return records

## Release 1.0.41

* Fixed: PHP warning regarding missing special folders

## Release 1.0.40

* Fixed: PHP warning regarding missing special folders

## Release 1.0.39

* Fixed: PHP warning regarding missing special folders

## Release 1.0.38

* Fixed: Unssen counter return from catch casted to integer.

## Release 1.0.37

* Fixed: INSTALL_PATH in identity_switch_newmails.php

## Release 1.0.36

* Fixed: In classic skin, selection list of identities was not in foreground

## Release 1.0.35

* Fixed: INSTALL_PATH in identity_switch_newmails.php
* Changed: Position of dropdown in classic skin

## Release 1.0.34

* Added: Some more comments in config.inc.php.dist
* Fixed: In some cases "special folders" were empty. Fix will handle.

## Release 1.0.33

* Fixed: PHP Warning in identity_switch.php:363

## Release 1.0.32

* Changed: "imap_pwd" now 128 bytes long

## Release 1.0.31

* Fixed: Hostname instead of identity label in desktop test notification shown

## Release 1.0.30

* Added: Debug support extended
* Added: New mail check now waiting for data file

## Release 1.0.29

* Fixed: Loop error in ident_switch_newmail.php
* Fixed: Usage of special characters '%' for SMTP hosts in config/config.inc.php

## Release 1.0.28

* Added: Mentioning limitiations in README.md
 
## Release 1.0.27

* Fixed: 'interval' not loaded when creating new identity
* Fixed: Some config.php.dist parameter not set as default for identity

## Release 1.0.26

* Added: IMAP folder delimiter configuration parameter
* Added: Wildcard for domain in configuration parameter
* Fixed: Typo in README.md

## Release 1.0.25

* French translation provided by @rglemaire

## Release 1.0.24

* Fixed: Bug in preferences (identity edit)
* Fixed: Bug when trying to send mails with default identity

## Release 1.0.23

* Skipped

## Release 1.0.22

* Skipped

## Release 1.0.21

* Fixed: Minor bug in refresh interval
* Added: Debug configuration option

## Release 1.0.20

* Fixed: Special folder handling
* Fxied: "Check all folders" flag not accepted

## Release 1.0.19

* Fixed isse #14: Bug in composer.json

## Release 1.0.18

* Fixed issue #8: CodeShakingSheep pull request merged to fix MySQL DB table creation for migration from ident_switch plugin
* Fixed issue #9: Protocol selection for SMTP now possible
* Fixed issue #10: CodeShakingSheep pull request merged to fix ident_switch migration DB INSERT statement
* Fixed issue #13: PHP 8.1 warnings for 'show_real_foldernames'
* Fixed issue #13: PHP 8.1 warnings for unknown special folder names

## Release 1.0.17

* identityswitch menu is now automatically closed if user clicks somewhere on screen

## Release 1.0.15

* Bug fixed for 'show_real_foldernames'.
* Special message before record has been created added.

## Release 1.0.14

* Error message prefixed by "idsw".
* If identity is set as default, hen disable identity_switch handling.
* If a new record is created, it is not possible to return any error message due to RoundCube design.
* Fixed some error messages.

## Release 1.0.13

* Thank to https://github.com/HLFH .
* type 'tsl' fixed to 'tls'.
* some README.MD types fixed.
* support for SMTP array in config.inc.php ($config['smtp_host']).

## Release 1.0.12

* 'dont_override' option documented.
* Bug fixed when changing standard identity name.
* Some bugs fixed when trying to edit identity record.

## Release 1.0.11

* Some typos fixed.

## Release 1.0.10

* When switching identity unseen counter update is forced.

## Release 1.0.9

* Notification handling slightly modified.

## Release 1.0.8

* Some fixes to README.md.

## Release 1.0.7

* Unseen count on active account moved to identity_switch_do_switch().

## Release 1.0.6

* Fixing some unread counter problems.
* Bug in SQlite and postgres SQL files.

## Release 1.0.5

* Some more changes to CSS file.
* Changed CSS and JS compressor.
* Creation of identity selection menu restricted to mail template.

## Release 1.0.4

* Function create_menu() moved back to identity_switch.php.

## Release 1.0.3

* Updates to CSS files.

## Release 1.0.2

* Updates to CSS files.

## Release 1.0.1

* SMTP not working properly for default account.

## Release 1.0.0

* Code completly rewritten.
* New-mail check added.
* Ntification about new mail added.


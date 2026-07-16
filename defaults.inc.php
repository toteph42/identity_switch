<?php
/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

$config['identity_switch.config'] = [

 	// If you want to use your own configuration, copy this file to config.inc.php
	// Please note:
	// - If you specify 'default', configuration from RoundCube is used.

	// Catch all. This defines default configuration values for all identities,
	// If specify your own "catch all" in config.inc.php, it will override this section.
	'*' => [

		// General configuration parameters which will be handled by plugin

		// Specify no. of retries for reading data from mail server.
		// Default is 5 times.
		'cfg_retries' 				=> 5,

		// Specify number of microseconds between each new mail check.
		// Default is 333 micoseconds.
		'cfg_delay'					=> 333,

		// Dropdown selection line size.
		// Defaults to 34px
		'cfg_lsize'					=> 34,

		// Set logging level for 'logs/identity_switch.log'.
		// 0 = Error messages (default) will be written to log file as well as to RoundCube error log file
		// 1 = Exceution messages
		// 2 = Additional debug mesages
		'cfg_logging'				=> 0,

		// Hide user preference window when editing an identity
		'cfg_hide_prefs'			=> false,

		// debug IMAP connections for all identities
		'cfg_imap_debug'			=> false,
		// debug SMTP connections for all identities - not supported on idenity level
		// 'cfg_smtp_debug'			=> false,

		// ------------------------------------------------------------------------------------------------------

		// Default identity label name
		'isw_label'					=> '',

		// Some new notification handling settings introduced by plugin

		// refresh interval
		'isw_refresh_interval'		=> 'default',
		// check all folders
		'isw_check_all_folders' 	=> 'default',

		// Basic notification
		'isw_notification_basic' 	=> false,
		// Desktop notification
		'isw_notification_desktop' 	=> false,
		// Desktop notification time out (seconds)
		'isw_notification_timeout' 	=> 10,
		// Sound notification flag
		'isw_notification_sound' 	=>  false,

		// ------------------------------------------------------------------------------------------------------

		// Additional RoundCube configuration parameters. When switching to the matching identity,
		// all parameters which were specified here or in config.inc.php will be handled by identity_switch.
		// Please note:
		// - These parameter will overide RoundCube settings.
		// - If you specify 'default', configuration from RoundCube is used (parameter without "isw_" prefix).
		// - If you do not specify parameter, no user specifc configuration will be applied.
		// - Passwords must be in clear text. They will be encryted during loading of configuration.

		// Some IMAP settings used during storage connection initialization

		'isw_imap_host'				=> 'default',
		// The folowing parameter will be extracted from isw_imap_host automatically:
		// 'isw_imap_ssl'			=> '',
		// 'isw_imap_port'			=> '',
		'isw_imap_user' 			=> 'default',
		'isw_imap_pass' 			=> 'default',
		'isw_imap_auth_pw'			=> 'default',
		'isw_imap_auth_type'		=> 'default',
		'isw_imap_skip_deleted'		=> 'default',
		'isw_imap_force_caps'		=> 'default',
		'isw_imap_disabled_caps'	=> 'default',
		'isw_imap_timeout'			=> 'default',
		'isw_imap_auth_cid'			=> 'default',
		'isw_imap_socket_options'	=> null,
		'isw_imap_timeout'			=> 0,
		'isw_imap_driver'			=> 'imap',
		'isw_imap_message_flags'	=> [ 'JUNK' => 'Junk', 'NONJUNK' => 'NonJunk' ],
		'isw_imap_attempt'			=> 1,
		'isw_imap_retry'			=> false,
		'isw_imap_delimiter'		=> 'default',

		// Some SMTP settings used during sending e-mail initialization

		'isw_smtp_host'				=> 'default',
		// The folowing parameter will be extracted from isw_smtp_host automatically:
		// 'isw_smtp_ssl'			=> '',
		// 'isw_smtp_port'			=> '',
		'isw_smtp_user' 			=> 'default',
		'isw_smtp_pass' 			=> 'default',
		'isw_smtp_auth_cid'			=> 'default',
		'isw_smtp_auth_pw'	 		=> 'default',
		'isw_smtp_auth_type'	 	=> 'default',
		'isw_smtp_helo_host'		=> 'default',
		'isw_smtp_timeout'			=> 'default',
		'isw_smtp_conn_options'		=> 'default',
		'isw_gssapi_context'		=> null,
		'isw_gssapi_cn'				=> null,

		// Some OAUTH settings used during sending e-mail initialization
		// This is not in place - see issue #78

		/*
		'isw_oauth_provider' 		=> 'default',
		'isw_oauth_provider_name' 	=> 'default',
		'isw_oauth_client_id' 		=> 'default',
		'isw_oauth_client_secret'	=> 'default',
		'isw_oauth_config_uri' 		=> 'default',
		'isw_oauth_issuer' 			=> 'default',
		'isw_oauth_jwks_uri' 		=> 'default',
		'isw_oauth_auth_uri' 		=> 'default',
		'isw_oauth_pkce'			=> 'default',
		'isw_oauth_token_uri' 		=> 'default',
		'isw_oauth_identity_uri' 	=> 'default',
		'isw_oauth_logout_uri' 		=> 'default',
		'isw_oauth_timeout' 		=> 'default',
		'isw_oauth_verify_peer' 	=> 'default',
		'isw_oauth_scope' 			=> 'default',
		'isw_oauth_auth_parameters'	=> 'default',
		'isw_oauth_identity_fields' => 'default',
		'isw_oauth_login_redirect' 	=> 'default',
		'isw_oauth_cache' 			=> 'default',
		'isw_oauth_cache_ttl' 		=> 'default',
		'isw_oauth_user_create_map' => 'default',
		'isw_oauth_password_claim'	=> 'default',
		'isw_oauth_auth_type' 		=> 'default',
		*/
	],

	// ---------------------------------------------------------------------------------------------------------------

	// Configuration settings for other data types.
 	// Appropriate set of values is searched by mapping domain of e-mail (from identity) to e-mail.

	// Catch domain part of e-mail setting
	// 'domain.tld' => [
	//		'imap_host' 			=> 'ssL://imap.another.tld:993',
	//		'smtp_host' 			=> 'tls://smtp.another.tld:465',
	//		'refresh_interval' 		=> 1000,
	// ],

	// Catch specific e-mail settings
	// 'admin@example.net' 			=> [
	//		'imap_user'				=> 'Captain',
	//		'imap_pass'				=> 'stavrosmuellerbeta',
	// ],

];


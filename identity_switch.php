<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

/**
 *
 * 	Data structure
 *
 *
 * 	cfg 						general configuration data
 * 		iid						active identity
 * 		show_prefs				show user preferences
 * 		default_iid				default identity for this user
 * 		logging					loggin level to 'logs/identity_switch.log'
 * 		check					allow new mail checking
 * 		delay					delay between each new mail check
 * 		retries					specify no. of retries for reading data from mail server
 * 		wait					max. number of seconds to wait for response from identity_switch_newmails.php
 * 		export	 				export file name
 * 		import	  				data exchange file
 * 		fp						file pointer
 *  	lsize					dropdown selection line size
 *
 * 	[n]							identity confciguration data
 *		_unseen					# of unseen messages
 *		_checked_last			last time checked
 *		_notify					notify user flag
 *
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_prefs.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_cfg.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_newmails.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_migrate.php';

class identity_switch extends identity_switch_cfg
{
    public $task = '?(?!login).*';

	/**
	 * 	Initialize Plugin
	 *
	 * 	{@inheritDoc}
	 * 	@see rcube_plugin::init()
	 */
	function init(): void
	{
## unset($_SESSION[identity_switch_cfg::TABLE]);
		$rc = rcmail::get_instance();

		// identity switch hooks and actions
		$this->add_hook('startup', 						  [ $this, 'on_startup' ]);
		$this->add_hook('render_page', 					  [ $this, 'on_render_page' ]);
		$this->add_hook('template_object_composeheaders', [ $this, 'on_object_composeheaders' ]);
		$this->add_hook('storage_connect', 				  [ $this, 'on_storage_connect' ]);
		$this->add_hook('smtp_connect', 				  [ $this, 'on_smtp_connect' ]);
		$this->add_hook('oauth_login', 				  	  [ $this, 'on_oauth' ]);

		// register our own action handler
		$this->register_action('identity_switch_do',  	  [ $this, 'do_switch' ]);


		if (!isset($_SESSION[identity_switch_cfg::TABLE]))
		{
			// check for symlink in public_html for RoundCube >= 1.6
			$link = \INSTALL_PATH.'public_html/plugins/identity_switch';
		    $fs = new Filesystem();
			if (!$fs->exists($link))
			{

				if (!$fs->exists(\INSTALL_PATH.'public_html/'.$link))
				{

		    		$path = \INSTALL_PATH.'plugins/identity_switch';

		    		// check for Windows OS
					if ('\\' === \DIRECTORY_SEPARATOR)
		    			$fs->symlink($path, $link);
			    	else
			    		$fs->symlink(Path::makeRelative($path, Path::getDirectory($link)), $link);

			    	self::write_log(__FILE__, __LINE__, 'Creating symlink "'.$link.'" to "'.$path.'"', true);
		    	}
		    }

		    // check for migration
	    	new identity_switch_migrate();
		}

		// load settings
		parent::init();

		// notification hooks and action
		if ($rc->output instanceof rcmail_output_html)
			$rc->output->include_script('../../plugins/identity_switch/assets/identity_switch.js');

		// new mail hooks and action
		$this->add_hook('refresh', 			  			  [ $this, 'check_unseen' ]);
		$this->add_hook('ready',	 					  [ $this, 'check_unseen' ]);

		$this->include_stylesheet('assets/identity_switch.css');
	}

	/**
	 * 	Startup script
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function on_startup(array $args): array
	{
		$rc = rcmail::get_instance();

		// not default user?
		if (isset($_SESSION['username']) && strcasecmp($rc->user->data['username'], $_SESSION['username']) !== 0)
		{
			if ($args['task'] == 'mail')
			{
				$this->add_texts('localization/');
				$rc->config->set('create_default_folders', false);
			}
		}

		return $args;
	}

	/**
	 * 	Build selection menu
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function on_render_page(array $args): array
	{
		$rc = rcmail::get_instance();

		if ($rc->task != 'mail' || $args['template'] != 'mail')
			return $args;

		$this->add_texts('localization');

		// build table
		$acc = [];
		$iid = self::get('cfg', 'iid');
		foreach (self::get() as $k => $v)
			// label available?
			if (is_numeric($k) && $v['isw_label'] <> '')
				$acc[rcube::Q($v['isw_label'])] = [ 'iid' => $k, 'unseen' => $v['_unseen'] ];

		// sort identities
		ksort($acc);

		// find position of iid

		$off = 0;
		foreach ($acc as  $a)
		{
			// identity found?
			if ($a['iid'] == $iid)
				break;
			$off++;
		}

		// get dropdown line size
		$size = $this->get('cfg', 'lsize');

		// render UI if user has extra accounts
		if (count($acc) > 1)
		{
			$div = '<div id="identity_switch_menu"'.
				   ' class="form-control"'.
				   ' onclick="identity_switch_toggle_menu('.$off * $size.')">'.
				   rcube::Q(self::get($iid, 'isw_label')).
				   '<div id="identity_switch_dropdown" style="line-height:'.$size.'px"><ul>';
			foreach ($acc as $name => $r)
				$div .= '<li onclick="identity_switch_run('.$r['iid'].');"><a href="#">'.$name.
					  	'<span id="identity_switch_unseen_'.$r['iid'].'"'.
					  	' class="unseen" style="top:'.($size >= 24 ? ((34 - $size)/2).'px':
					  	' 0px;line-height:initial;font-size:x-small').'">'.
				  	   	($r['iid'] == $iid ? "" : ($r['unseen'] > 0 ? $r['unseen'] : '')).'</span></a></li>';
			rcmail::get_instance()->output->add_footer($div.'</ul></div></div>');
		}

		return $args;
	}

	/**
	 * 	Perform identity switch
	 */
	function do_switch(): void
	{
        // get new identity
		self::set('cfg', 'iid', $iid = (int)rcube_utils::get_input_value('identity_switch_iid', rcube_utils::INPUT_POST));

		$_SESSION['username'] = self::get($iid, 'isw_imap_user');
		$_SESSION['password'] = self::get($iid, 'isw_imap_pass');

		rcmail::get_instance()->output->redirect( [
				'_task' => 'mail',
				'_mbox' => 'INBOX',
		] );
	}

	/**
	 * 	Change userid in composer window to proper identity
	 *
	 * 	@param array $args
	 */
	function on_object_composeheaders(array $args): void
	{
		if ($args['id'] == '_from')
		{
			$rc = rcmail::get_instance();
			if (strcasecmp($_SESSION['username'], $rc->user->data['username']) !== 0)
				$rc->output->add_script('identity_switch_fixIdent('.self::get('cfg', 'iid').');', 'docready');
		}
	}

	/**
	 * 	Open storage
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function on_storage_connect(array $args): array
	{
		$rc = rcmail::get_instance();
		$rec = self::get(self::get('cfg', 'iid'));

		foreach ( [ 'auth_type' => 0, 'skip_deleted' => 0, 'auth_cid' => 0, 'auth_pw' => 0,
					'debug' => 0, 'force_caps' => 0, 'disabled_caps' => 1, 'socket_options' => 0,
					'timeout' => 0, 'driver' => 0, 'language' => 0, 'host' => 0, 'user' => 0,
					'port' => 0, 'ssl' => 0, 'password' => 0, 'message_flags' => 1,
					'ssl_mode' => 0, 'attempt' => 0, 'retry' => 0 ] as $key => $typ)
		{
			if ($key == 'password')
				$int = 'isw_imap_pass';
			elseif ($key == 'language')
			{
				$args[$key] = self::get('cfg', $key);
			} else
				$int = 'isw_imap_'.$key;

			if (isset($rec[$int]))
				$args[$key] = $int == 'isw_imap_pass' ? $rc->decrypt($rec[$int]) :
							  ($typ ? (array)$rec[$int] : $rec[$int]);
		}

		// unsupported hack
		$_SESSION['imap_delimiter'] = $rec['isw_imap_delimiter'];

		return $args;
	}

	/**
	 * 	Send mail
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function on_smtp_connect(array $args): array
	{
		$rc = rcmail::get_instance();
		$rec = self::get(self::get('cfg', 'iid'));

		foreach ( [ 'smtp_host' => 0, 'smtp_user' => 0, 'smtp_pass' => 0, 'smtp_auth_cid' => 0,
					'smtp_auth_pw' => 0, 'smtp_auth_type' => 0, 'smtp_helo_host' => 0,
					'smtp_timeout' => 0, 'smtp_conn_options' => 1,
					// smtp_auth_callbacks - not supported
					'gssapi_context' => 0, 'gssapi_cn' => 0, ] as $key => $typ)
		{
			$int = 'isw_'.$key;

			// build host?
			if ($key == 'smtp_host')
			{
				$host = (string)$rec[$int];
				if (isset($rec['isw_smtp_ssl']))
					$host = $rec['isw_smtp_ssl'].'://'.$host;
				if (isset($rec['isw_smtp_port']))
					$host .= ':'.$rec['isw_smtp_port'];
				$args[$key] = $host;
			} else
				if (isset($rec[$int]))
					$args[$key] = $key == 'smtp_pass' ? $rc->decrypt($rec[$int]) :
								  ($typ ? (array)$rec[$int] : $rec[$int]);
		}

		return $args;
	}

	/**
	 * 	Login with Oauth
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function on_oauth(array $args): array
	{
		return $args;
	}

	/**
	 * 	Check for unseen mails
	 */
	function check_unseen($args) {

		// get configuration
		if(!is_array($cfg = self::get('cfg')))
			return $args;

		// only refresh?
		if (!isset($args['action']))
			return $args;

		// single mail marked as unread
		if ($args['action'] == 'mark')
		{
			$iid = self::get('cfg', 'iid');
			$n   = $_SESSION['unseen_count']['INBOX'];
			if (self::get($iid, 'isw_check_all_folders') == '1')
			{
				foreach ($_SESSION['unseen_count'] as $k => $v)
					if ($k != 'INBOX')
						$n += $v;
			}
			switch (rcube_utils::get_input_string('_flag', rcube_utils::INPUT_POST))
			{
			case 'read':
				$n--;
				self::write_log(__FILE__, __LINE__, 'Mail flag "unread" deleted.', true);
				break;

			case 'unread':
				$n++;
				self::write_log(__FILE__, __LINE__, 'Mail flag "unread" set.', true);
				break;

			default:
				breaK;
			}

			// something changed?
			if (self::get($iid, '_unseen') <> $n)
			{
		        self::set($iid, '_unseen', $n);
	    	    self::set($iid, '_checked_last', time());
	        	self::set($iid, '_notify', true);
				self::write_log(__FILE__, __LINE__, 'Starting update on unseen counter.', true);
				self::do_unseen_update();
			}

			return $args;
		}

		if ($args['action'] && $args['action'] != 'refresh' && $args['action'] != 'getunread')
			return $args;

		self::write_log(__FILE__, __LINE__, 'Configuration loaded "'.json_encode($cfg).'".', true);

		// make a copy of our cached data
		$export = self::get();

		// check if we're outside waiting window
		$chk = 0;
		foreach ($export as $iid => $rec)
		{
			if (!is_numeric($iid))
				continue;

			if ($rec['_checked_last'] + (int)$rec['isw_refresh_interval'] < time())
				$chk++;
			else
				unset($export[$iid]);
		}

		if (!$chk)
		{
			if (!$chk)
				self::write_log(__FILE__, __LINE__, 'No accounts to check - stop checking', true);

			return $args;
		}

		self::write_log(__FILE__, __LINE__, 'Start checking '.$chk.' account(s)', true);

		if ($chk && !file_exists($cfg['export']))
		{
			// any connection available?
			if (!is_resource($cfg['fp']))
			{
				// check whether host supports SSL
				// host with port?
				if (strpos($_SERVER['HTTP_HOST'], ':'))
					$c = explode(':', $_SERVER['HTTP_HOST']);
				else
					$c = [ $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'] ];
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
					$host = 'ssl://';

				$host .= $c[0].':'.$c[1];

			    self::set('cfg', 'fp', $cfg['fp'] = new identity_switch_rpc());
				if (is_string($cfg['fp']->open($host)))
				{
					$this->write_log(__FILE__, __LINE__, 'Cannot open connection for "'.$host.'" - stop checking');
					return $args;
				}
				self::write_log(__FILE__, __LINE__, 'Host "'.$host.'" connected', true);
			}

			// save data for background sharing
			file_put_contents($cfg['export'], serialize($export));

			self::write_log(__FILE__, __LINE__, 'Cache file "'.$cfg['export'].'" created', true);

    		// prepare request (no fopen() usage because "allow_url_fopen=FALSE" may be set in PHP.INI)
			$req = '/plugins/identity_switch/identity_switch_newmails.php?iid=0&cache='.urlencode($cfg['export']);
			if (!$cfg['fp']->write($req))
			{
				if (is_resource($cfg['fp']))
					fclose($cfg['fp']);
				self::set('cfg', 'fp', $cfg['fp'] = 0);
				$this->write_log(__FILE__, __LINE__, 'Cannot write to "'.$host.'" Request: "'.$req.'" - stop checking');
				return $args;
			}
			self::write_log(__FILE__, __LINE__, 'Request started "'.$req.'"', true);
		} else
			self::write_log(__FILE__, __LINE__, 'We assume connection is still alive and export file "'.
						    $cfg['export'].'" is available', true);

		// check for data file
		$n = 0;
		while (!file_exists($cfg['import']))
		{
			if ($n++ > $cfg['wait'])
			{
				self::write_log(__FILE__, __LINE__, 'No import file found - stop checking after '.$cfg['wait'].
								' seconds waiting for "'.$cfg['import'].'"', true);
				return $args;
			}
			sleep (1);
		}

		// load data file
		self::write_log(__FILE__, __LINE__, 'Loading and deleting import file', true);
		$wrk = @file_get_contents($cfg['import']);
		@unlink($cfg['import']);

		$upd = false;

		// process data lines
		if (is_string($wrk))
		{
			foreach (explode('###', $wrk) as $line)
			{
				if (!$line)
					continue;

				$r = explode('##', $line);
				if (!is_array($r))
					continue;

				// check for error message
				if (!$r[1] && isset($r[2]))
				{
					$this->write_log(__FILE__, __LINE__, 'NewMail error: '.$r[2]);
					continue;
				}

				$rec = self::get($r[1]);

				if ($r[2] <> $rec['_unseen'] && $r[0] > $rec['_checked_last'])
				{
					$upd = true;
					if ($r[2] > $rec['_unseen'])
				 		self::set($r[1], '_notify', true);
					self::set($r[1], '_unseen', $r[2]);
				}
				if ($r[0] > $rec['_checked_last'])
					self::set($r[1], '_checked_last', $r[0]);
			}

			if ($upd)
			{
				self::write_log(__FILE__, __LINE__, 'Starting update on unseen counter.', true);
				self::do_unseen_update();
			}
		}

		return $args;
	}

	/**
	 * 	Do update on unseen counter (and notify)
	 */
	function do_unseen_update(): void
	{
		$rc = rcmail::get_instance();

		$this->add_texts('localization');

		//  control array
		$ctl    = [];
		$ctl[0] = [
					'autoplay'		=> rawurlencode($this->gettext('isw.notify.err.autoplay')),
					'notification'	=> rawurlencode($this->gettext('isw.notify.err.notification')),
					'title'			=> rawurlencode($this->gettext('isw.notify.title')),
		];

		$cnt   = 1;
		$sound = false;
		$basic = false;
		foreach (self::get() as $iid => $rec)
		{
			// skip unwanted entries
			if (!is_numeric($iid))
				continue;

			// set unseen to provide to browser
			$ctl[$cnt]['iid'] = $iid;
			$ctl[$cnt]['unseen'] = $rec['_unseen'];

			// should we notify?
			if ($rec['_notify'])
			{
				self::set($iid, '_notify', false);

				if ($rec['isw_notification_basic'] & !$basic)
				{
					$basic = true;
					$ctl[$cnt]['basic'] = 1;
				}

			    if ($rec['isw_notification_desktop'])
			    	$ctl[$cnt]['desktop'] =  [
			    		'text' 		=> rawurlencode(sprintf($this->gettext('isw.notify.msg'), $rec['_unseen'],
								  	   $rec['isw_label'])),
			    		'timeout'	=> $rec['isw_notification_timeout'],
					];

				if ($rec['isw_notification_sound'] && !$sound)
				{
					$sound = true;
					$ctl[$cnt]['sound'] = 1;
				}
			}
			$cnt++;
		}

		$rc->output->command('plugin.identity_switch_update_unseen', $ctl);
	}

}

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
 *  	lsize					dropdown selection line size
 *  	lock					lock status for tabel updates
 *  	init					initial new mail check performed
 *
 * 	[n]							identity configuration data
 *		_unseen					# of unseen messages
 *		_old					last # of unseen messages
 *		_notify					notify user flag
 *		_checked				last time checked for new mail
 *
 */

require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_prefs.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_cfg.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_migrate.php';

class identity_switch extends identity_switch_cfg
{
    public $task = '?(?!login).*';

    // true= menu has been constructed
    static private $_menu = false;

	/**
	 * 	Initialize Plugin
	 *
	 * 	{@inheritDoc}
	 * 	@see rcube_plugin::init()
	 */
	function init(): void
	{
		$rc = rcmail::get_instance();

		// identity switch hooks and actions
		$this->add_hook('startup', 						  [ $this, 'on_startup' ]);
		$this->add_hook('render_page', 					  [ $this, 'on_render_page' ]);
		$this->add_hook('template_object_composeheaders', [ $this, 'on_object_composeheaders' ]);
		$this->add_hook('storage_connect', 				  [ $this, 'on_storage_connect' ]);
		$this->add_hook('smtp_connect', 				  [ $this, 'on_smtp_connect' ]);
		$this->add_hook('oauth_login', 				  	  [ $this, 'on_oauth' ]);
		$this->add_hook('ready', 			  			  [ $this, 'check_flag' ]);

		// register our own action handler
		$this->register_action('identity_switch_run',  	  [ $this, 'switch_identity' ]);
		$this->register_action('identity_switch_newmail', [ $this, 'check_newmail' ]);

		// check for migration
		if (!isset($_SESSION[identity_switch_cfg::TABLE]))
	    	new identity_switch_migrate();

		// load settings
		parent::init();

		// notification hooks and action
		if ($rc->output instanceof rcmail_output_html)
		{
			$rc->output->include_script('../../plugins/identity_switch/assets/identity_switch.js');
			$rc->output->add_script('identity_switch_init();', 'head_top');
			$this->include_stylesheet('assets/identity_switch.css');
		}
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

		// already initialized?
		if (self::$_menu)
			return $args;

		// we're initialized
		self::$_menu = true;

		$acc = [];
		$ctl = [];
		$n   = 0;
		foreach (self::get() as $k => $v)
		{
			if (!is_numeric($k) || $v['isw_label'] == '')
				continue;
			$acc[rcube::Q($v['isw_label'])] = [ 'iid' => $k, 'unseen' => $v['_unseen'] ];
			$ctl[$n]['iid'] = $k;
			// compute delay timer
			$wait = $v['isw_refresh_interval'];
			if (time() > $v['_checked'])
				self::set($k, '_checked', time() + $wait);
			else
				$wait = $v['_checked'] - time();
			$ctl[$n++]['wait'] = $wait;
		}

		// swap initialization status
		$ctl[0]['init'] = self::get('cfg', 'init');
		self::set('cfg', 'init', true);

		// swap logging status
		$ctl[0]['logging'] = self::get('cfg', 'cfg_logging');

		// start checking for new mails
		$rc->output->command('plugin.identity_switch_newmail', $ctl);

		// build menu
		$this->add_texts('localization');

		// sort identities
		ksort($acc);

		// find position of iid
		$iid = self::get('cfg', 'iid');
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

		// render UI if user has identities to switch to
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
		$rec = self::get(self::get('cfg', 'iid'));

		foreach ( [ 'auth_type' => 0, 'skip_deleted' => 0, 'auth_cid' => 0, 'auth_pw' => 0,
					'debug' => 0, 'force_caps' => 0, 'disabled_caps' => 1, 'socket_options' => 0,
					'timeout' => 0, 'driver' => 0, 'language' => 0, 'host' => 0, 'user' => 0,
					'port' => 0, 'ssl' => 0, 'password' => 0,
					// added for buggy RC 1.7.1 -> #10221
					'pass' => 0,
					'message_flags' => 1,
					'ssl_mode' => 0, 'attempt' => 0, 'retry' => 0 ] as $key => $typ)
		{
			if ($key == 'language')
			{
				$args[$key] = self::get('cfg', $key);
				continue;
			}

			if ($key == 'ssl_mode')
				$int = 'isw_imap_ssl';
			elseif (strpos($key, 'pass') !== false)
				$int = 'isw_imap_pass';
			else
				$int = 'isw_imap_'.$key;

			if ($key == 'debug')
				$rec[$int] = self::get('cfg', 'imap_debug');

			if ($rec[$int])
				$args[$key] = $int == 'isw_imap_pass' ? rcmail::get_instance()->decrypt($rec[$int]) :
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
					// smtp_smtp_debug - not supported
					'gssapi_context' => 0, 'gssapi_cn' => 0, ] as $key => $typ)
		{
			$int = 'isw_'.$key;

			// replacement characters?
			if ($key == 'smtp_user' && $rec[$int] == '%u')
				$int = 'isw_imap_user';
			if ($key == 'smtp_pass' && $rec[$int] == '%p')
				$int = 'isw_imap_pass';

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
	 * 	Check for flagging of mails
	 */
	function check_flag($args) {

		// action available?
		if (!isset($args['action']))
			return $args;

		// move to trash?
		if ($args['action'] == 'move')
		{
			$iid = self::get('cfg', 'iid');
			$rc  = rcmail::get_instance();
			$box = $rc->config->get('trash_mbox');

			// is it trash box
			if ($box != rcube_utils::get_input_value('_target_mbox', rcube_utils::INPUT_POST))
				return $args;

			$msg = new rcube_message(rcube_utils::get_input_value('_uid', rcube_utils::INPUT_POST),
									 rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_POST));

            // message found?
            if (!empty($msg->headers))
            {
				self::set($iid, '_old', self::get($iid, '_unseen'));
				$unseen = $_SESSION['unseen_count']['INBOX'];
				// message not seen yet?
				if (!isset($msg->headers->flags['SEEN']))
				{
					$unseen--;
				    self::set($iid, '_unseen',  $unseen);
					self::swap($iid);
				}
            }

			return $args;
		}

		// mail marked as read/unread
		if ($args['action'] != 'mark')
			return $args;

		$iid 	= self::get('cfg', 'iid');
		$unseen = $_SESSION['unseen_count']['INBOX'];

		// check all folders
		if (self::get($iid, 'isw_check_all_folders') == '1' && isset($_SESSION['unseen_count']))
		{
			// we hope RoundCube has set session variable
			foreach ($_SESSION['unseen_count'] as $k => $v)
				if ($k != 'INBOX')
					$unseen += $v;
		}

		switch (rcube_utils::get_input_string('_flag', rcube_utils::INPUT_POST))
		{
		case 'read':
			$unseen--;
			self::write_log(__FILE__, __LINE__, sprintf('%03d', $iid).': Mail flag "unread" deleted', true);
			break;

		case 'unread':
			$unseen++;
			self::write_log(__FILE__, __LINE__, sprintf('%03d', $iid).': Mail flag "unread" set', true);
			break;

		default:
			breaK;
		}

		self::set($iid, '_old', self::get($iid, '_unseen'));
	    self::set($iid, '_unseen', $unseen);
		self::swap($iid);

		return $args;
	}

	/**
	 * 	Perform identity switch
	 */
	function switch_identity(): void
	{
        // get new identity
        $iid = (int)rcube_utils::get_input_value('identity_switch_iid', rcube_utils::INPUT_POST);
		// activate identity
        self::set('cfg', 'iid', $iid);

		$rec   				  = self::get($iid);
		$_SESSION['username'] = $rec['isw_imap_user'];
		$_SESSION['password'] = $rec['isw_imap_pass'];

        // send response
		rcmail::get_instance()->output->redirect( [
				'_task' => 'mail',
				'_mbox' => 'INBOX',
		], 0 );
	}

	/**
	 * 	Check for new mail
	 */
	function check_newmail()
	{
		$rc = rcmail::get_instance();

		// get iid to check
		$iid = (int)rcube_utils::get_input_value('identity_switch_iid', rcube_utils::INPUT_POST);
		$rec = self::get($iid);

	   	// we must delete storage object, to get SSL status reset
		$rc->storage = null;

		$siid = self::get('cfg', 'iid');

		// connect
		self::set('cfg', 'iid', $iid);

		$storage = $rc->get_storage();
		for ($n = 0; $n < self::get('cfg', 'retries'); $n++)
		{
			// parameters were set by on_storage_connect()
			if ($storage->connect('', '', ''))
				break;
			usleep(self::get('cfg', 'delay'));
		}
		if ($n == self::get('cfg', 'retries'))
		{
			self::write_log(__FILE__, __LINE__, sprintf('%03d', $iid).': Cannot connect to "'.
							$rec['isw_imap_ssl'].'://'.$rec['isw_imap_host'].':'.$rec['isw_imap_port'].
							'" for user "'.$rec['isw_imap_user'].'"');
			return;
		}

		// restore actual identity
		self::set('cfg', 'iid', $siid);

		// get list of all subscribed folders
		$storage = $rc->get_storage();
		$folders = [ 'INBOX' ];

		if ($rec['isw_check_all_folders'] == '1')
			$folders += $storage->list_folders_subscribed('', '*'. null, null, true);

		// drop exception folders (and their subfolders)
		foreach (rcube_storage::$folder_types as $val)
		    if (($k = array_search($val, $folders)) !== false)
				unset($folders[$k]);

		// count unseen
		$unseen = 0;
		foreach($folders as $mbox)
		{
			unset($storage->conn->data['STATUS:'.$mbox]);
			$unseen += $storage->count($mbox, 'UNSEEN', true, false);
		}

	    $storage->close();

	    if ($unseen <> $rec['_unseen'])
	    {
			self::write_log(__FILE__, __LINE__, sprintf('%03d', $iid).': '.$unseen.' new mails found'.
							' (old value: '.$rec['_unseen'].')', true);
			self::set($iid, '_old', $rec['_unseen']);
			if ($unseen > $rec['_unseen'])
		    	self::set($iid, '_notify', true);
			self::set($iid, '_unseen', $unseen);
		    self::swap($iid);
	    }
	}

	/**
	 * 	Swap unseen counter and notify
	 *
	 * 	@param 	$iid int
	 */
	function swap(int $iid)
	{
		$rc = rcmail::get_instance();

		$this->add_texts('localization');

		//  control array
		$ctl    = [];
		$ctl[0] = [
					'autoplay'		=> rawurlencode($this->gettext('isw.notify.err.autoplay')),
					'notification'	=> rawurlencode($this->gettext('isw.notify.err.notification')),
					'title'			=> rawurlencode($this->gettext('isw.notify.title')),
					'logging'		=> self::get('cfg', 'cfg_logging'),
		];

		$rec = self::get($iid);

		// provide unseen information to browser
		$ctl[1]['iid'] 	  = $iid;
		$ctl[1]['unseen'] = $rec['_unseen'];
		$ctl[1]['old']    = $rec['_old'];

		// should we notify?
		if ($rec['_notify'])
		{
			self::set($iid, '_notify', false);

			if ($rec['isw_notification_basic'])
				$ctl[1]['basic'] = 1;

		    if ($rec['isw_notification_desktop'])
		    	$ctl[1]['desktop'] =  [
		    		'text' 		=> rawurlencode(sprintf($this->gettext('isw.notify.msg'), $rec['_unseen'],
							  	   $rec['isw_label'])),
		    		'timeout'	=> $rec['isw_notification_timeout'],
				];

			if ($rec['isw_notification_sound'])
				$ctl[1]['sound'] = 1;
		}

		$rc->output->command('plugin.identity_switch_notify', $ctl);
	}

}

<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://www.gnu.org/licenses/gpl-3.0.en.html
 */

class identity_switch_cfg extends identity_switch_prefs
{
	static protected $config = [];					// configuration array
    static protected $cache = null;					// cache handler

	const TABLE = 'identity_switch';				// where to store data in $_SESSION

	// special variables stored on RiundCube to cover paralel AJAX-Requests
	const VOLATILE = [ '_unseen', '_checked', '_notify' ];

	// log levels
	const LVL_ERROR = 0;
	const LVL_EXEC	= 1;
	const LVL_DEBUG	= 2;

	/**
	 * 	Initialize Plugin
	 *
	 * 	{@inheritDoc}
	 * 	@see rcube_plugin::init()
	 */
	function init(): void
	{
		parent::init();

		// configuration already available?
		if (isset($_SESSION[self::TABLE]))
			return;

		$rc = rcmail::get_instance();

		// first load default config
		self::_load_cfg();

		// save global configuration parameters
		foreach (self::$config as $nam => $set)
		{
			if (!is_array($set))
			{
				self::write_log(__FILE__, __LINE__, self::LVL_ERROR,
							   'Only arrays were allowed in "identity_switch.config" - stopping execution');
				exit();
			}
			foreach ($set as $k => $v)
				if (substr($k, 0, 4) == 'cfg_')
				{
					self::set('cfg', substr($k, 4), $v);
					unset(self::$config[$nam][$k]);
				} elseif (substr($k, 0, 4) != 'isw_')
					unset(self::$config[$nam][$k]);
		}

		// load identity parameter for default identity
		$r = $rc->user->get_identity();
		if (!isset($r['email']))
		{
			self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Cannot catch default user email - stopping execution');
			exit();
		}
		self::get_cfg(0, $r['email']);

		// load all other known identities
		$iid = self::get('cfg', 'iid');
		$sql = 'SELECT `identity_id`, `email`'.
			   ' FROM '.$rc->db->table_name('identities', true).
			   ' WHERE `user_id` = ? AND `del` = 0';
		$q   = $rc->db->query($sql, $rc->user->ID);
		while ($rec = $rc->db->fetch_assoc($q))
			// skip default identity and alll "non" identiy_switch entries
			if ((int)$rec['identity_id'] != $iid)
				self::get_cfg((int)$rec['identity_id'], $rec['email']);
	}

	/**
	 * 	Load default configuration
	 */
	private function _load_cfg(): void {

		// load configuration
		$rc = rcmail::get_instance();

		// first load default config
		foreach ( [ 'defaults.inc.php', 'config.inc.php' ] as $file)
		{
			parent::load_config($file);
			self::$config = array_merge(self::$config, $rc->config->get('identity_switch.config', []));
		}
	}

	/**
	 * 	Get configuration for identity
	 *
	 * 	@param 	int $iid
	 * 	@param 	string $email
	 *  @return array $rec
	 */
	protected function get_cfg(int $iid, string $email): array {

		$rc = rcmail::get_instance();

		// load default configuration?
		if (!$iid)
		{
			$r   = $rc->user->get_identity();
			$iid = (int)$r['identity_id'];
			self::set('cfg', 'iid', $iid);							// save active identity id
			self::set('cfg', 'default_iid', $iid);					// save default identity
			self::set('cfg', 'init', false);						// initial new mail check performed

			// set the proper default user data
			self::set($iid, 'isw_label', $r['name']);
			self::set($iid, 'isw_imap_user', $_SESSION['username']);
			self::set($iid, 'isw_imap_pass', $_SESSION['password']);
			self::set($iid, 'isw_imap_host', $_SESSION['storage_host']);
			self::set($iid, 'isw_imap_port', $_SESSION['storage_port']);
			self::set($iid, 'isw_imap_auth', $_SESSION['storage_ssl']);
			self::set($iid, 'isw_imap_language', '');
			self::set($iid, 'isw_refresh_interval', $rc->config->get('refresh_interval'));
		} elseif (!isset(self::$config['*']))
			self::_load_cfg();

		// load preferences
		self::load_cfg($iid, $email);

		$rec = self::get($iid);

		// 3.0.20 ->
		$chg = false;
		foreach ( [ 'isw_imap_ssl', 'isw_smtp_ssl' ] as $k)
		{
			if (isset($rec[$k]))
			{
				$k1 = substr($k, 0, 9).'auth';
				self::set($iid, $k1, $rec[$k1] = $rec[$k]);
				foreach ([ $k, substr($k, 0, 9).'auth_pw' ] as $k)
				{
					self::del($iid, $k);
					unset($rec[$k]);
					$chg = true;
				}
			}
		}
		foreach ([ 'isw_gssapi_context', 'isw_gssapi_cn' ] as $k)
		{
			self::del($iid, $k);
			unset($rec[$k]);
			$chg = true;
		}
		if ($chg)
			self::save_cfg($iid);
		// <- 3.0.20

		return $rec;
	}

	/**
	 * 	Load configuration into identity_switch cache
	 *
	 * 	@param int $iid
	 * 	@param string $email
	 */
	protected function load_cfg(int $iid, string $email): void
	{
		$rc = rcmail::get_instance();

		$cfg = self::$config['*'];
		$txt = 'catch all';
		if (isset(self::$config[$email]))
		{
			$cfg = array_merge($cfg, self::$config[$email]);
			$txt = 'full';
		}
		// get domain of identity
		elseif (($p = strstr($email, '@')) && ($dom = substr($p, 1)) && isset(self::$config[$dom]))
		{
			$cfg = array_merge($cfg, self::$config[$dom]);
			$txt = 'domain';
		}
		self::write_log(__FILE__, __LINE__, self::LVL_DEBUG,
						sprintf('%03d', $iid).': Applying predefined configuration - '.$txt.' match.');

		$did = self::get('cfg', 'default_iid');

		foreach ($cfg as $k => $v)
		{
			// do not swap temporary configuration parameters
			if (substr($k, 0, 1) == '_')
				continue;

			// parameter already set?
			if (isset($_SESSION[self::TABLE][$iid][$k]))
				continue;

			// load RoundCube default?
			if ($v == 'default')
			{
				switch ($k)
				{
				case 'isw_imap_host':
				case 'isw_imap_auth':
				case 'isw_imap_port':
				case 'isw_imap_user':
				case 'isw_imap_pass':
				case 'isw_imap_language':
					$v = self::get($did, $k);
					break;

				default:
					$v = $rc->config->get(substr($k, 4));
					break;
				}
			}

			// encrypt passwords
			if (strpos($k, 'pass') && $v != '%p')
				$v = $rc->encrypt($v);

			// split host names
			if ($k == 'isw_imap_host' || $k == 'isw_smtp_host')
			{
				$k = substr($k, 0, 9);
				if (strpos((string)$v, ':') > 0)
					$t = rcube_utils::parse_host_uri($v);
				else
					$t = [ $v, $rc->config->get($k.'ssl'), $rc->config->get($k.'port') ];
				self::set($iid, $k.'host', $t[0]);
				self::set($iid, $k.'auth', $t[1]);
				self::set($iid, $k.'port', $t[2]);
			} else
				self::set($iid, $k, $v);
		}

		// any configuration saved?
		$sql = ' SELECT `identity_switch_prefs`'.
			   ' FROM '.$rc->db->table_name('identities', true).
			   ' WHERE `identity_id` = ? AND `user_id` = ?';
		$q   = $rc->db->query($sql, $iid, $rc->user->ID);
		if (($r = $rc->db->fetch_assoc($q)) && isset($r['identity_switch_prefs']))
		{
			$prefs = (array)json_decode($r['identity_switch_prefs']);
			if (isset($prefs['isw_label']) && $prefs['isw_label'] <> '')
			{
				foreach ($prefs as $k => $v)
					self::set($iid, $k, is_object($v) ? (array)$v : $v);
				self::write_log(__FILE__, __LINE__, self::LVL_DEBUG,
								sprintf('%03d', $iid).': Updating configuration with saved data');
			}
		}
	}

	/**
	 * Save configuration
	 *
	 * @param int $iid
	 */
	protected function save_cfg(int $iid): void
	{
		$rc = rcmail::get_instance();

		$sql = 'UPDATE '.$rc->db->table_name('identities', true).
			   ' SET `identity_switch_prefs` = ? '.
			   ' WHERE `identity_id` = ? AND `user_id` = ?';

		$prefs = self::get($iid);
		foreach ($prefs as $k => $v)
			// delete internal temporary parameters
			if (substr($k, 0, 1) == '_')
				unset($prefs[$k]);
		$v;
		$q = $rc->db->query($sql, json_encode($prefs), $iid, $rc->user->ID);
		if ($rc->db->affected_rows($q) === false)
			$this->write_log(__FILE__, __LINE__, self::LVL_ERROR, sprintf('%03d', $iid).': Error saving data');
	}

	/**
	 * 	Get variable
	 *
	 * 	@param  string|int $sect
	 * 	@param  string|int $var
	 *  @param  bool
	 * 	@return string|int|bool|array|null
	 */
	protected function get(mixed $sect = null, mixed $var = null, bool $errchk = true): mixed
	{
		if (($t = gettype($sect)) != 'string' && $t != 'integer' && $t != 'NULL')
		{
			self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Variable type "'.$t.'" invalid for variable "'.$sect.'"');
			return null;
		}
		if (($t = gettype($var)) != 'string' && $t != 'integer' && $t != 'NULL')
		{
			self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Variable type "'.$t.'" invalid for variable "'.$var.'"');
			return null;
		}

		// get whole table?
		if (!$sect && !$var)
		{
			if (!isset($_SESSION[self::TABLE]))
				$_SESSION[self::TABLE] = [];

			$all = $_SESSION[self::TABLE];
			foreach ($all as $k => $v)
			{
				// is it identity?
				if (is_numeric($k))
					$all[$k] = array_merge($v, self::cache_read($k));
			}
			return $all;
		}

		// get variable?
		// special variable?
		if (in_array($var, self::VOLATILE, true))
		    return self::cache_read($sect)[$var];

		// get variable?
		if ($sect && $var && isset($_SESSION[self::TABLE][$sect][$var]))
			return $_SESSION[self::TABLE][$sect][$var];

		// get section?
		elseif (isset($_SESSION[self::TABLE][$sect]))
		    return is_int($sect) ? array_merge($_SESSION[self::TABLE][$sect], self::cache_read($sect)) :
		           $_SESSION[self::TABLE][$sect];

		// we should never go here
		if ($errchk)
		    self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Section "'.$sect.'" Variable "'.$var.'" not found!');

		return null;
	}

	/**
	 * 	Set variable in cache
	 *
	 * 	@param string|int $sect
	 * 	@param array|string|int $var
	 * 	@param string|int|bool|array $val
	 * 	@param string|int|bool $default
	 */
	protected function set(mixed $sect, mixed $var, mixed $val = null, mixed $default = null): void
	{
		if (($t = gettype($sect)) != 'string' && $t != 'integer')
		{
			self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Variable type "'.$t.'" invalid for variable "'.$sect.'"');
			return;
		}
		if (($t = gettype($var)) != 'string' && $t != 'integer' && $t != 'array')
		{
			self::write_log(__FILE__, __LINE__, self::LVL_ERROR, 'Variable type "'.$t.'" invalid for variable "'.$var.'"');
			return;
		}

		// cache special varibales
		if (in_array($var, self::VOLATILE, true))
		{
		    self::cache_write($sect, $var, $val);
		    return;
		}

		// table defied?
		if (!isset($_SESSION[self::TABLE]))
			$_SESSION[self::TABLE] = [];

		if (is_array($var))
		{
			if (!isset($_SESSION[self::TABLE][$sect]))
				$_SESSION[self::TABLE][$sect] = [];
			foreach ($var as $k => $v)
				$_SESSION[self::TABLE][$sect][$k] = is_null($v) ? $default : $v;
		} else
			$_SESSION[self::TABLE][$sect][$var] = $val;
	}

	/**
	 * 	Delete cached variable
	 *
	 * 	@param string|int $sect
	 * 	@param string $var
	 */
	protected function del(string|int|null $sect = null, ?string $var = null): void
	{
		if (!$sect && !$var)
			unset($_SESSION[self::TABLE]);

		if ($sect && $var)
			unset($_SESSION[self::TABLE][$sect][$var]);
		elseif ($sect)
			unset($_SESSION[self::TABLE][$sect]);
	}

	/**
	 * 	Cache key: per browser session and identity.
 	 * 	Thus, parallel requests of different identities never overwrite each other,
	 * 	and multiple sessions of the same user do not get in each other's way.
	 */

	/**
	 * 	Return unique key for cache
	 *
	 *	@param int $iid
	 * 	@return string
	 */
	protected function cache_key(int $iid): string
	{
    	return $iid.'_'.substr(md5(session_id()), 0, 12);
	}

	/**
	 * 	Read from cache
	 *
	 * 	@param int $iid
	 * 	@return array
	 */
	protected function cache_read(int $iid): array
	{
		// allocate cache handler
		if (!self::$cache)
	    	self::$cache = rcmail::get_instance()->get_cache('identity_switch', self::get('cfg', 'cache'), 86400, true);

		$v = self::$cache->get(self::cache_key($iid));
	    return array_merge([ '_unseen' => 0, '_checked' => 0, '_notify' => false ],
	                       is_array($v) ? $v : [] );
	}

	/**
	 * 	Write to cache
	 *
	 * 	@param int $iid
	 * 	@param string $var
	 * 	@param mixed $val
	 */
	protected function cache_write(int $iid, string $var, mixed $val): void
	{
	    $v       = $this->cache_read($iid);
	    $v[$var] = $val;
	    self::$cache->set($this->cache_key($iid), $v);
	}

	/**
	 * 	Write log message
	 *
	 * 	@param string $file		File name
	 * 	@param string $line		Line number
	 * 	@param string $txt 		Log message
	 * 	@param int	  $lvl		LVL_ERROR; LVL_EXEC (default); LVL_DEBUG
	 */
	static public function write_log(string $file, int $line, int $lvl, string $txt): void
	{
		if (!isset($_SESSION[self::TABLE]['cfg']['logging']))
			return;

		switch ($lvl)
		{
		case self::LVL_ERROR:
			rcmail::get_instance()->write_log('errors', basename($file).'('.$line.'): '.$txt);
			$e = new \Exception;
			error_log(var_export($e->getTraceAsString(), true));

		case self::LVL_EXEC:
			if ($lvl == 0 || $_SESSION[self::TABLE]['cfg']['logging'] > 0)
				rcmail::get_instance()->write_log('identity_switch', basename($file).'('.$line.'): '.$txt);
			break;

		case self::LVL_DEBUG:
			if ($_SESSION[self::TABLE]['cfg']['logging'] == 2)
				rcmail::get_instance()->write_log('identity_switch', basename($file).'('.$line.'): '.$txt);
			break;

		default:
			self::write_log($file, $line, LVL_ERROR, 'Invalid log level ('.$lvl.')');
			break;
		}
	}

}

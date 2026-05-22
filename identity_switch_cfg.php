<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

class identity_switch_cfg extends identity_switch_prefs
{
	static protected $config = [];					// configuration array

	const TABLE = 'identity_switch';				// where to store data in $_SESSION

	/**
	 * 	Initialize Plugin
	 *
	 * 	{@inheritDoc}
	 * 	@see rcube_plugin::init()
	 */
	function init(): void
	{
		parent::init();

		// configuration loaded?
		if (isset($_SESSION[self::TABLE]))
			return;

		// load configuration
		$rc = rcmail::get_instance();

		// first load default config
		foreach ( [ 'defaults.inc.php', 'config.inc.php' ] as $file)
		{
			parent::load_config($file);
			self::$config = array_merge(self::$config, $rc->config->get('identity_switch.config', []));
		}

		// save global configuration parameters
		foreach (self::$config as $nam => $set)
		{
			foreach ($set as $k => $v)
				if (substr($k, 0, 4) == 'cfg_')
				{
					self::set('cfg', substr($k, 4), $v);
					unset(self::$config[$nam][$k]);
				} elseif (substr($k, 0, 4) != 'isw_')
					unset(self::$config[$nam][$k]);
		}

		// set no identity
		self::set('cfg', 'iid',  0);

		// set export file name
		self::set('cfg', 'export', $c = $rc->config->get('temp_dir', sys_get_temp_dir()).'/isw_out.'.session_id());

		// set data file name
		self::set('cfg', 'import',  str_replace('_out', '_in', $c));

		// set file pointer
		self::set('cfg', 'fp', 0);

		// load identity parameter for default identity
		self::get_cfg(0);

		// load all other known identities
		$iid = self::get('cfg', 'iid');
		$sql = 'SELECT `identity_id`'.
			   ' FROM '.$rc->db->table_name('identities', true).
			   ' WHERE `user_id` = ?';
		$q   = $rc->db->query($sql, $rc->user->ID);
		while ($r = $rc->db->fetch_assoc($q))
			// skip default identity
			if ((int)$r['identity_id'] != $iid)
				self::get_cfg((int)$r['identity_id']);
	}

	/**
	 * 	Get configuration for identity
	 *
	 * 	@param int $iid
	 */
	protected function get_cfg(int $iid): void {

		$rc = rcmail::get_instance();

		// load default configuration?
		if (!$iid)
		{
			$r   = $rc->user->get_identity();
			$iid = (int)$r['identity_id'];
			self::set('cfg', 'iid', $iid);											// save active identity id
			self::set('cfg', 'default', $iid);										// save default identity
			self::set('cfg', 'language', $rc->config->get('language'));				// save language

			// set the proper default user data
			self::set($iid, 'isw_label', $r['name']);
			self::set($iid, 'isw_imap_user', $_SESSION['username']);
			self::set($iid, 'isw_imap_pass', $_SESSION['password']);
			self::set($iid, 'isw_imap_host', $_SESSION['storage_host']);
			self::set($iid, 'isw_imap_port', $_SESSION['storage_port']);
			self::set($iid, 'isw_imap_ssl', $_SESSION['storage_ssl']);

			// load preferences
			self::load_cfg((int)$iid, $r['email']);

		} else
		{
			$sql = 'SELECT `email` FROM '.
				   $rc->db->table_name('identities', true).
				   ' WHERE `identity_id` = ?';
			$r 	 = $rc->db->query($sql, $iid);
			$r 	 = $rc->db->fetch_assoc($r);

			// load RoudCube user preferences
			self::load_cfg($iid, $r['email']);
		}

		self::set($iid, '_unseen', 			0);										// # of unseen messages
		self::set($iid, '_checked_last',	0);										// last time checked
		self::set($iid, '_notify',			false);									// notify user flag
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
		self::write_log(__FILE__, __LINE__, 'Applying predefined configuration for '.$iid.' - '.$txt.' match.', true);

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
				$v = $rc->config->get(substr($k, 4));

			// check for special configuration paraneters
			if ($k == 'smtp_pass' && $v == '%p')
				$v = $rc->decrypt(self::get($iid, 'isw_imap_pass'));
			if ($k == 'smtp_user' && $v == '%u')
				$v = self::get($iid, 'isw_imap_user');

			// encrypt passwords
			if (strpos($k, 'pass'))
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
				self::set($iid, $k.'ssl', $t[1]);
				self::set($iid, $k.'port', $t[2]);
			} else
				self::set($iid, $k, $v);
		}

		// any configuration saved?
		$sql = ' SELECT `identity_switch_prefs`'.
			   ' FROM '.$rc->db->table_name('identities', true).
			   ' WHERE `identity_id` = ?';
		$q   = $rc->db->query($sql, $iid);
		if (($r = $rc->db->fetch_assoc($q)) && isset($r['identity_switch_prefs']))
		{
			foreach ((array)json_decode($r['identity_switch_prefs']) as $k => $v)
				self::set($iid, $k, is_object($v) ? (array)$v : $v);

			self::write_log(__FILE__, __LINE__, 'Updating configuration for '.$iid.' with saved data.', true);
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
			   ' WHERE `identity_id` = ?';

		$prefs = self::get($iid);
		foreach ($prefs as $k => $v)
			// delete internal temporary parameters
			if (substr($k, 0, 1) == '_')
				unset($prefs[$k]);
		$v;
		$q   = $rc->db->query($sql, json_encode($prefs), $iid);
		if ($rc->db->affected_rows($q) === false)
			$this->write_log(__FILE__, __LINE__, 'Error saving data for "'.$iid.'"');
	}

	/**
	 * 	Get variable
	 *
	 * 	@param  string|int $sect
	 * 	@param  string|int $var
	 * 	@return string|int|bool|array
	 */
	protected function get(string|int|null $sect = null, string|int|null $var = null): mixed
	{
		// get whole table?
		if (!$sect && !$var)
		{
			if (!isset($_SESSION[self::TABLE]))
				$_SESSION[self::TABLE] = [];
			return $_SESSION[self::TABLE];
		}

		// get variable?
		if ($sect && $var && isset($_SESSION[self::TABLE][$sect][$var]))
			return $_SESSION[self::TABLE][$sect][$var];
		// get section?
		elseif (isset($_SESSION[self::TABLE][$sect]))
			return $_SESSION[self::TABLE][$sect];

		// we should never go here
	    self::write_log(__FILE__, __LINE__, 'Variable "'.$var.'" not available!');
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
	protected function set(string|int $sect, array|string|int $var, mixed $val = null, mixed $default = null): void
	{

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

		if ($sect)
			unset($_SESSION[self::TABLE][$sect]);
		else
			unset($_SESSION[self::TABLE][$var]);
	}

	/**
	 * 	Write log message
	 *
	 * 	@param string $txt 		Log message
	 * 	@param string $file		File name
	 * 	@param string $line		Line number
	 * 	@param bool   $debug 	TRUE=Is debug message; FALSE=regular message (default)
	 */
	static public function write_log(string $file, int $line, string $txt, bool $debug = false): void
	{
		if (!isset($_SESSION[self::TABLE]['cfg']))
			return;

		if (!$debug && $_SESSION[self::TABLE]['cfg']['logging'] > 0)
			rcmail::get_instance()->write_log('identity_switch', basename($file).'('.$line.'): '.$txt);

		if ($debug && $_SESSION[self::TABLE]['cfg']['logging'] == 2)
			rcmail::get_instance()->write_log('identity_switch', basename($file).'('.$line.'): Debug: '.$txt);
	}

}

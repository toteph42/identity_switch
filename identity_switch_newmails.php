<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

// include environment
if (!defined('INSTALL_PATH'))
	define('INSTALL_PATH', gethostname() == 'Jolly' ? 'D:/www/syncgw/': realpath(__DIR__.'/../../').'/');
##	define('INSTALL_PATH', realpath(__DIR__.'/../../').'/');

require_once \INSTALL_PATH.'program/include/iniset.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_rpc.php';
require_once \INSTALL_PATH.'plugins/identity_switch/identity_switch_cfg.php';

class identity_switch_newmails extends identity_switch_rpc {

	private $file;
	private $cache;
	private $fp;

    /**
     * 	Run the controller.
     */
	public function __construct()
	{
		$rc = rcmail::get_instance();

		if (is_null($iid = rcube_utils::get_input_value('iid', rcube_utils::INPUT_GET)))
			return;

		if (is_null($cache = rcube_utils::get_input_value('cache', rcube_utils::INPUT_GET)))
		{
			$_SESSION[identity_switch_cfg::TABLE]['cfg'] = [ 'logging' => '1' ];
			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Missing parameter "cache" - stop checking');
			return;
		}

		// get cached data
		if (!file_exists($cache))
		{
			$_SESSION[identity_switch_cfg::TABLE]['cfg'] = [ 'logging' => '1' ];
			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Cache file "'.$cache.'" does not exists - stop checking');
			return;
		} else
			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Cache file loaded', true);
		$this->cache = unserialize(file_get_contents($cache));

		$_SESSION[identity_switch_cfg::TABLE] = $this->cache;

		// storage initialization hook
		$rc->plugins->register_hook('storage_init', [ $this, 'set_language' ]);

		if (!$iid)
		{
			$res = [];
			foreach ($this->cache as $iid => $rec)
			{
				// skip configuration array and identities without label
				if (!is_numeric($iid) || !$rec['isw_label'])
					continue;

				// get our host name
				if (strpos($_SERVER['HTTP_HOST'], ':'))
					$c = explode(':', $_SERVER['HTTP_HOST']);
				else
					$c = [ $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'] ];
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
					$host = 'ssl://';
				$host .= $c[0].':'.$c[1];

				$res[$iid] = new identity_switch_rpc();
				if (!$res[$iid]->open($host))
				{
					self::write_data($iid.'##'.$res[$iid]);
					identity_switch_cfg::write_log(__FILE__, __LINE__, 'Cannot open host "'.$host.'" - stop checking', true);
					return;
				}

				// prepare request (no fopen() usage because "allow_url_fopen=FALSE" may be set in PHP.INI)
				$req = '/plugins/identity_switch/identity_switch_newmails.php?iid='.$iid.
					   '&cache='.urlencode($cache);
				if (!$res[$iid]->write($req))
				{
					if (is_resource($res[$iid]))
						fclose($res[$iid]);
					self::write_data('0##Identity: '.$iid.' Cannot write to "'.$host.'" Request: "'.$req.'" - stop checking');
					return;
				}

				// delay execution?
				if (count($this->cache) > 1 && isset($this->cache['cfg']['delay']) && $this->cache['cfg']['delay'] > 0)
				{
					if ($this->cache['cfg']['delay'] > 1000000)
					{
						identity_switch_cfg::write_log(__FILE__, __LINE__, 'Delay execution by "'.
														$this->cache['cfg']['delay'].'" seconds', true);
						sleep ($this->cache['cfg']['delay'] / 1000000);
					}
					else
					{
						identity_switch_cfg::write_log(__FILE__, __LINE__, 'Delay execution by "'.
														$this->cache['cfg']['delay'].'" microseconds', true);
						usleep ($this->cache['cfg']['delay']);
					}
				}
			}

			// collect data
			$cnt = 0;
			while (count($res) && $cnt++ < $this->cache['cfg']['retries'])
			{
				foreach ($res as $iid => $obj)
				{
					if ($wrk = $res[$iid]->read())
						self::write_data('0##'.$wrk);
					unset($res[$iid]);
					$cnt  = 0;
				}
				$obj; // Disable Eclipse warning
			}
			if ($cnt >= $this->cache['cfg']['retries'])
				self::write_data('0##Number of retries exceeded for identity '.$iid.' - stop checking');

			// delete cache data
			@unlink($cache);
			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Cache file "'.$cache.'" deleted', true);
		} else {

			$this->cache['cfg']['iid'] = $iid;
			$rec = $this->cache[$iid];

			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Start checking "'.$iid.' "'.
											json_encode($rec).'"', true);

	   		// must delete storage object, to get SSL status reset
			$rc->storage = null;

			// connect
			$storage = $rc->get_storage();

			if (!$storage->connect($rec['isw_imap_host'], $rec['isw_imap_user'],
			  					   $rc->decrypt($rec['isw_imap_pass']), $rec['isw_imap_port'], $rec['isw_imap_ssl']))
			{
				self::write_data('0##Identity '.$iid.': Cannot connect to "'.
								 $rec['imap_ssl'].'://'.$rec['isw_imap_host'].':'.$rec['isw_imap_port'].
								 '" for user "'.$rec['isw_imap_user'].'" - stop checking');
				return;
			}

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

	       	self::write_data($iid.'##'.$unseen);
			identity_switch_cfg::write_log(__FILE__, __LINE__, 'Setting unseen count to '.$unseen.' for identity id '.$iid, true);
		}
    }

    /**
     * 	Set language for IMAP connection
     *
     * 	@param array $args
     * 	@return array
     */
    function set_language (array $args): array
    {
    	$args['language'] = $this->cache['cfg']['language'];

    	return $args;
    }

    /**
     * 	Write record to data file
     *
     * 	@param string $msg
     * 	@return bool
     */
    private function write_data (string $msg): bool
    {
    	if (!$this->fp || fwrite($this->fp, $msg) === false)
    	{
    		if (is_resource($this->fp))
    			fclose($this->fp);

			// open output file
			if (!($this->fp = @fopen($this->cache['cfg']['import'], 'a')))
			{
				identity_switch_cfg::write_log(__FILE__, __LINE__, 'Error opening import file "'.
												 $this->cache['cfg']['import'].'"');
				return false;
			}
			return fwrite($this->fp, time().'##'.$msg.'###') !== false ? true : false;
    	}

    	return true;
    }

}
// run task
new identity_switch_newmails();

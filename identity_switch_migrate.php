<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

class identity_switch_migrate extends identity_switch_cfg
{

	public function __construct()
	{
		$rc = rcmail::get_instance();

		// Already migrated?
		$sql = 'SELECT * FROM '.$rc->db->table_name('identities', true).
			   ' WHERE `identity_switch_prefs` IS NOT NULL LIMIT 1';
		$q = $rc->db->query($sql);
		if ($rc->db->fetch_assoc($q))
			return;

		$sql = 'SELECT * FROM '.$rc->db->table_name('identity_switch', true);
		$q = $rc->db->query($sql);
		while ($r = $rc->db->fetch_assoc($q))
		{
			if (!$r['flags'])
				continue;

			$imap_ssl = '';
			if ($r['flags'] & 0x0004)
				$imap_ssl = 'ssl';
			if ($r['flags'] & 0x0008)
				$imap_ssl = 'tsl';

			$smtp_ssl = '';
			if ($r['flags'] & 0x0010)
				$smtp_ssl = 'ssl';
			if ($r['flags'] & 0x0020)
				$smtp_ssl = 'tsl';

			foreach ([ 'isw_label'					=> $r['label'],
					   'isw_notification_basic' 	=> ($r['flags'] & 0x0100 ? true : false),
					   'isw_notification_desktop' 	=> ($r['flags'] & 0x0200 ? true : false),
					   'isw_notification_timeout' 	=> $r['notify_timeout'],
					   'isw_notification_sound' 	=> ($r['flags'] & 0x0400 ? true : false),
					   'isw_refresh_interval' 		=> $r['newmail_check'],
					   'isw_check_all_folders' 		=> ($r['flags'] & 0x0800 ? true : false),
					   'isw_imap_delimiter'			=> $r['imap_delim'],
					   'isw_imap_host'				=> $r['imap_host'],
					   'isw_imap_port'				=> $r['imap_port'],
					   'isw_imap_ssl'				=> $imap_ssl,
					   'isw_imap_user'				=> $r['imap_user'],
					   'isw_imap_pass'				=> $r['imap_pwd'],
					   'isw_smtp_host'				=> $r['smtp_host'],
					   'isw_smtp_port'				=> $r['smtp_port'],
					   'isw_smtp_ssl'				=> $smtp_ssl,
					   'isw_smtp_user'				=> $r['smtp_user'],
					   'isw_smtp_pass'				=> $r['smtp_pwd'],
					 ] as $k => $v)
				identity_switch_cfg::set((int)$r['iid'], $k, $v);

			identity_switch_cfg::save_cfg((int)$r['iid']);

			if ($r['user_id'] != $rc->user->ID)
				identity_switch_cfg::del($r['iid']);
		}

		// delete table
		$rc->db->query('DROP TABLE '.$rc->db->table_name('identity_switch'));
    }

}

<?php
declare(strict_types=1);

/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

class identity_switch_prefs extends rcube_plugin
{

	/*
	 * 	Test cases for input parser
	 *

	 	Xbool = true;
	 	Xnull = null;
	 	Xpass1 = '%p';
	 	Xpassword = '123456';
	  	Xstring = 'Zeichen';

	 	-- simple empty array 1. dimension
	  	array1[1] = 'String';
	 	-- missing closing bracket and EOL
	 	array2 = [
	 	-- empty array
	 	array2 = [];
	 	-- a bit more complex
	 	array3[1] = [];
	 	array4[1] = null;
	 	array5[1] = false;
	 	-- complex 2. dimensional array
	 	array6[1][2] = 'String';
	 	array7[1][2] = true;
	 	-- complex 4. dimensional array
	 	array8[1][2][3][4] = null;

	 */

	/**
	 * 	Initialize Plugin
	 *
	 * 	{@inheritDoc}
	 * 	@see rcube_plugin::init()
	 */
	function init(): void
	{
		// preference hooks and actions
		$this->add_hook('identity_form', 				  [ $this, 'show_isw_prefs'	]);
		$this->add_hook('identity_update', 				  [ $this, 'save_isw_prefs'	]);
		$this->add_hook('identity_delete', 				  [ $this, 'del_isw_prefs'	]);
		$this->add_hook('identity_create_after',		  [ $this, 'save_isw_prefs' ]);
		$this->add_hook('preferences_list', 			  [ $this, 'show_rc_prefs'	]);
		$this->add_hook('preferences_save', 			  [ $this, 'save_isw_prefs' ]);
	}

	/**
	 * 	Show identity switch preferences
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function show_isw_prefs(array $args): array
	{
		$this->add_texts('localization');

		if (isset($args['record']['identity_id']))
		{
			$hidden = 0;
			$iid = (int)$args['record']['identity_id'];
	       	if (!is_array($rec = identity_switch_cfg::get($iid, null, false)))
	       	{
	       		$val = '';
	       		$rec = identity_switch_cfg::get_cfg($iid, $args['record']['email']);
	       	} else
	       		$val = $rec['isw_label'];
		} else
		{
			$hidden = 1;
        	$val = $this->gettext('idsw.common.noedit');
		}

		$fld = rcube_output::get_edit_field('isw_label', $val, [
            						'label' 	=> rcube::Q($this->gettext('isw.common.label')),
			              			'type'  	=> 'text',
							        'maxlength' => 32,
							        'size'	  	=> 40,
            						'value'		=> $val,
									'disabled'	=> $hidden,
        ], 'text');

		// insert identity label
       	$pos = array_search('email', array_keys($args['form']['addressing']['content']));
        $args['form']['addressing']['content'] =
        	array_slice($args['form']['addressing']['content'], 0, $pos, true) +
            [ 'isw_label' => [
            					'label' 	=> rcube::Q($this->gettext('isw.common.label')),
            					'value'		=> $fld,
            				 ]
            ] +
            array_slice($args['form']['addressing']['content'], $pos, null, true);

	    if ($hidden)
	    	return $args;

        // configuration string
		$cfg = '';

	    switch ($typ = identity_switch_cfg::get('cfg', 'typ')) {
		case 1: 												// simple view
			$authImap = new html_select([ 'name' => "_isw_imap_ssl" ]);
			$authImap->add($this->gettext('idsw.imap.ssl.none'), 'none');
			$authImap->add($this->gettext('idsw.imap.ssl.ssl'), 'ssl');
			$authImap->add($this->gettext('idsw.imap.ssl.tls'), 'tls');

			$args['form']['imap'] = [
				'name' 	  => $this->gettext('idsw.imap.caption'),
				'content' => [
					'isw_imap_user' 		=> [ 'label' => $this->gettext('idsw.imap.user'),
											   'type' => 'text', 'maxlength' => 64 ],
					'isw_imap_pass' 		=> [ 'label' => $this->gettext('idsw.imap.pass'),
											   'type' => 'password', 'maxlength' => 128 ],
					'isw_imap_host'	 		=> [ 'label' => $this->gettext('idsw.imap.host'),
											   'type'  => 'text', 'maxlength' => 64 ],
					'isw_imap_ssl' 			=> [ 'label' => $this->gettext('idsw.imap.ssl'),
											   'value' => $authImap->show($rec['isw_imap_ssl']) ],
					'isw_imap_port' 		=> [ 'label' => $this->gettext('idsw.imap.port'),
										  	   'type' => 'text', 'maxlength' => 5 ],
					'isw_imap_delimiter'	=> [ 'label' => $this->gettext('idsw.imap.delimiter'),
									  		   'type' => 'text', 'maxlength' => 1 ],
				],
			];
			$args['record']['isw_imap_user'] 		= $rec['isw_imap_user'];
	        $args['record']['isw_imap_pass']  		= strlen($rec['isw_imap_pass']) ?
	        								  		  rcmail::get_instance()->decrypt($rec['isw_imap_pass']) : '';
			$args['record']['isw_imap_host'] 		= $rec['isw_imap_host'];
	        $args['record']['isw_imap_port'] 		= $rec['isw_imap_port'];
	        $args['record']['isw_imap_delimiter'] 	= $rec['isw_imap_delimiter'];

			$authSmtp = new html_select([ 'name' => "_isw_smtp_ssl" ]);
			$authSmtp->add($this->gettext('idsw.smtp.ssl.none'), 'none');
			$authSmtp->add($this->gettext('idsw.smtp.ssl.ssl'), 'ssl');
			$authSmtp->add($this->gettext('idsw.smtp.ssl.tls'), 'tls');

			$args['form']['smtp'] = [
				'name' 	  => $this->gettext('idsw.smtp.caption'),
				'content' => [
					'isw_smtp_user' 	=> [ 'label' => $this->gettext('idsw.smtp.user'),
										   'type' => 'text', 'maxlength' => 64 ],
					'isw_smtp_pass' 	=> [ 'label' => $this->gettext('idsw.smtp.pass'),
										   'type' => 'password', 'maxlength' => 128 ],
		        	'isw_smtp_host'	 	=> [ 'label' => $this->gettext('idsw.smtp.host'),
										   'type' => 'text', 'maxlength' => 64 ],
					'isw_smtp_ssl' 		=> [ 'label' => $this->gettext('idsw.smtp.ssl'),
										   'value' => $authSmtp->show($rec['isw_smtp_ssl']) ],
		        	'isw_smtp_port' 	=> [ 'label' => $this->gettext('idsw.smtp.port'),
										   'type' => 'text', 'maxlength' => 5 ],
				],
			];
	       	$args['record']['isw_smtp_user'] = $rec['isw_smtp_user'];
	       	if ($rec['isw_smtp_pass'] == '%p')
		        $args['record']['isw_smtp_pass'] = $rec['isw_smtp_pass'];
	       	elseif (strlen($rec['isw_smtp_pass']))
	       		$args['record']['isw_smtp_pass'] = rcmail::get_instance()->decrypt($rec['isw_smtp_pass']);
			$args['record']['isw_smtp_host'] = $rec['isw_smtp_host'];
	        $args['record']['isw_smtp_port'] = $rec['isw_smtp_port'];

	        $args['isw_call'] = $rec;
	        $args = self::mailbox($args);

		case 2:													// complex view
			if (is_array($rec = identity_switch_cfg::get($iid)))
			    foreach ($rec as $k => $v)
	        		$cfg .= self::show_val($k, $v);
	        if ($typ == 1)
	        {
	        	$t = new html_inputfield([ 'name' => '_isw_prefs', 'type' => 'hidden', 'value' => $cfg ]);
	        	$args['form']['isw_prefs'] = [ 'name' => '', 'content' => $t->show() ];
	        } else
	        {
				$t = new html_textarea();
				$args['form']['isw_prefs'] = [
					'name'		=> rcube::Q($this->gettext('isw.used.prefs')),
					'type'		=> 'textarea',
					'content'	=> $t->show($cfg, [
								   'name' 		=> '_isw_prefs',
								   'rows' 		=> 10,
								   'style' 		=> 'border-style=inherit',
								   'disabled' 	=> '0',
								   'wrap' 		=> 'off',
					]),
				];
	        }
	        break;

		default:												// disabled
			$cfg = rcube::Q($this->gettext('isw.hide.prefs'));
			$t = new html_textarea();
			$args['form']['isw_prefs'] = [
				'name'		=> rcube::Q($this->gettext('isw.used.prefs')),
				'type'		=> 'textarea',
				'content'	=> $t->show($cfg, [
							   'name' 		=> '_isw_prefs',
							   'rows' 		=> 10,
							   'style' 		=> 'border-style=inherit',
							   'disabled' 	=> '1',
							   'wrap' 		=> 'off',
				]),
			];
			break;
		}

		return $args;
	}

	/**
	 * 	Show value
	 *
	 * 	@param string|array $key
	 * 	@param mixed $val
	 * 	@return string
	 */
	private function show_val(array|string $key, mixed $val): string {

		$str = '';

		// hide internal parameters
	    if (substr($key, 0, 1) == '_' || $key == 'isw_label' || substr($key, 0, 4) == 'cfg_')
	        return $str;

		if (is_array($val))
		{
			// empty array?
			if (!count($val))
				return $str.$key." = [];\n";;
			$nl = true;
        	foreach ($val as $k => $v)
	        {
	        	// convert stdClass to array (multidimensional array)
	        	if (is_object($v))
	        		$v = get_object_vars($v);
		        if (is_array($v))
        			return $str.self::show_val($key.'['.$k.']', $v);
	        	if ($nl)
		        {
		        	$nl = false;
		        	$str .= $key;
		        }
	        	$str .= self::show_val('['.$k.']', $v);;
	        	$nl = true;
    	    }
        	return $str;
		} else
			$str .= $key.' = ';

		if(is_bool($val))
	        $str .= ($val ? 'true' : 'false').";\n";
	    elseif (!is_object($val) && !strlen((string)$val))
	        $str .= "null;\n";
	    elseif (strpos($key, 'pass') !== false && $val != '%p')
	        $str .= "'●●●●●●●●';\n";
	    else
	       	$str .= "'".(is_object($val) ? get_class($val) : $val)."';\n";

	    return $str;
	}

	/**
	 * 	Show general preference
	 *
	 * 	@param 	array $args
	 * 	@return array
	 */
	function show_rc_prefs(array $args): array
	{
		$this->add_texts('localization');

		if ($args['section'] == 'general')
			return self::general($args);
		elseif ($args['section'] == 'mailbox')
			return self::mailbox($args);

		return $args;
	}

	/**
	 * 	Save identity switch preferences
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function save_isw_prefs(array $args): array
	{
		// saving identity data
		$iid = !isset($args['id']) ? (int)identity_switch_cfg::get('cfg', 'iid') : (int)$args['id'];
		if (!is_array($prefs = identity_switch_cfg::get($iid, null, false)))
			$prefs = identity_switch_cfg::get_cfg($iid, $args['record']['email']);
		$passw = [ $prefs['isw_imap_pass'], $prefs['isw_smtp_pass'] ];

		// get list of locked preferences
		$no_override = rcmail::get_instance()->config->get('dont_override');

		if (isset($args['prefs']))
		{
			switch ($args['section'])
			{
			case 'general':
				$k = 'isw_refresh_interval';
				if (!isset($no_override[$k]))
					$prefs[$k] = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST) * 60;
				break;

			case 'mailbox':
				foreach ([ 'isw_check_all_folders',
						   'isw_notification_basic',
						   'isw_notification_desktop',
						   'isw_notification_timeout',
						   'isw_notification_sound' ] as $k)
				{
					$v = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST);
					if (!isset($no_override[$k]))
						$prefs[$k] = is_null($v) ? false : true;
				}

			default:
				break;
			}
		} else
		{
			$prefs['isw_label'] = rcube_utils::get_input_value('_isw_label', rcube_utils::INPUT_POST);

			if ($v = rcube_utils::get_input_value('_isw_prefs', rcube_utils::INPUT_POST))
				foreach (explode("\n", $v) as $r)
					self::get_val($prefs, $r);

			// simple view?
			if (identity_switch_cfg::get('cfg', 'typ') == 1)
			{
				foreach ([ 'isw_imap_user',
						   'isw_imap_pass',
						   'isw_imap_host',
						   'isw_imap_ssl',
						   'isw_imap_port',
						   'isw_imap_delimiter',
						   'isw_smtp_user',
						   'isw_smtp_pass',
						   'isw_smtp_host',
						   'isw_smtp_ssl',
						   'isw_smtp_port',
						   'isw_notification_timeout' ] as $k)
				{
					$v = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST);
					if (strpos($k, 'pass') && $v != '%p')
						$prefs[$k] = rcmail::get_instance()->encrypt($v);
					else
						$prefs[$k] = $v;
				}

				foreach ([ 'isw_check_all_folders',
						   'isw_notification_basic',
						   'isw_notification_desktop',
						   'isw_notification_sound' ] as $k)
					$prefs[$k] = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST) == 1 ? true : false;
			} else
			{
				// special password check
				foreach ($prefs as $k => $v)
					if (strpos($k, 'pass') !== false)
					{
						// swap old password?
						if ($v == '●●●●●●●●')
							$prefs[$k] = $passw[$k == 'isw_imap_pass' ? 0 : 1];
					}
			}
		}

		// replace preferences
		identity_switch_cfg::del($iid);
		ksort($prefs);
		identity_switch_cfg::set($iid, $prefs);

		// save user preferences
		identity_switch_cfg::save_cfg($iid);

		return $args;
	}

	/**
	 * 	Delete identity
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	function del_isw_prefs(array $args): array
	{
		// get identity
		$iid = !isset($args['id']) ? (int)identity_switch_cfg::get('cfg', 'iid') : (int)$args['id'];

		// delete identity from cache
		identity_switch_cfg::del($iid);

		// set default identity
		identity_switch_cfg::set('cfg', 'iid', identity_switch_cfg::get('cfg', 'default_iid'));

		// rebuild dropdown
		identity_switch::$_menu = false;

		return $args;
	}

	/**
	 * 	Get value
	 *
	 * 	@param array $prefs
	 * 	@param string $row
	 */
	private function get_val(array &$prefs, string $row): void
	{
		// replace tabs
		$row = str_replace([ "\t", "\r", "\n" ], [ ' ', ' ', ' ' ], $row);

		// skip empty lines
		if (trim($row, " ;") == '')
			return;

		// check for array
		if ($p = strpos($row, '['))
		{
			// extract master key
			$k    = trim(substr($row, 0, $p), '= ');
			$pref = &$prefs[$k];
			$row  = trim(substr($row, $p));

			// extract sub keys
			while (strpos($row, '[') !== false)
			{
				$row = substr($row, 1);
				// missing ']'
				if (($p = strpos($row, ']')) === false)
					$p = 0;
				if (($k = substr($row, 0, $p)) && $k != '[')
					$pref = &$pref[$k];
				$row  = trim(substr($row, $p + 1), ' =');
			}

			if ($row == ';')
				$pref = [];
			else
				$pref = self::chk_type(trim($row, " =;'"));

			return;
		}

		// get key and value
		list($k, $v) = explode(' =', $row);
		$k = trim($k, " \t");
		$v = rtrim($v, " ;\n\r");
		$v = self::chk_type(trim($v, " '"));

		// special password check
		if (strpos($k, 'pass') !== false)
			if ($v != '%p' && $v != '●●●●●●●●')
				$v = rcmail::get_instance()->encrypt($v);

		$prefs[$k] = $v;

		return;
	}

	/**
	 * 	Validate variable type
	 *
	 * 	@param string $val
	 * 	@return mixed
	 */
	private function chk_type(string $val): mixed {

		switch ($val)
		{
		case 'null':
			$val = null;
			break;

		case 'true':
			$val = true;
			break;

		case 'false':
			$val = false;

		default:
			break;
		}

		return $val;
	}

	/**
	 * 	Show general preferences
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	private function general(array $args): array
	{
		$rc  = rcmail::get_instance();
		$iid = identity_switch_cfg::get('cfg', 'iid');
		$rec = identity_switch_cfg::get($iid);

		$set  = &$args['blocks']['main']['options'];
		$mini = $rc->config->get('min_refresh_interval');

		// get list of locked preferences
		$no_override = $rc->config->get('dont_override');

		// delete std. entry
		unset($set['refresh_interval']);

		// refresh interval
        $field_id = 'isw_refresh_interval';
		if (!isset($no_override[$field_id]) && isset($field_id))
        {
			$sel = new html_select([ 'name' => '_'.$field_id, 'id' => $field_id, 'class' => 'custom-select']);

            $sel->add($rc->gettext('never'), 0);
			foreach ([ 1, 3, 5, 10, 15, 30, 60 ] as $min)
			{
				if (!$mini || $mini <= $min * 60)
				{
                	$label = $rc->gettext(['name' => 'everynminutes', 'vars' => ['n' => $min]]);
					$sel->add($label, $min);
				}
			}

			$set[$field_id] = [
						'title'   => html::label($field_id,
									 rcube::Q('[ '.$this->gettext('isw.identity').': '.$rec['isw_label'].' ] '.
								 	 $rc->gettext('refreshinterval'))),
                        'content' => $sel->show($rec[$field_id] / 60),
			];
		}

		return $args;
	}

	/**
	 * 	Show mailbox preferences
	 *
	 * 	@param array $args
	 * 	@return array
	 */
	private function mailbox(array $args): array
	{
		$rc  = rcmail::get_instance();

		// get list of locked preferences
		$no_override = $rc->config->get('dont_override');

		// change working set
		if (isset($args['isw_call']))
		{
			$rec = $args['isw_call'];

			$args['form']['notify'] = [
				'name' 	  => $this->gettext('isw.notify.caption'),
				'content' => [],
			];
			$set = &$args['form']['notify']['content'];
			$tit = 'label';
			$val = 'value';

			$field_id = 'isw_check_all_folders';
			if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		    {
				$sel = new html_checkbox(['name' => '_'.$field_id, 'id' => $field_id, 'value' => 1]);

		        $set[$field_id] = [
		                       	$tit   	=> rcube::Q($rc->gettext('checkallfolders')),
		                        $val	=> $sel->show($rec[$field_id]),
		        ];
			}
		} else
		{
			$iid = identity_switch_cfg::get('cfg', 'iid');
			$rec = identity_switch_cfg::get($iid);

			$set = &$args['blocks']['new_message'];
			$set['name'] = rcube::Q('[ '.$this->gettext('isw.identity').': '.$rec['isw_label'].' ] ').$set['name'];

			$set = &$args['blocks']['new_message']['options'];
			$tit = 'title';
			$val = 'content';

			unset($set['check_all_folders']);
			$field_id = 'isw_check_all_folders';
			if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		    {
				$sel = new html_checkbox(['name' => '_'.$field_id, 'id' => $field_id, 'value' => 1]);

		        $set[$field_id] = [
		                       	$tit   	=> html::label($field_id, rcube::Q($rc->gettext('checkallfolders'))),
		                        $val	=> $sel->show($rec[$field_id]),
		        ];
			}
		}

		// our own preferences
		$field_id = 'isw_notification_basic';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ $tit  => $this->gettext('isw.notify.basic'),
								  $val	=> $cb->show($rec[$field_id]).
								  		 	html::a([ 'href' => '#',
											'onclick' => 'identity_switch_notify_basic(); return false',
											'name' => '_notify_basic_test' ],
			                	       	 	$this->gettext('isw.notify.test')) ];
		}

		$field_id = 'isw_notification_desktop';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ $tit	=> $this->gettext('isw.notify.desktop'),
								  $val 	=> $cb->show($rec[$field_id]).
										   	html::a(['href' => '#',
											'onclick' => 'identity_switch_notify_desktop(\''.
											rawurlencode($this->gettext('isw.notify.title')).'\',\''.
											rawurlencode(sprintf($this->gettext('isw.notify.msg'), 1,
										    $rec['isw_label'])).
											'\',\''.$rec['isw_notification_timeout'].'\',\''.
											rawurlencode($this->gettext('isw.notify.err.notification')).
											'\'); return false',
											'name' => '_notify_desktop_test' ],
										 	$this->gettext('isw.notify.test')) ];

			$to = new html_select([ 'name' => '_isw_notification_timeout' ]);
        	foreach ([ 5, 10, 15, 30, 45, 60 ] as $sec)
            		$to->add($this->gettext(['name' => 'afternseconds', 'vars' => [ 'n' => $sec ]]), $sec);
			$set['isw_notification_timeout'] = [ $tit	=> $this->gettext('isw.notify.timeout'),
												 $val	=> $to->show((int)$rec['isw_notification_timeout']) ];
		}

		$field_id = 'isw_notification_sound';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ $tit	=> $this->gettext('isw.notify.sound'),
								  $val 	=> $cb->show($rec[$field_id]).
											html::a(['href' => '#',
											'onclick' => 'identity_switch_notify_sound(\''.
											rawurlencode($this->gettext('isw.notify.err.autoplay')).
											'\'); return false',
											'name' => '_notify_sound_test' ],
											$this->gettext('isw.notify.test')) ];
		}

		return $args;
	}

}

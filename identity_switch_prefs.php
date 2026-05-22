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

        $iid = (int)$args['record']['identity_id'];
		$val = identity_switch_cfg::get($iid, 'isw_label');
		$fld = rcube_output::get_edit_field('isw_label', $val, [
            						'label' 	=> rcube::Q($this->gettext('isw.common.label')),
			              			'type'  	=> 'text',
							        'maxlength' => 32,
							        'size'	  	=> 40,
            						'value'		=> $val,
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

        // get configuration?
		$cfg  = '';
		$hide = identity_switch_cfg::get('cfg', 'hide_prefs');

		if ($hide)
			$cfg = rcube::Q($this->gettext('isw.hide.prefs'));
		else
	        foreach (identity_switch_cfg::get($iid) as $k => $v)
	        	$cfg .= self::show_val($k, $v);

		$t = new html_textarea();
		$args['form']['isw_prefs'] = [
			'name'		=> rcube::Q($this->gettext('isw.used.prefs')),
			'type'		=> 'textarea',
			'content'	=> $t->show($cfg, [
						   'name' 		=> '_isw_prefs',
						   'rows' 		=> 10,
						   'style' 		=> 'border-style=inherit',
						   'disabled' 	=> $hide ? '1' : '0',
						   'wrap' 		=> 'off',
			]),
		];

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

		// do not show internal parameters
	    if (substr($key, 0, 1) == '_' || $key == 'isw_label')
	        return $str;

		if (is_array($val))
		{
			// empty array?
			if (!count($val))
				return $str.$key." = [];\n";;
			$nl = true;
        	foreach ($val as $k => $v)
	        {
		        if (is_array($v))
        			return $str.self::show_val($key.'['.$k.']', $v);
	        	if ($nl)
		        {
		        	$nl = false;
		        	$str .= $key."[";
		        }
	        	$str .= $k."] = '".$v."';\n";
	        	$nl = true;
    	    }
        	return $str;
		} else
			$str .= $key.' = ';

		if(is_bool($val))
	        $str .= ($val ? 'true' : 'false').";\n";
	    elseif (!strlen((string)$val))
	        $str .= "null;\n";
	    elseif (strpos($key, 'pass') && $key != '%p')
	        $str .= "'●●●●●●●●';\n";
	    else
	       	$str .= "'".$val."';\n";

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
		$iid   = !isset($args['id']) ? (int)identity_switch_cfg::get('cfg', 'iid') : (int)$args['id'];
		$prefs = identity_switch_cfg::get($iid);

		if (isset($args['prefs']))
		{
			switch ($args['section'])
			{
			case 'general':
				$k = 'isw_refresh_interval';
				$prefs[$k] = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST);
				break;

			case 'mailbox':
				foreach ([ 'isw_check_all_folders',
						   'isw_notification_basic',
						   'isw_notification_desktop',
						   'isw_notification_timeout',
						   'isw_notification_sound' ] as $k)
				{
					$v = rcube_utils::get_input_value('_'.$k, rcube_utils::INPUT_POST);
					$prefs[$k] = is_null($v) ? false : true;
				}

			default:
				break;
			}
		} else
		{

			if ($v = rcube_utils::get_input_value('_isw_label', rcube_utils::INPUT_POST))
				$prefs['isw_label'] = $v;

			if ($v = rcube_utils::get_input_value('_isw_prefs', rcube_utils::INPUT_POST))
				foreach (explode("\n", $v) as $r)
					self::get_val($prefs, $r);
		}

		// replace preferences
		identity_switch_cfg::del($iid);
		identity_switch_cfg::set($iid, $prefs);

		// save user preferences
		identity_switch_cfg::save_cfg($iid);

		return $args;
	}

	/**
	 * 	Get value
	 *
	 * 	@param array $prefs
	 * 	@param string $row
	 * 	@return null
	 */
	private function get_val(array &$prefs, string $row): null
	{
		// replace tabs
		$row = str_replace([ "\t", "\r", "\n" ], [ ' ', ' ', ' ' ], $row);

		// skip empty lines
		if (trim($row, " ;") == '')
			return null;

		// check for array
		// -- simple array 1. dimension
		// imap_message_flags[JUNK] = 'Junk';
		// -- complex array 2. dimension
		// smtp_conn_options[ssl][verify_peer] = '1';
		// -- complex array 3. dimension
		// test[1][2][3][4] = 'val';
		if ($p = strpos($row, '['))
		{
			// extract master key

			// [imap_message_flags]
			// [smtp_conn_options]
			// [test]

			$k    = substr($row, 0, $p);
			$pref = &$prefs[$k];
			$row  = substr($row, $p);

			// empty array?
			// = [];
			if (strpos($row, '[]') !== false)
			{
				$prefs[$k] = [];
				return null;
			}

			// extract sub keys

			// [imap_message_flags][JUNK]
			// [smtp_conn_options][ssl][verify_peer]
			// [test][1][2][3][4]

			while (strpos($row, '[') !== false)
			{
				$row = substr($row, 1);
				if (($p = strpos($row, ']')) === false)
					; // missing ']'
				$k    = substr($row, 0, $p);
				$pref = &$pref[$k];
				$row  = substr($row, $p + 1);
			}

			// = 'Junk';
			// = '1';
			// = 'val';
			$pref = trim($row, " =;'");

			return null;
		}

		// get key and value
		list($k, $v) = explode(' =', $row);
		$k = trim($k, " \t");
		$v = rtrim($v, " ;\n\r");
		$prefs[$k] = self::val_val($k, trim($v, " '"));

		return null;
	}

	/**
	 * 	Validate key pair
	 *
	 * 	@param string $key
	 * 	@param string $val
	 * 	@return mixed
	 */
	private function val_val(string $key, string $val): mixed {

		$rc = rcmail::get_instance();

		if (strpos($key, 'pass'))
			$val = $rc->encrypt($val);

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

		// get list of locked parameters
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
		$iid = identity_switch_cfg::get('cfg', 'iid');
		$rec = identity_switch_cfg::get($iid);

		// get helper functions
		$no_override = $rc->config->get('dont_override');

		$set = &$args['blocks']['new_message'];
		$set['name'] = rcube::Q('[ '.$this->gettext('isw.identity').': '.$rec['isw_label'].' ] ').$set['name'];

		// change working set
		$set = &$args['blocks']['new_message']['options'];

		$field_id = 'isw_check_all_folders';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
       	{
			$sel = new html_checkbox(['name' => '_'.$field_id, 'id' => $field_id, 'value' => 1]);

            $set[$field_id] = [
                        'title'   => html::label($field_id, rcube::Q($rc->gettext('checkallfolders'))),
                        'content' => $sel->show($rec[$field_id]),
        	];
		}

		// our own preferences
		$field_id = 'isw_notification_basic';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ 'title'  	=> $this->gettext('isw.notify.basic'),
								  'content'	=> $cb->show($rec[$field_id]).
								  		   		html::a([ 'href' => '#',
											   'onclick' => 'identity_switch_basic(); return false',
											   'name' => '_notify_basic_test' ],
			                	       	 	   $this->gettext('isw.notify.test')) ];
		}

		$field_id = 'isw_notification_desktop';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ 'title'	=> $this->gettext('isw.notify.desktop'),
								  'content'	=> $cb->show($rec[$field_id]).
										   		html::a(['href' => '#',
											   'onclick' => 'identity_switch_desktop(\''.
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
			$set['isw_notification_timeout'] = [ 'title' 	=> $this->gettext('isw.notify.timeout'),
												 'content'	=> $to->show((int)$rec['isw_notification_timeout']) ];
		}

		$field_id = 'isw_notification_sound';
		if (!isset($no_override[$field_id]) && isset($rec[$field_id]))
		{
			$cb = new html_checkbox([ 'name' => '_'.$field_id, 'id' => $field_id, 'value' => '1' ]);

			$set[$field_id]   = [ 'title' 	=> $this->gettext('isw.notify.sound'),
								  'content' => $cb->show($rec[$field_id]).
												  html::a(['href' => '#',
												  'onclick' => 'identity_switch_sound(\''.
												  rawurlencode($this->gettext('isw.notify.err.autoplay')).
												  '\'); return false',
												  'name' => '_notify_sound_test' ],
												  $this->gettext('isw.notify.test')) ];
		}

		return $args;
	}

}

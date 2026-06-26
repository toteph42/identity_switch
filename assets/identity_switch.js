/*
 * 	Identity switch RoundCube Bundle
 *
 *	@copyright	(c) 2024 - 2026 Florian Daeumling, Germany. All right reserved
 * 	@license 	https://github.com/toteph42/identity_switch/blob/master/LICENSE
 */

// plugin initialization
function identity_switch_init() 
{
	// add command listerner for new mail check
	rcmail.addEventListener('plugin.identity_switch_newmail', identity_switch_newmail);
	
	// add command listener for notification
	rcmail.addEventListener('plugin.identity_switch_notify', identity_switch_notify)

	// bind to messages list select event, so favicon will be reverted on message preview too
 	rcmail.addEventListener('init', function() {
         if (rcmail.message_list)
             rcmail.message_list.addEventListener('select', identity_switch_stop_notify);
 	});
}

// check for new mails
function identity_switch_newmail(ctl) 
{
	for (var i = 0; i < ctl.length; i++) 
	{
		// call new mail check asynchronously (after 20 ms)
		if (!ctl[0].init)
			setTimeout(identity_switch_check_newmail, 20, ctl[i].iid);

		// setup timer
		if (ctl[0].logging == '2')
		{
			var d = new Date();
			console.log(d.toLocaleString('de-DE') + ' ' + ctl[i].iid + ': Start ckecking in ' + ctl[i].wait + ' seconds'); 
		}
		setInterval(identity_switch_check_newmail, ctl[i].wait * 1000, ctl[i].iid);
	}
}

// execute PHP call
function identity_switch_check_newmail(iid) 
{
	rcmail.http_post('plugin.identity_switch_newmail', { 'identity_switch_iid' : iid } );
}

// switch identity
function identity_switch_run(iid) 
{
    rcmail.env.unread_counts = {};
	rcmail.http_post('plugin.identity_switch_run', { 'identity_switch_iid': iid });
}

// ------------ notification handling -------------------------------

// perform updates on unseen counter (and notify)
function identity_switch_notify(ctl) 
{
	var autoplay = decodeURI(ctl[0].autoplay);
	var notification = decodeURI(ctl[0].notification);
	var title = decodeURI(ctl[0].title);

	for (var i = 1; i < ctl.length; i++) 
	{
		var e = $('#identity_switch_unseen_' + ctl[i].iid);
		
		if (ctl[i].unseen == '0')
			e.text('');
		else
			e.text(ctl[i].unseen);

		if (ctl[0].logging == '2')
		{
			var d = new Date();
			console.log(d.toLocaleString('de-DE') + ' ' + ctl[i].iid + ': Changing unseen from ' + 
						ctl[i].old + ' to ' + ctl[i].unseen);
		}
		
		// check for notification
		if (ctl[i].basic !== undefined)
			identity_switch_notify_basic();
		if (ctl[i].desktop !== undefined) 
			identity_switch_notify_desktop(title, ctl[i].desktop.text, ctl[i].desktop.timeout, notification);
		if (ctl[i].sound !== undefined)
			identity_switch_notify_sound(autoplay);
	}
}

// stop notification
function identity_switch_stop_notify(prop)
{
    // revert original favicon
    if (rcmail.env.favicon_href && rcmail.env.favicon_changed && (!prop || prop.action != 'check-recent')) {
        $('<link rel="shortcut icon" href="'+rcmail.env.favicon_href+'"/>').replaceAll('link[rel="shortcut icon"]');
        rcmail.env.favicon_changed = 0;
    }
}

// browser notification: window.focus and favicon change
function identity_switch_notify_basic()
{
    var w = rcmail.is_framed() ? window.parent : window;
    w.focus();

    var src = rcmail.assets_path('plugins/identity_switch/assets');

    // we cannot simply change a href attribute, we must to replace the link element (at least in FF)
	var link = $('<link rel="shortcut icon">').attr('href', src + '/alert.ico');
 	var olink = $('link[rel="shortcut icon"]', w.document);
    if (!rcmail.env.favicon_href)
        rcmail.env.favicon_href = olink.attr('href');

    rcmail.env.favicon_changed = 1;
    link.replaceAll(olink);
}

// desktop notification
function identity_switch_notify_desktop(title, msg, timeout, errmsg)
{
	if (!('Notification' in window) || window.Notification.permission !== "granted") 
	{
		alert(decodeURIComponent(errmsg));
		window.Notification.requestPermission();
		return;
	}
		 
    var popup = new window.Notification(decodeURIComponent(title), {
                dir: "auto",
                lang: "",
                body: decodeURIComponent(msg),
                icon: rcmail.assets_path('plugins/identity_switch/assets/alert.gif')
    	});
	popup.onclick = function() { this.close(); };
	setTimeout(function() { popup.close(); }, timeout * 1000);
}

// sound notification
function identity_switch_notify_sound(errmsg) 
{
    var src = rcmail.assets_path('plugins/identity_switch/assets/alert');

	if (!('Notification' in window) || window.Notification.silent) 
	{
		alert(decodeURIComponent(errmsg));
		return;
	}
		 
	if (!('Navigator' in window) && window.Navigator.getAutoplayPolicy &&
		window.Navigator.getAutoplayPolicy('mediaelement') != 'allowed') 
	{
		alert(decodeURIComponent(errmsg));
		window.Notification.requestPermission();
		return;
	}
	
    new Audio(src + '.mp3').play();
}

// ------------ menu handling -------------------------------

// show/hide menu
function identity_switch_toggle_menu(offset) 
{
	var d = $('#identity_switch_dropdown'); 

	if (d.is(':hidden')) 
	{
		d.show();
		$('#messagelist-fixedcopy').css('z-index', 'auto');
		
		// scroll to iid
		d.scrollTop(offset);
	} else
		d.hide();
}

// hide menu on mouse clicks
$(document).click(function(event) 
{ 
    // Check for left button
    if (event.button == 0) 
	{
		// close dropdown if clicked somewhere
		var id = event.target.id;
		var d = $('#identity_switch_dropdown'); 
	    if (id != 'identity_switch_menu' && !d.is(':hidden'))
			d.hide();
    }
});

// stop notification
function identity_switch_stop_notify(prop)
{
    // revert original favicon
    if (rcmail.env.favicon_href && rcmail.env.favicon_changed && (!prop || prop.action != 'check-recent')) 
	{
        $('<link rel="shortcut icon" href="'+rcmail.env.favicon_href+'"/>').replaceAll('link[rel="shortcut icon"]');
        rcmail.env.favicon_changed = 0;
    }
}

// ------------ set menu position -------------------------------

$(function() 
{
	var $sw = $('#identity_switch_menu');
	var isOk = false;

	if ($sw.length == 0)
		return;
	
	switch (rcmail.env['skin']) 
	{
	case 'larry':
		isOk = identity_switch_skinLarry($sw);
		break;
			
	case 'classic':
		isOk = identity_switch_skinClassic($sw);
		break;

    case 'elastic':
	case 'hivemail':
        isOk = identity_switch_skinElastic($sw);
		break;
    
    default:
		alert('identity_switch plugin: Your skin "' + rcmail.env['skin'] + '" is not supported!')
		return;
	}

	if (isOk)
		$sw.show();
	else
		alert('identity_switch plugin: Cannot show drop down menu!');
});

function identity_switch_skinLarry($sw) 
{
	var $truName = $('.topright .username');
	
	if ($truName.length > 0 && $sw.length > 0) 
	{
		$sw.prependTo('#taskbar');
		$truName.hide();
		
		// move our selection menu a bit to the right
		$('#identity_switch_menu')
			.css('padding-top', '4px')
			.css('padding-bottom', '4px');
		$('#identity_switch_dropdown')
			.css('margin-left', '-92px');
			
		return true;
	}
	return false;
}

function identity_switch_skinClassic($sw) 
{
	var $taskBar = $('#taskbar');
	
	if ($taskBar.length > 0) 
	{
		$taskBar.prepend($sw);
		
		// move our selection menu a bit to the right
		$('#identity_switch_menu').css('left', '-10px')
			.css('top', '-5px');
		$('#identity_switch_dropdown')
			.css('left', '190px')
			.css('top', '-40px');
			
		return true;
	}
	return false;
}

function identity_switch_skinElastic($sw) 
{
    var $taskBar = $('.header-title.username');
    
    $sw.css('background-color', 'transparent').css('padding','4px 0 0 2rem');
    if ($taskBar.length > 0) 
	{
        $taskBar.prepend($sw);
        $taskBar.css('margin-left', '20px');

		// remove text from <span>
	    var $node = $('.header-title.username');
	 
		var newNode = $('<' + $node[0].nodeName + '/>');
		$.each( $node[0].attributes, function ( i, attribute ) {
	        newNode.attr(attribute.name, attribute.value);
		});
	  	$node.children().each(function() {
	    	newNode.append(this);
	  	});
	  	$node.replaceWith(newNode);

		// move our selection menu a bit to the bottom
		$('#identity_switch_menu')
			.css('height', '30px')
			.css('width', '180px');
		$('#identity_switch_dropdown')
			.css('left', '9px')
			.css('margin-top', '0');

       return true;
    }
	
    return false;
}

// change userid in composer window to select proper identity
function identity_switch_fixIdent(iid) 
{
	if (parseInt(iid) > 0)
		$("#_from").val(iid);
}


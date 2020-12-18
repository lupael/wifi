<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="expires" content="now" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Andrej Trcek (https://github.com/andrejtrcek/wifis)">
	
	<title>{$translate.93}</title>
	
	<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/fonts.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
	<link href="/assets/fontawesome/css/all.css" rel="stylesheet">

	{if isset($redirect)}<meta http-equiv="refresh" content="{$redirect.time}; url={$redirect.link}" />{/if}

</head><body>

	{contents}

	<script src="/assets/js/jquery-3.4.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/popper.min.js" type="text/javascript"></script>
	<script src="/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

<script type="text/javascript">
{if $module == "dashboard"}

jQuery(document).ready(function($) {
    $('.menutoggle').click(function() {
	    event.preventDefault();
		$('.sublist').toggle();
    });
});

{/if}

{if $module == "firstpage"}

jQuery(document).ready(function($) {
    $('.langselector').click(function() {
	    event.preventDefault();
	    $('.droplist').toggle();
		if ($('.droplist').is(":visible")) {
			$(".langselector").css({ 'text-decoration' : 'underline' });
		} else {
			$(".langselector").css({ 'text-decoration' : 'none' });	
		}
	});
});

{/if}
</script>

</body></html>
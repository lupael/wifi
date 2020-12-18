<?php

session_start();

/* set values */
$header = true;
$custom = false;
$values = array();

require_once 'vendor/autoload.php';

/* require files */
require_once("include/common.inc.php");
$import = import(array("dao",array("config","data","mikrotik","pay")),"db");
foreach($import as $row) require_once($row);

/* prepare vars */
$nicepath = nicepath();
$lang = 'en';
if (isset($nicepath[0]) && isset($config['languages'][$nicepath[0]])) {
	$lang = $nicepath[0];
	$nicepath = array_slice($nicepath,1);
} else {
	$browserlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	$suggested = (in_array($browserlang, array_keys($config['languages']))) ? $browserlang : 'en';
	$redirect = (!empty($nicepath)) ? $suggested . "/" . implode("/", $nicepath): $suggested;
	location("/" . $redirect);
}
$values['lang'] = $lang;

if (file_exists($config['path']['languages'] . $lang)) {
	$translation = parse_ini_file($config['path']['languages'] . $lang);
	if (isset($translation['lang'])) {
		$values['translate'] = $translation['lang'];
	}
}

/* include module */
$module = "firstpage";
if (isset($nicepath[0])) {
	$module_name = str_replace(array("-"),"_",$nicepath[0]);
    if (file_exists("modules/" . $module_name . "/index.php")) {
		$module = $module_name;
    }
}
require_once("modules/" . $module . "/index.php");
$mod = new $module;

/* connect DB */
$db->connect($config['db']);
unset($config['db']);

/* connect Redis */
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$userdata = $hs_data->get_userdata();

/* configure template system */
$tpl = new Monotek\MiniTPL\Template;
$tpl->set_compile_location($config['path']['cache'], true);
$tpl->set_paths(array("templates/", "modules/" . $module . "/templates/"));

$values['custom_html'] = false;

/* get & print content */
ob_start();
$r = $mod->get();
$i = ob_get_contents();
ob_end_clean();

if ($header && is_array($r)) {
	list($tpl_file, $val) = $r;
	$tpl->load($tpl_file);
	if (!empty($val) && is_array($val)) {
		$tpl->assign($val);
	}
	$tpl->assign("config",$config);
	$tpl->assign($values);
	$maincontent = $tpl->get();

	$tpl->load("site.tpl");
	$tpl->assign("contents",$maincontent);
	$tpl->assign("config",$config);
	$tpl->assign("module",$module);
	$tpl->assign($values);
	$r = $tpl->get();
} else {
	$r = $i;
}
echo cleanup(convertCharset(stripslashes($r)));
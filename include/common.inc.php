<?php

function nicepath() {
        $url = $_SERVER['REQUEST_URI'];	
        if (strstr($url,"?")) {
                list($url,)=explode("?",$url,2);
        }
        $path = explode("/",str_replace("//","/",trim(strip_tags($url))));
        $paths = array();
        foreach ($path as $row) {
                $row = trim($row);
                if ($row != "") $paths[] = $row;
        }
        return $paths;
}

function shorten($string, $len, $end=NULL) {
    $x = substr($string, 0, $len);
    if (strlen($x) < strlen($string)) {
        $y = explode(' ', $x);
        unset($y[count($y)-1]);
        $y = implode(' ', $y);
        if (strlen($y) > strlen($x)/2)
            $x = $y;
        $x .= $end;
    }
    return $x;
}
																		
function back() {
    if(isset($_SERVER['HTTP_REFERER'])) return header("Location: ".$_SERVER['HTTP_REFERER']);
    header("Location: /");
}

function location($url) {
    return header("Location: " . $url);
}

function import() {
    $args = func_get_args();
    $file = array();
    foreach($args as $row)
    {
	if(is_array($row))
	{
	    if(is_array($row[1]))
	    {
		foreach($row[1] as $r)
		{
		    $file[] = $row[0] . "/" . $r . ".php";
		}
	    } else {
		$file[] = $row[0] . "/" . $row[1] . ".php";
	    }
	} else {
	    $file[] = "include/class." . $row . ".php";
	}
	
    }

    foreach($file as $k=>$v)
    {
	if(!file_exists($v)) unset($file[$k]);
    }

    return $file;
}

function dao($mod,$ext = "index")
{
    if(file_exists("modules/" . $mod . "/dao/" . $ext . ".php"))
    {
	require_once("modules/" . $mod . "/dao/" . $ext . ".php");
    }

    $u = $mod . "_dao";
    if($ext!="index")
    {
	$u = $mod . "_" . $ext . "_dao";
    }
    return new $u;
}

function getFirst($items) {
    $items = array_slice($items,0,1);
    return $items[0];
}

function cleanup($c) {
	return str_replace(array("\n","\t"),"",$c);
}

function getip() {
    return ((isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
}

function fetchGetParameters() {
    $url = $_SERVER['REQUEST_URI'];	
    if (strstr($url,"?")) {
		list($url,$get)=explode("?",$url,2);
    }
    
    if(isset($get) && trim($get)) {
		$get = explode("&",trim($get,"?"));	
		if(isset($get) && !empty($get))	{
		    $params = array();
		    foreach($get as $row) {
				$o = explode("=",$row);
				if(isset($o[0]) && isset($o[1])) {
				    $params[$o[0]] = $o[1];
				}			
		    }
		    return $params;
		}
    }
    return;
}
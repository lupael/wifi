<?php

class db
{
    function connect($config) {
		$this->sql = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    }

    function query($string) {
		$q = $this->sql->query($string) or die(mysqli_error($this->sql));
		return $q;
    }

    function q($query) {
		if (func_num_args() == 1) {
			return $this->query($query);
		}
		$q = explode('?', $query);
		$sql = $q[0];
		unset($q[0]);
		$i = 1;
		foreach ($q as $q2) {
			$sql .= "'".$this->escape(func_get_arg($i++))."'".$q2;
		}
		return $this->query($sql);
    }
    
    function fetch_array($string) {
		return $this->sql->fetch_array($string);
    }

    function fetch_assoc($string) {
		return $string->fetch_assoc();
    }

    function fetch($string) {
		return $this->fetch_assoc($string);
    }

    function fetch_row($string) {
		return $this->sql->fetch_row($string);
    }
    
    function num_rows($string) {
		return $this->sql->num_rows($string);
    }

    function escape($string) {
		return $this->sql->real_escape_string($string);
    }

    function insert($table, $fields) {
		foreach ($fields as $k=>$v) {
			$fields[$k] = $this->escape($v);
		}
		return $this->query("insert into ".$table." (".implode(",",array_keys($fields)).") values ('".implode("','",$fields)."');");
    }

    function update($table,$fields,$keyname) {
		$retval = 'update '.$table.' set ';
		if (!is_array($keyname)) {
			$keyname = array($keyname);
		}
		foreach ($fields as $k=>$v) {
			if (!in_array($k,$keyname)) {
				$retval .= $k."='".$this->escape($v)."',";
			}
		}

		$retval = substr($retval,0,-1);
		$retval .= ' where ';

		foreach ($keyname as $key) {
			$retval .= $key."='".$this->escape($fields[$key])."' and ";
		}
		$retval = substr($retval,0,-5);
		return $this->query($retval);
    }

    function insert_id() {
		list($id) = $this->get_line("select LAST_INSERT_ID();",0);
		return $id;
    }

    function get_row($q) {
	    $i = $this->q($q);
		return $i->fetch_assoc();
    }

    function get_line($q) {
	    $i = $this->q($q);
		return $i->fetch_row();
    }

    function get_row_all($id,$q) {
		$i = $this->q($q);
		$u = array();
		while ($row = $this->fetch($i)) {
			$u[$row[$id]] = $row;
		}
		return $u;
    }
}

$db = new db;
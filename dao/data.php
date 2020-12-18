<?php

class hotspot_data {
	
	function get_packages() {
		global $db, $redis, $values;
		$key = "hotspot:packages:" . $values['lang'];
		if ($r = $redis->get($key)) {
			return json_decode($r, true);
		}
		
		$lines = $db->get_row_all("id", "select * from packages where active='1' order by position asc");
		if (!empty($lines)) {
			foreach ($lines as $k=>$v) {
				$lines[$k]['name'] = $values['translate'][$v['name']];
				$lines[$k]['price'] = array('value' => $v['price']);
				$lines[$k]['price']['display'] = str_replace(".", ",", $lines[$k]['price']['value']);
				if (substr($lines[$k]['price']['display'],-1) == '0') {
					$lines[$k]['price']['display'] = substr($lines[$k]['price']['display'],0,-1);
				}
				$lines[$k]['transfer'] = array('value' => $v['transfer'], 'display' => round($v['transfer']/1000));

				$lines[$k]['duration'] = array('value' => $v['duration'], 'display' => $v['duration'] / 3600, 'unit' => $values['translate'][68]);
				if ($lines[$k]['duration']['display'] > 24) {
					$lines[$k]['duration']['display'] = $lines[$k]['duration']['display']/24;
					$lines[$k]['duration']['unit'] = $values['translate'][64];
				}
			}			
		}
		$redis->setEx($key, 60, json_encode($lines));
		return $lines;		
	}
	
	function get_transaction($transaction_id) {
		global $db;
		return $db->get_row("select * from transactions where id='" . $transaction_id . "'");
	}

	function get_transactions($user_id) {
		global $db, $values;
		$transactions = $db->get_row_all("id", "select * from transactions where user_id='" . $user_id . "' order by created asc");
		if (!empty($transactions)) {
			$packages = $this->get_packages();
			foreach ($transactions as $k=>$v) {
				$transactions[$k]['package'] = $packages[$v['package_id']];
				$transactions[$k]['date_created'] = $this->convert_date($v['created']);
				if ($v['stamp_start'] !== '0000-00-00 00:00:00') {
					$transactions[$k]['date_start'] = $this->convert_date($v['stamp_start']);
				}
			}
			return $transactions;
		}
		return false;
	}
	
	function get_transaction_last($user_id) {
		global $db;
		$r = $db->get_row("select * from transactions where user_id='" . $user_id . "' and confirmed='1' and stamp_end>'" . date("Y-m-d H:i:s") . "' order by created desc limit 0,1");
		if (!empty($r) && isset($r['id'])) {
			$packages = $this->get_packages();
			$r['package'] = $packages[$r['package_id']];
			$r['date_created'] = $this->convert_date($r['created']);
			$r['date_start'] = $this->convert_date($r['stamp_start']);
			$r['date_end'] = $this->convert_date($r['stamp_end']);
			return $r;
		}
	}

	function get_userdata() {
		global $db, $mikrotik;
		
		$response = array();

		$local = (isset($_SESSION['user']) && !empty($_SESSION['user'])) ? $_SESSION['user'] : false;
		$remote = $this->get_user();
		
		if ($local !== false) {
			$user = $this->get_username($local['username']);
			if (empty($user)) {
				$this->user_logout();
				return false;
			}
			$_SESSION['user'] = $user;

			if ($remote !== false) {
				$_SESSION['user']['remote'] = $remote;
			}
			$response = $_SESSION['user'];	
		}
		
		if ($remote !== false) {
			$user = $this->get_username($remote['username']);
			if (empty($user)) {
				return false;
			}
			$_SESSION['user'] = $user;
			$_SESSION['user']['remote'] = $remote;
			$response = $_SESSION['user'];
		}
		
		if (!empty($response)) {
			$response['local_address'] = getip();
			$response['local_mac'] = $mikrotik->get_mac();
			return $response;
		}
		return false;
	}

	function get_user() {
		global $db, $mikrotik, $redis;
		
		$key = "hotspot:user:" . md5(getip());
		if ($r = $redis->get($key)) {
			return json_decode($r, true);
		}

		$user = $mikrotik->get_user();
		if ($user === false) {
			return false;	
		}

		$redis->setEx($key, 60, json_encode($user));
		return $user;
	}
	
	function user_logout() {
		$_SESSION['user'] = NULL;
		unset($_SESSION['user']);
	}
	
	function get_user_profile($user_id) {
		global $db;
		return $db->get_row("select * from users where id='" . $user_id . "'");
	}
	
	function get_username($username) {
		global $db;
		return $db->get_row("select * from users where username='" . $db->escape($username) . "'");
	}
	
	function get_action_last($action) {
		global $db;
		return $db->get_row("select * from logs where action='" . $action . "' and ip='" . getip() . "' order by stamp desc limit 0,1");	
	}

	function set_transaction($data) {
		global $db;
		$data['created'] = date("Y-m-d H:i:s");
		return $db->insert("transactions", $data);
	}	
	
	function set_action($action, $data = '') {
		global $db;
		$db->insert("logs", array(
					"action" => $action,
					"ip" => getip(),
					"data" => $data
		));	
	}

	function statistics_user_active($user) {
		global $db, $mikrotik, $redis;
		
		$key = "hotspot:statistics:" . $user['username'];
		if ($r = $redis->get($key)) {
			return json_decode($r, true);
		}
		
		$stats = $mikrotik->get_statistics_user_active($user['username']);
		if ($stats) {
			$redis->setEx($key, 60000, json_encode($stats));
			return $stats;
		}
	}

	function get_statistics_user($transaction) {
		global $db;
		return $db->get_row("select * from statistics where transaction_id='" . $transaction . "' order by stamp desc limit 0,1");
	}
	
	function get_statistics_user_active($transaction) {
		global $db;
		return $db->get_row("select * from statistics_active where transaction_id='" . $transaction . "' order by stamp desc limit 0,1");
	}	

	function convert_date($timestamp) {
		global $values;
		$i = array();
		$i[] = date("d. m. Y", strtotime($timestamp));
		$i[] = $values['translate'][73];
		$i[] = date("H:i", strtotime($timestamp));
		return implode(" ", $i);
	}
	
	function convert_time($seconds) {
		global $values;
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		$r = $dtF->diff($dtT);
		$o = array();
		if ($r->d > 0) {
			$o[] = $r->d;
			if ($r->d==1) { $o[] = $values['translate'][61]; }
			if ($r->d==2) { $o[] = $values['translate'][62]; }
			if ($r->d>2 && $r->d<5) { $o[] = $values['translate'][63]; }
			if ($r->d>4) { $o[] = $values['translate'][64]; }
		}
		if ($r->h > 0) {
			$o[] = $r->h;
			if ($r->h==1) { $o[] = $values['translate'][65]; }
			if ($r->h==2) { $o[] = $values['translate'][66]; }
			if ($r->h>2 && $r->h<5) { $o[] = $values['translate'][67]; }
			if ($r->h>4) { $o[] = $values['translate'][68]; }
		}

		if ($r->i > 0) {
			$o[] = $r->i;
			if ($r->i==1) { $o[] = $values['translate'][69]; }
			if ($r->i==2) { $o[] = $values['translate'][70]; }
			if ($r->i>2 && $r->i<5) { $o[] = $values['translate'][71]; }
			if ($r->i>4) { $o[] = $values['translate'][72]; }
		}

		return implode(" ", $o);
	}
	
	function save_statistics_user($user) {
		global $db, $mikrotik;

		$stats = $mikrotik->get_statistics_user($user['username']);
		if ($stats) {
			$transaction = $this->get_transaction_last($user['id']);
			if (!empty($transaction)) {
				$stats['transaction_id'] = $transaction['id'];
			}
			$stats['user_id'] = $user['id'];
			
			$db->insert("statistics", $stats);
		}		
	}

	function save_statistics_user_active($user) {
		global $db, $mikrotik;
		$stats = $mikrotik->get_statistics_user_active($user['username']);
		if ($stats) {
			$transaction = $this->get_transaction_last($user['id']);
			if (!empty($transaction)) {
				$stats['transaction_id'] = $transaction['id'];
			}
			$stats['user_id'] = $user['id'];
			$db->insert("statistics_active", $stats);
		}		
	}
}

$hs_data = new hotspot_data;
<?php

class dashboard
{
	var $userdata;
	
    function get()
    {
		global $nicepath, $values, $tpl, $hs_data, $userdata;
		
		if ($userdata === false || empty($userdata)) {
			location("/" . $values['lang'] . "/");
			die;
		}

		$this->userdata = $userdata;
		
		$data = array();

		$func = 'general';
		if (method_exists($this, 'get_' . $nicepath[1]) && is_callable(array($this, 'get_' . $nicepath[1]))) {
			$func = $nicepath[1];
		}
		$data['module'] = $func;
		$func = 'get_' . $func;
		
		list($tpl_file, $title, $val) = $this->$func();
		$tpl->load($tpl_file);
		if (!empty($val) && is_array($val)) {
			$tpl->assign($val);
		}
		$tpl->assign($values);
		$data['content'] = $tpl->get();

		$data['title'] = $values['translate'][$title];

	    return array("dashboard.tpl", $data);
    }
	
	function get_general() {
		global $hs_data, $mikrotik;
		
		$data = array();
		$data['package'] = $hs_data->get_transaction_last($this->userdata['id']);
		$data['user'] = $this->userdata;

		if (!empty($data['package'])) {
			$data['user']['statistics'] = array();
			$time_left = strtotime($data['package']['stamp_end'])-time();
			$data['user']['statistics']['time'] = round(($data['package']['package']['duration']['value']-$time_left)/$data['package']['package']['duration']['value'],2)*100;
			$data['user']['statistics']['time_left'] = $hs_data->convert_time($time_left);
			$data['user']['statistics']['time_used'] = $hs_data->convert_time($data['package']['package']['duration']['value']-$time_left+60);


$data['user']['remote']['limit_bytes_total'] = 9998047422;


			$data_left = ($data['package']['package']['transfer']['value']*1000000)-$data['user']['remote']['limit_bytes_total'];
			$data['user']['statistics']['usage'] = round($data_left/($data['package']['package']['transfer']['value']*1000000),2)*100;
			$data['user']['statistics']['transfer_left'] = round($data['user']['remote']['limit_bytes_total']/1000000000,3);
			$data['user']['statistics']['transfer_used'] = round((($data['package']['package']['transfer']['value']*1000000)-$data['user']['remote']['limit_bytes_total'])/1000000000,3);
		}

		return array("general.tpl", 39, $data);
	}
	
	function get_payment() {
		global $hs_data, $nicepath;
		
		$data = array();
		$data['packages'] = $hs_data->get_packages();
		
		if ($nicepath[2] == "new" && intval($nicepath[3]) && isset($data['packages'][$nicepath[3]])) {
			return $this->get_order();
		}

		$data['transactions'] = $hs_data->get_transactions($this->userdata['id']);

		if (!empty($data['transactions'])) {
			$data['last'] = $hs_data->get_transaction_last($this->userdata['id']);
			$data['active'] = (strtotime($data['last']['stamp_end']) < time()) ? false : true;
		}
		return array("transactions.tpl", 40, $data);	
	}
	
	function get_order() {
		global $hs_data, $values, $pay, $nicepath;

		$data = array();
		
		$last = $hs_data->get_action_last('1');
		if (isset($last) && strtotime($last['stamp']) > strtotime('-5 min')) {
			$data['error'] = $values['translate'][52];
		}
		
		if (empty($data)) {
			$hs_data->set_action('1');
			
			$user = $hs_data->get_user($this->userdata['id']);
			$packages = $hs_data->get_packages();
			
			$transaction = array();
			$transaction['package_id'] = $packages[$nicepath[3]]['id'];
			$transaction['transaction'] = md5($transaction['package_id'] . time());
			$transaction['user_id'] = $this->userdata['id'];

			$order = $pay->create_order(array("package" => $packages[$nicepath[3]], "transaction" => $transaction['transaction'], "lang" => $values['lang']));
			
			if (!isset($order) || $order === false) {
				$data['error'] = $values['translate'][54];
			}
			
			if (empty($data)) {
				$transaction['paypal'] = $order['id'];
				$hs_data->set_transaction($transaction);
				$data["success"] = $values['translate'][53];
				$values['redirect'] = array("time" => 3, "link" => $order['link']);
			}
		}
		
		return array("order.tpl", 51, $data);
	}
	
	function get_settings() {
		global $values, $db, $mikrotik;
		
		$data = array();
		$data['userdata'] = $this->userdata;

		if (!empty($_POST)) {
			$i = array();
			$i["password"] = trim(strip_tags($_POST["password"]));
			$i["repeat"] = trim(strip_tags($_POST["repeat"]));
			$i["email"] = trim(strip_tags($_POST["email"]));
			
			$data['error'] = array();

			if (!$i["email"] || !filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
				$data['error']['email'] = $values['translate'][31];
			}

			if ($i["password"] || $i["repeat"]) {
				if (!$i["password"] || strlen($i["password"])<5) {
					$data['error']['password'] = $values['translate'][32];
				}
					
				if (!$i["repeat"] || $i["repeat"]!==$i["password"]) {
					$data['error']['repeat'] = $values['translate'][33];
				}
			}

			if (empty($data['error'])) {
				if ($i["email"] !== $data['userdata']['email']) {
					$user = $db->get_row("select * from users where email LIKE '%" . $db->escape($i["email"])  . "%'");
					if (isset($user) && $user['email'] == $i["email"]) {
						$data['error']['email'] = $values['translate'][36];
					}
				}
				
				if (empty($data['error'])) {
					$db->update('users', array(
									'id' => $data['userdata']['id'],
									'email' => $i["email"]
								), 'id');
					
					$_SESSION['user']['email'] = $i["email"];
					
					$hotspot = $mikrotik->set_password($data['userdata']['username'], $i["password"]);
					if ($hotspot === false) {
						$data['error'][] = $values['translate'][56];
					}
					
					if (empty($data['error'])) {
						unset($i["password"], $i["repeat"]);
						$data['success'] = $values['translate'][57];
					}
				}
			}
			
			if (!empty($data['error']) || isset($data['success'])) {			
				$data['form'] = $i;
			}
		}

		return array("settings.tpl", 41, $data);
	}

	function get_help() {
		return array("help.tpl", 42, array());
		
		
	}	
	
	function get_logout() {
		global $hs_data, $mikrotik, $values;

		$data = array();

		$remote = $mikrotik->set_logout($this->userdata['username']);
		$hs_data->user_logout();
		$data['success'] = $values['translate'][79];
		$values['redirect'] = array('time' => 5, 'link' => '/' . $values['lang'] . '/');

		return array("logout.tpl", 43, $data);
	}	
	
}
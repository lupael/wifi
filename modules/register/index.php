<?php

class register
{
    function get()
    {
	    global $db, $nicepath, $hs_data, $mikrotik, $config, $pay, $values;

		if ($nicepath[1] == "payment") { return $this->payment(); }
		if (in_array($nicepath[1], array("terms", "privacy"))) { return $this->rules(); }

		$data = array();
		
		$data['packages'] = $hs_data->get_packages();

	    if (!empty($_POST)) {
			$i = array();
		    $i["username"] = trim(strip_tags(htmlspecialchars($_POST["username"], ENT_QUOTES)));
		    $i["password"] = trim(strip_tags($_POST["password"]));
		    $i["repeat"] = trim(strip_tags($_POST["repeat"]));
		    $i["email"] = trim(strip_tags($_POST["email"]));
		    $i["package"] = intval($_POST["package"]);
			$i["traceability"] = json_encode($_SERVER);
		    
			$data['user'] = $i;
			$data['error'] = array();
			
			if (!$i["username"] || strlen($i["username"])<5 || in_array(strtolower($i["username"]), array("admin", "root", "mikrotik", "nobody", "hotspot"))) {
				$data['error']['username'] = $values['translate'][30];
			}
			
			if (!$i["email"] || !filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
				$data['error']['email'] = $values['translate'][31];
			}
			
			if (!$i["password"] || strlen($i["password"])<5) {
				$data['error']['password'] = $values['translate'][32];
			}
			
			if (!$i["repeat"] || $i["repeat"]!==$i["password"]) {
				$data['error']['repeat'] = $values['translate'][33];
			}
			
			if (!$i["package"] || !isset($data['packages'][$i["package"]])) {
				$data['error']['package'] = $values['translate'][34];
			}
			
			if (empty($data['error'])) {
				$user = $db->get_row("select * from users where username LIKE '%" . $db->escape($i["username"]) . "%' OR email LIKE '%" . $db->escape($i["email"])  . "%'");
				
				if (isset($user) && ($user['username'] == $i["username"] || strtolower($user['username']) == strtolower($i["username"]))) {
					$data['error']['username'] = $values['translate'][35];
				}
				
				if (isset($user) && $user['email'] == $i["email"]) {
					$data['error']['email'] = $values['translate'][36];
				}
				
				if (empty($data['error'])) {
					$transaction = array();
					$transaction['package_id'] = $i["package"];
					$transaction['transaction'] = md5($i["package"] . time());
					
					$i['router_id'] = $mikrotik->register(array('username' => $i['username'], 'password' => $i["password"], 'profile' => $data['packages'][$i["package"]]));
					
					if ($i['router_id'] === false) {
						$data['error'][] = $values['translate'][24];
					}
					
					if (empty($data['error'])) {
						$data["profile"] = $data['packages'][$i["package"]];
						unset($i["package"], $i["repeat"], $i["password"]);
						
						$mac = $mikrotik->get_mac();
						if ($mac !== false) {
							$i['mac'] = $mac;
						}
						
						$db->insert("users", $i);
						
						$transaction['user_id'] = $db->insert_id();

						if (!isset($transaction['user_id']) || !intval($transaction['user_id'])) {
							$data['error'][] = $values['translate'][24];
						}
						
						if (empty($data['error'])) {
							$order = $pay->create_order(array("package" => $data["profile"], "transaction" => $transaction['transaction'], "lang" => $values['lang']));
							if (!isset($order) || $order === false) {
								$data['error'][] = $values['translate'][24];
							}
							
							if (empty($data['error'])) {
								$transaction['paypal'] = $order['id'];
								$hs_data->set_transaction($transaction);
								$data["success"] = $values['translate'][25];
								$values['redirect'] = array("time" => 3, "link" => $order['link']);
							}
						}
					}					
				}
			}
	    }
	    return array("register.tpl", $data);
    }
	
	function payment() {
		global $db, $nicepath, $hs_data, $values, $pay, $mikrotik;
		
		$data = array();

		switch ($nicepath[2]) {
			case "cancel":
				$db->update(	'transactions',
								array(	'paypal' => $_GET['paymentId'],
										'confirmed' => 2),
								'paypal');
			
				$data = array('error' => $values['translate'][26], 'icon' => 'far fa-times-circle');
			break;
			
			case "confirm":
				if (isset($_GET['paymentId']) && isset($_GET['token'])) {
					$transaction = $db->get_row("select * from transactions where transaction='" . $db->escape(end($nicepath)) . "' and paypal='" . $db->escape($_GET['paymentId']) . "'");
					
					if (empty($transaction)) {
						$data = array('error' => $values['translate'][27], 'icon' => 'far fa-times-circle');
					}

					if (!empty($transaction['confirmed']) && $transaction['confirmed'] == '1') {
						$data = array('success' => $values['translate'][28], 'icon' => 'far fa-check-circle');
					}

					if (empty($data)) {
						$check = $pay->check_order(htmlspecialchars($_GET['paymentId'], ENT_QUOTES), htmlspecialchars($_GET['PayerID'], ENT_QUOTES));

						if (!isset($check) || $check === false || $check['state'] !== 'approved') {
							$data = array('error' => $values['translate'][29], 'icon' => 'fas fa-spinner fa-pulse');
							$values['redirect'] = array('time' => 10, 'link' => $_SERVER['REQUEST_URI']);
						}
						
						if (empty($data)) {
							$packages = $hs_data->get_packages();
							$db->update(	'transactions',
											array(	'id' => $transaction['id'],
													'confirmed' => 1,
													'confirmation' => serialize($check['confirmation']),
													'stamp_start' => date("Y-m-d H:i:s"),
													'stamp_end' => date("Y-m-d H:i:s", strtotime("+" . $packages[$transaction['package_id']]['duration']['value'] . " seconds"))),
											'id');
											
							$transfer = $mikrotik->set_package($transaction['id']);
							if ($transfer === false) {
								$data = array('error' => $values['translate'][38], 'icon' => 'far fa-times-circle');
							}
							
							if (empty($data)) {
								$data = array('success' => $values['translate'][28], 'icon' => 'far fa-check-circle');

								$remote = $mikrotik->set_logout();
								$hs_data->user_logout();
							}
						}
					}
				}
			break;
		}

		return array("payment.tpl", $data);
	}
	
	function rules() {
		global $tpl, $values, $nicepath;
		
		$tpl->load($nicepath[1] . "_" . $values['lang'] . ".tpl");
		$tpl->assign($values);
		$r = $tpl->get();
		
		return array("rules.tpl", array("rules" => $r));
	}	
}
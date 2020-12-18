<?php
use PEAR2\Net\RouterOS;

class mikrotik {
	
	var $client;
	
	function __construct($ip, $user, $password) {
		try {
			$this->client = new RouterOS\Client($ip, $user, $password);
		} catch (Exception $e) { }
	}
	
	function register($data) {
		try {
			$util = new RouterOS\Util($this->client);
			$util->setMenu('/ip hotspot user');
			$users = $util->add(
						array(	'name' => $data['username'],
								'password' => $data['password'],
								'profile' => $data['profile']['profile'],
								'email' => $data['email'],
								'disabled' => 'no',
								'limit-uptime' => '00:00:01',
								'limit-bytes-total' => '1'
								)
			);
		} catch (Exception $e) { return false; }
		return $users;
	}

	function get_user() {
		$data = array();
		try {
			$request = new RouterOS\Request('/ip hotspot active print');
			$request->setQuery(RouterOS\Query::where('address', getip()));
			$responses = $this->client->sendSync($request);
		} catch(Exception $e) { return false; }

		if ($responses->getProperty('uptime')) {
			$data['username'] = $responses->getProperty('user');
			$data['address'] = $responses->getProperty('address');
			$data['mac_address'] = $responses->getProperty('mac-address');
			$data['uptime'] = $responses->getProperty('uptime');
			$data['session_time_left'] = $responses->getProperty('session-time-left');
			$data['bytes_in'] = $responses->getProperty('bytes-in');
			$data['bytes_out'] = $responses->getProperty('bytes-out');
			$data['limit_bytes_total'] = $responses->getProperty('limit-bytes-total');
			return $data;
		}		
		return false;
	}
	
	function set_package($order_id) {
		global $hs_data;
		
		$transaction = $hs_data->get_transaction($order_id);
		if (!empty($transaction)) {
			$user = $hs_data->get_user_profile($transaction['user_id']);

			if (!empty($user)) {
				$hs_data->save_statistics_user($user);

				$packages = $hs_data->get_packages();
				try {
					$request = new RouterOS\Request('/ip hotspot user set');
					$r = $this->client->sendSync(
							$request
							->setArgument('numbers', $user['username'])
							->setArgument('profile', $packages[$transaction['package_id']]['profile'])
							->setArgument('limit-uptime', $packages[$transaction['package_id']]['duration']['value'])
							->setArgument('limit-bytes-total', $packages[$transaction['package_id']]['transfer']['value']*1000000));
				} catch (Exception $e) { return false; }
				
				$this->reset_counter($user['username']);
				
				return true;
			}			
		}
		return false;
	}
	
	function reset_counter($user) {
		$user_id = $this->get_user_id($user);
		
		if ($user_id) {
			try {
				$request = new RouterOS\Request('/ip hotspot user reset-counters');
				$request->setArgument('numbers', $user_id);
				$this->client->sendSync($request);
			} catch (Exception $e) { return false; }
		}
		return true;
	}
	
	function check_user($user, $password) {
		$data = array();
		try {
			$request = new RouterOS\Request('/ip hotspot user print');
			$request->setQuery(RouterOS\Query::where('name', $user));
			$responses = $this->client->sendSync($request);
			
			if ($responses->getProperty('password') && $responses->getProperty('password') === $password) {
				return array('profile' => $responses->getProperty('profile'));
			}
		} catch (Exception $e) { return false; }
		return false;
	}
	
	function set_login($username, $password) {
		try {
			$request = new RouterOS\Request('/ip hotspot active login');
			$r = $this->client->sendSync(
					$request
					->setArgument('user', $username)
					->setArgument('password', $password)
					->setArgument('mac-address', $this->get_mac())
					->setArgument('ip', getip()));		
		} catch (Exception $e) { return false; }
		return true;		
	}
	
	function set_logout($user) {
		$user_id = $this->get_user_id($user, 1);
		if ($user_id) {
			try {
				$request2 = new RouterOS\Request('/ip hotspot active remove');
				$r = $this->client->sendSync(
						$request2
						->setArgument('.id', $user_id));
			} catch (Exception $e) { return false; }
			return true;
		}
		return false;
	}

	function get_statistics_user($user) {
		$data = array();
		try {
			$request = new RouterOS\Request('/ip hotspot user print');
			$request->setQuery(RouterOS\Query::where('name', $user));
			$responses = $this->client->sendSync($request);

			if ($responses->getProperty('uptime')) {
				$data['uptime'] = $responses->getProperty('uptime');
				$data['bytes_in'] = $responses->getProperty('bytes-in');
				$data['bytes_out'] = $responses->getProperty('bytes-out');
				$data['packets_in'] = $responses->getProperty('packets-in');
				$data['packets_out'] = $responses->getProperty('packets-out');
			}
		} catch (Exception $e) { return false; }
		
		if (!empty($data)) {		
			return $data;
		}
		return false;
	}
	
	function get_statistics_user_active($user) {
		$data = array();
		try {
			$request = new RouterOS\Request('/ip hotspot active print');
			$request->setQuery(RouterOS\Query::where('user', $user));
			$responses = $this->client->sendSync($request);
			
			if ($responses->getProperty('uptime')) {
				$data['address'] = $responses->getProperty('address');
				$data['mac_address'] = $responses->getProperty('mac-address');
				$data['uptime'] = $responses->getProperty('uptime');
				$data['session_time_left'] = $responses->getProperty('session-time-left');
				$data['bytes_in'] = $responses->getProperty('bytes-in');
				$data['bytes_out'] = $responses->getProperty('bytes-out');
				$data['limit_bytes_total'] = $responses->getProperty('limit-bytes-total');
			}
		} catch (Exception $e) { return false; }
		if (!empty($data)) {		
			return $data;
		}
		return false;
	}

	function set_password($user, $password) {
		
		$user_id = $this->get_user_id($user);
		
		if ($user_id) {
			try {
				$request = new RouterOS\Request('/ip hotspot user set');
				$r = $this->client->sendSync(
						$request
						->setArgument('numbers', $user_id)
						->setArgument('password', $password));
			} catch (Exception $e) { return false; }
		}
	}
	
	function get_user_id($user, $address = 0) {
		global $redis;
		$key = "hotspot:userid:" . $user . ":" . $address;
		if ($r = $redis->get($key)) {
			return $r;
		}
		try {
			if ($address === 1) {
				$request = new RouterOS\Request('/ip hotspot active print');
				$request->setQuery(RouterOS\Query::where('user', $user)->andWhere('address', getip()));
			} else {
				$request = new RouterOS\Request('/ip hotspot user print');
				$request->setQuery(RouterOS\Query::where('name', $user));
			}
			$responses = $this->client->sendSync($request);
		} catch (Exception $e) { return false; }
		$redis->setEx($key, 60, $responses->getProperty('.id'));
		return $responses->getProperty('.id');
	}
	
	function get_mac() {
		global $redis;
		$key = "hotspot:mac:" . str_replace(".","",getip());
		if ($r = $redis->get($key)) {
			return $r;
		}

		try {
			$printRequest = new RouterOS\Request('/ip arp print .proplist=mac-address');
			$printRequest->setQuery(
				RouterOS\Query::where('address', getip())
			);
			$mac = $this->client->sendSync($printRequest)->getProperty('mac-address');
			if (null !== $mac) {
				$redis->setEx($key, 60, $mac);
				return $mac;
			}
		} catch (Exception $e) { return false; }
		return false;	
	}
}

$mikrotik = new mikrotik($config['routeros']['ip'], $config['routeros']['user'], $config['routeros']['password']);
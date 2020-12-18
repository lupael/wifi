<?php

class firstpage
{
    function get()
    {
	    global $hs_data, $values, $userdata, $mikrotik;
	    
	    $data = array();
	    
	    if ($userdata !== false && !empty($userdata)) {
			location("/" . $values['lang'] . "/dashboard");
			die;
		}

	    if (!empty($_POST)) {
		    $username = trim(strip_tags(htmlspecialchars($_POST["username"], ENT_QUOTES)));
		    $password = trim(strip_tags(htmlspecialchars($_POST["password"], ENT_QUOTES)));
		    
		    if (!$username || !$password) {
			    $data['error'] = $values['translate'][74];
		    }
		    
		    if (empty($data['error'])) {
			    
			    $hs_data->set_action('2', json_encode(array($username, $password, $_SERVER)));
			    
			    $profile = $hs_data->get_username($username);
			    if (empty($profile)) {
				    $data['error'] = $values['translate'][76];
			    }
			    
			    if (empty($data['error'])) {
				    $exists = $mikrotik->check_user($username, $password);
				    if ($exists === false) {
					    $data['error'] = $values['translate'][75];
				    }
				   
				    if (empty($data['error'])) {
					    $_SESSION['user'] = $profile;
					    $active = $hs_data->get_transaction_last($profile['id']);
					    if (!empty($active) || (isset($exists['profile']) && $exists['profile'] === 'Unlimited')) {
						    $login = $mikrotik->set_login($username, $password);
						    if ($login === false) {
							    $data['error'] = $values['translate'][77];
						    }
						    
						    if (empty($data['error'])) {
								location("/" . $values['lang'] . "/dashboard");
								die;
							}
					    }
					    location("/" . $values['lang'] . "/dashboard");
						die;
					}
				}
		    }  
	    }

	    return array("first.tpl",$data);
    }
}
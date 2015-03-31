<?php
App::uses('TrackerAppModel', 'Tracker.Model');
App::uses('HttpSocket', 'Network/Http');


class Project extends TrackerAppModel {
	
	public $token = __TRACKER_CLIENT_TOKEN; // this is the default token api ( a catch all client account works best )
	
	public $url = 'https://www.pivotaltracker.com/services/v5/';
	
	public $client = __TRACKER_CLIENT_USERNAME; // username of the client placeholder user
	
	public $accountId = __TRACKER_ACCOUNT_ID; // the main account id that the admin uses for projects
	
	public $useTable = false;
	
	
	public function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
		
		$this->HttpSocket = new HttpSocket(); 
		
		if (CakeSession::read('Auth.User.user_role_id') != 6 && CakeSession::read('Auth.User.other')) {
			// clients user role, all use the same api key (a manually created fake user in pivotal tracker called Client Bot)
			$this->token = CakeSession::read('Auth.User.other');
		}
    }

/**
 * Post method
 * 
 */
	public function post($endpoint = 'projects', $data, $options = array()) {
		$data = json_decode($data, true);
		$data = json_encode(array_merge($data, array('token' => $this->token)));
		$request = array(
			'header' => array(
		        'Content-Type' => 'application/json'
		    	)
			);
		$url = $url = $this->url . $endpoint;
		return $this->HttpSocket->post($url, $data, $request);
	}

/**
 * Post method
 * 
 */
	public function put($endpoint = 'projects', $data, $options = array()) {
		//if (!empty($data['text'])) {
			//$data['text'] = '@public ' . ' #client-comment ' . $data['text']; // must have @public to be seen by the public 
			//$data = json_encode(array_merge($data, array('token' => $this->token)));
		//}
		$data = json_decode($data, true);
		$data = json_encode(array_merge($data, array('token' => $this->token)));
		$request = array(
			'header' => array(
		        'Content-Type' => 'application/json'
		    	)
			);
		$url = $url = $this->url . $endpoint;
		return $this->HttpSocket->put($url, $data, $request);
	}

/**
 * Download method
 */
 	public function download($fileId, $options = array()) {
		$url = 'https://www.pivotaltracker.com/file_attachments/' . $fileId . '/download';
		$request = array(
				'header' => array(
			        'X-TrackerToken' => $this->token,
			    	)
				);
 		return $this->HttpSocket->get($url, null, $request);
 	}

/**
 * Upload method
 */
 	public function upload($data, $options = array()) {
		$url = 'https://www.pivotaltracker.com/services/v5/projects/' . $data['project_id'] . '/uploads';
		unset($data['story_id']);// can't send this
		unset($data['text']);// can't send this
		move_uploaded_file($data['file']['tmp_name'], '/opt/bitnami/php/tmp/' . $data['file']['name']);

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-TrackerToken: ' . $this->token, 
			'Content-Type: multipart/form-data'
			));
		$data['file'] = '@' . '/opt/bitnami/php/tmp/' . $data['file']['name'];
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
				
		
		// debug($data);
		// // Build the multipart body of the http request
		// $boundaryString = 'Boundary' . String::uuid();
		// $body .= "------" . $boundaryString;
		// $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"" . $data['file']['name'] . "\"\r\n";
		// $body .= "Content-Type: " . $data['file']['type'] . "\r\n";
		// //$body .= "Content-Transfer-Encoding: binary\r\n";
		// //$body .= "\r\n";
		// $body .= "------" . $boundaryString;
		// $body .= "Content-Disposition: form-data; name=\"file\"\r\n\r\n";
		// $body .= file_get_contents($data['file']['tmp_name'])."\r\n";
		// $body .= "------" . $boundaryString . "--\r\n";
		// // pr($body);
		// $data['file'] = file_get_contents($data['file']['tmp_name']);
		// $request = array(
			// 'header' => array(
		        // 'X-TrackerToken' => $this->token,
		        // 'Content-Type' => 'multipart/form-data; boundary=----' . $boundaryString
		    	// ),
		   	// 'body' => $body
			// );
 		// return $this->HttpSocket->post($url, $data, $request);
 	}
		
/**
 * Get method
 */
	public function get($endpoint = 'projects', $options = array()) {
		// construct the request url
		$query = null;
		if (!empty($options['?'])) {
			foreach ($options['?'] as $key => $value) {
				$query .= '&' . $key . '=' . $value;
			}
		}
		if (defined('__TRACKER_CLIENT_TOKEN') || !empty($this->token)) {
			$url = $this->url . $endpoint . '?token=' . $this->token . $query;
		} else {
			$url = $this->url . $endpoint;
		}
		return json_decode($this->HttpSocket->get($url), true);
		
		// doesn't come down here
		// $endpoint = 'projects/' . $this->projectId . '/stories';
		// $url = $this->url . $endpoint . '?token=' . $this->token;
// 		
		// $results = json_decode($this->HttpSocket->get($url), true);
		// debug($results);
		// foreach ($results as $result) {
			// $url = $this->url . 'projects/' . $this->projectId . '/stories/' . $result['id'] . '/comments?token=' . $this->token . '&fields=story_id,text,person_id,created_at,updated_at,file_attachments';
			// $comments = json_decode($this->HttpSocket->get($url), true);
			// debug($comments);
		// }
		// exit;
		// // array query
		// $results = $HttpSocket->get('http://www.google.com/search', array('q' => 'cakephp'));
	}

/**
 * Me method
 * A special get request for getting an api key, using the users pivotal tracker login.
 * 
 */
 	public function me($user = null, $password = null) {
 		$this->HttpSocket->configAuth('Basic', $user, $password);
		return $this->get('me');
 	}
	
/**
 * Create constants, a backward compatibility function for when a new constant setting is introduced 
 * 
 * @todo This is unfinished, and might remain that way :)  1/21/2015 rk
 */
 	public function createConstants() {
		$Setting = ClassRegistry::init('Setting');
		
 		if (!defined('__TRACKER_CLIENT_TOKEN')) {
 			debug('Create client token');
			exit;
		} 
		
		if (!defined('__TRACKER_ACCOUNT_ID') && defined('__TRACKER_CLIENT_TOKEN')) {
			$this->token = __TRACKER_CLIENT_TOKEN;
			//debug($this->get('projects'));
			$setting['Setting']['type'] = 'Tracker';
			$setting['Setting']['name'] = 'ACCOUNT_ID';
			$setting['Setting']['value'] = $accountId;
			debug($setting);
			exit;
			
			$Setting->create();
			
			if ($Setting->add($setting)) {
			} else {
				throw new Exception(__('Error creating account id'));
				exit;
			}
		} elseif (!defined()) {
			debug('We cannot automatically get the account id, because there may be more than one that the admin is tied to, and we do not have a client token to double check for.');
			exit;
		}
		
		if (!defined('__TRACKER_CLIENT_USERNAME')) {
			debug('Create client username');
			exit;
		}
		return true;
 	}

}
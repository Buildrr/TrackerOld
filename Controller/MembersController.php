<?php
App::uses('TrackerAppController', 'Tracker.Controller');

class MembersController extends TrackerAppController {

	public $name = 'Members';
    
	public $uses = array('Tracker.Project', 'Users.User');
	
	//public $prefix = __TRACKER_CLIENT_USERNAME; // the username of a user in pt is good for this
	
	//public $clientId = '1383096'; // if you use a single client, what is their person id
	
	public function beforeFilter() {
		$this->set('page_title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
		$this->set('title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
		$this->layout = 'projects';
		$this->Auth->allow(); // hmmm... handle permissions outside of the Privileges plugin
		
		if (!$this->Session->read('Auth.User.id')) {
			if (!in_array($this->request->action, $this->allowedActions)) {
				$this->redirect(array('plugin' => 'users', 'controller' => 'users', 'action' => 'login'));
			}
		}
	}
	
	public function edit($id) {
		$this->request->data = $this->User->find('first', array('contain' => array('UserRole'), 'conditions' => array('User.id' => $id)));
		$this->set('userRoleId', $userRoleId = $this->request->data['UserRole']['id']);
		// we should only add real clients to clients that the Client Bot is associated with
		$this->Project->token = __TRACKER_CLIENT_TOKEN;
		$this->set('projects', $projects = Set::combine($this->Project->get('projects'), '{n}.id', '{n}.name'));
		
		if ($this->Session->read('Auth.User.user_role_id') == 1 && strlen($this->request->data['User']['other']) < 30) {
			$admin = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
			$this->Project->token = $admin['User']['other']; // admin's api key
			$this->set('labels', $labels = Set::combine($this->Project->get('projects/' . $this->request->data['User']['other'] . '/labels'), '{n}.name', '{n}.name'));
		}
	}

/**
 * Connect an admin user account with a pivotal tracker account, by getting the api key. 
 */
	public function connect() {
		
		$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
		
		// if pivotal tracker data is coming in we are getting a new api key
		if (!empty($this->request->data['Tracker']['username']) && !empty($this->request->data['Tracker']['password'])) {
			
			$iam = $this->Project->me($this->request->data['Tracker']['username'], $this->request->data['Tracker']['password']);
			if (!empty($iam['api_token'])) {
				// let's make sure it's a good account first...
				$accounts = $this->Project->get('accounts');
				if (!empty($accounts)) {
					// okay we're good go ahead and save
					$user['User']['other'] = $iam['api_token'];
					unset($user['User']['password']);
					if ($this->User->save($user)) {
						$this->Session->setFlash('API Key found or created.');
						$this->redirect(array('action' => 'bot'));
					} else {
						$this->Session->setFlash('Could not save Pivotal Tracker API Key, please try again. <br> ' . ZuhaSet::keyAsPaths($this->User->invalidFields()));
					}
				} else {
					$this->Session->setFlash('Please use your Pivotal Tracker Admin User to connect with your PROJECT buildrr Admin User.');
				}
			} else {
				// show the error from pivotal tracker
				$this->Session->setFlash($iam['kind']. ' : ' . $iam['error'] . '<br>' . $iam['possible_fix']);
			}
		}
		
	}

	/**
	 * Connect an client catch all user account with a pivotal tracker account, by getting the api key. 
	 */
	public function tie($accountId = null, $personId = null) {
		
		// only the admin user should be on this page (not sure that we check for that)
		$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
		$this->Project->token = $user['User']['other'];
			
			
		if ($this->request->is('post')) {		
			// if pivotal tracker data is coming in we are getting a new api key
			if (!empty($this->request->data['Tracker']['username']) && !empty($this->request->data['Tracker']['password'])) {
				$clientBot = $this->Project->me($this->request->data['Tracker']['username'], $this->request->data['Tracker']['password']);
			} elseif (defined('__TRACKER_CLIENT_TOKEN')) {
				// otherwise we are updating an existing client
				$this->Project->token = __TRACKER_CLIENT_TOKEN;
				$clientBot = $this->Project->get('me');
			}
			
			if (!empty($clientBot['api_token'])) {
				// change the token back to the admin's
				$this->Project->token = $user['User']['other'];
				
				// add the two settings
				$Setting = ClassRegistry::init('Setting');
				$setting['Setting']['type'] = 'Tracker';
				$setting['Setting']['name'] = 'CLIENT_TOKEN';
				$setting['Setting']['value'] = $clientBot['api_token'];
				$Setting->create();
				if ($Setting->add($setting)) {
					// second setting being added
					$setting['Setting']['name'] = 'CLIENT_USERNAME';
					$setting['Setting']['value'] = $clientBot['username'];
					$Setting->create();
					if ($Setting->add($setting)) {
						// second setting being added
						$setting['Setting']['name'] = 'CLIENT_ID';
						$setting['Setting']['value'] = $clientBot['id'];
						$Setting->create();
						if ($Setting->add($setting)) {
							// might as well manipulate the projects while we're here
							if (!empty($this->request->data['Tracker']['projects'])) {
								$count = 0;
								foreach ($this->request->data['Tracker']['projects'] as $projectId) {
									// add the webhooks
									$hook['webhook_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/tracker/projects/hook';
									$hook['webhook_version'] = 'v5';
									$hooks = $this->Project->post('projects/' . $projectId . '/webhooks', json_encode($hook));
									
									// add the member to projects
									$member['person_id'] = $clientBot['id'];
									$member['role'] = 'member';
									$response = $this->Project->post('projects/' . $projectId . '/memberships', json_encode($member));
									$body = json_decode($response->body, true);
									if ($response->code == 200) {
										$count++;
										continue;
									} else {
										$error = true;
										$this->Session->setFlash('Error adding member to projects, please contact an admin. <br>' . $body['general_problem']);
										break;
									}
								}
								if (empty($error)) {
									// Success every where
									$this->Session->setFlash('Successfully updated, and added to ' . $count . ' projects');
									$this->redirect(array('action' => 'view', $this->Session->read('Auth.User.id')));
								}
							} else {
								$this->Session->setFlash('Client Bot account update successful!');
								$this->redirect(array('action' => 'tie', $accountId, $personId));
							}
						} else {
							$this->Session->setFlash('Count not save client bot id, please try again.');
						}
					} else {
						$this->Session->setFlash('Count not save client bot username, please try again.');
					}
				} else {
					$this->Session->setFlash('Count not save API Key, please try again.');
				}
			} else {
				// show the error from pivotal tracker
				$this->Session->setFlash($iam['kind']. ' : ' . $clientBot['error'] . '<br>' . $clientBot['possible_fix']);
			}
		}

		// deal with view variables
		if (empty($accountId)) {
			$this->set('accounts', $accounts = Set::combine($this->Project->get('accounts'), '{n}.id', '{n}.name'));
			if (count($accounts) == 1) {
				// then just redirect
				$this->redirect(array('action' => 'tie', key($accounts)));
			}
		} elseif (empty($personId)) {
			$this->set('persons', $persons = $this->Project->get('accounts/' . $accountId . '/memberships'));
			$this->set('accountId', $accountId);
			$usernames = Set::combine($persons, '{n}.person.username', '{n}.id');
			if (defined('__TRACKER_CLIENT_USERNAME')) {
				$this->redirect(array('action' => 'tie', $accountId, $usernames[__TRACKER_CLIENT_USERNAME]));
			}
		} else {
			$this->set('person', $person = $this->Project->get('accounts/' . $accountId . '/memberships/' . $personId));
			$projects = Set::combine($this->Project->get('projects'), '{n}.id', '{n}.name');
			foreach ($projects as $id => $name) {
				$persons = Set::combine($this->Project->get('projects/' . $id . '/memberships'), '{n}.person.id', '{n}.person.username');
				if ($persons[$personId]) {
					$existingProjects[] = $projects[$id];
					unset($projects[$id]);
				} elseif (!$this->request->is('post')) {
					// select all by default
					$this->request->data['Tracker']['projects'][] = $id;
				}
			}
			$this->set('existingProjects', $existingProjects);
			$this->set('projects', $projects);
			$this->set('personId', $personId);
		}
		
	}
	
	/**
	 * Create the client catch all account
	 */
	public function bot() {
		// only the admin user should be on this page (not sure that we check for that)
		$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
		$this->Project->token = $user['User']['other'];

		// order is important on this
		if (strlen($user['User']['other']) > 10) {
			 // being at least this long (it is 32 chars long) should be adequate to check that we're dealing with an api token and not a project id
			 // debug($this->Project->token);
			 // debug($this->Project->get('me'));
			 // debug($this->Project->get('accounts'));
			 // exit;
			$this->set('accounts', $accounts = Set::combine($this->Project->get('accounts'), '{n}.id', '{n}.name'));
		}
		if (empty($accounts)) {
			$this->Session->setFlash(__('You must use a Pivotal Tracker Admin user to connect to your PROJECT buildrr Admin user account.'));
			$this->redirect(array('action' => 'connect'));
		}

		if ($this->request->is('post')) {
			$accountId = $this->request->data['Tracker']['account'];
			unset($this->request->data['Tracker']['account']);
			$response = $this->Project->post('accounts/' . $accountId . '/memberships', json_encode($this->request->data['Tracker']));
			$body = json_decode($response->body, true);
			$Setting = ClassRegistry::init('Setting');
			$setting['Setting']['type'] = 'Tracker';
			$setting['Setting']['name'] = 'ACCOUNT_ID';
			$setting['Setting']['value'] = $accountId;
			$Setting->create();
			if ($Setting->add($setting)) {
				if ($response->code == 200 || $body['requirement'] == 'Member already exists.') {
					// success, now give that account an api token after confirming the account at tie()			
					$this->Session->setFlash('New account member created, Please check ' . $this->request->data['Tracker']['email'] . ' to confirm the account. Then come back here to finish setting up the client bot.');
					$this->redirect(array('action' => 'tie', $body['account_id'], $body['person']['id']));	
				} else {
					$this->Session->setFlash($body['error'] . '<br>' . $body['general_problem'] . '<br>' . $body['requirement'] . '<br> <small>Did update the account id settings though.</small>');
				}
			} else {
				$this->Session->setFlash('Count not save account id settings, please contact an admin.');
			}
		}
	}

/**
 * Add project names from the Projects.PivotalTracker Model
 */
	public function view($id = null) {
				
		$user = $this->User->find('first', array('conditions' => array('User.id' => $id)));
		
		// if 'other' is empty and they are an admin, then they need to get an api key
		if (empty($user['User']['other']) && $user['User']['user_role_id'] == 1) {
			$this->redirect(array('action' => 'connect'));
		}
		
		// if 'other' is empty and they are an admin, then they need to get an api key
		if (!defined('__TRACKER_CLIENT_TOKEN') && $user['User']['user_role_id'] == 1) {
			$this->redirect(array('action' => 'bot'));
		}
		
		// We are ready to show the user's profile / dashboard
		if ($user['User']['user_role_id'] == 1) {
			$this->Project->token = $user['User']['other']; // this is the admin's token (was commented out, not sure why, but when it is, the clientBotError check doesn't work)
			if (defined('__TRACKER_CLIENT_TOKEN') && defined('__TRACKER_CLIENT_USERNAME')) {
				
				if (defined('__TRACKER_ACCOUNT_ID')) {
					// NOTE : This is not done, and it has backwards compatibility issues because of the 
					// new constant name.  Couldn't find a good way to get that accountId from VEMT automatically
					// have to get this before we update the token to the client's api token
					$this->set('persons', $persons = $this->Project->get('accounts/' . __TRACKER_ACCOUNT_ID . '/memberships'));
					if (strpos(json_encode($persons), __TRACKER_CLIENT_USERNAME) === false) {
						$this->set('clientBotError', $clientBotError = true); // client bot was deleted in pivotal tracker
					}
				}
				
				// check to make sure that the Client Bot is still setup	
				$this->Project->token = __TRACKER_CLIENT_TOKEN;			
				$clientBot = $this->Project->get('me');
				// get client bot projects
				$this->set('projects', $projects = $this->Project->get('projects'));
				$projectIds = Set::extract('/id', $projects);
			} else {
				$this->Project->createConstants(); // backwards compatibility, for when a new constant is introduced
			}
			
			// Admin can edit elements
			App::uses('Webpage', 'Webpages.Model');
			$Webpage = ClassRegistry::init('Webpages.Webpage');
			$Webpage->syncFiles('element');
			$this->set('webpages', $elements = $Webpage->find('all', array('conditions' => array('Webpage.type' => 'element'))));
			
			// Make sure we have a clients user role
			if ($this->User->UserRole->find('count', array('conditions' => array('UserRole.name' => 'clients'))) < 1) {
				$roleData['UserRole']['name'] = 'clients';
				$roleData['UserRole']['is_registerable'] = false;
				if ($this->User->UserRole->save($roleData)) {
					// do nothing, this should be a silent update
				} else {
					$this->Session->setFlash(__('There is a problem with your setup, please contact support with this error code : 70004'));
				}
			}
			
			// Admin can add and edit users
			$this->paginate['order'] = array('User.created' => 'DESC');
			$this->paginate['contain'] = array('UserRole');
			$users = $this->paginate('User');
			for ($i=0; $i < count($users); $i++) {
				if ($users[$i]['UserRole']['name'] == 'clients') {
					$project = $this->Project->get('projects/' . $users[$i]['User']['other']);
					$users[$i]['Project'] = $project;
				}
			}
			$this->set('users', $users);
			$this->set('userRoles', $userRoles = $this->User->UserRole->find('list', array('conditions' => array('UserRole.name NOT' => array('guests', 'admin')))));
			$this->view = 'admin_view';
			
		} else { // regular users\
			$projectIds = !empty($user['User']['other']) ? explode(',', $user['User']['other']) : null;
			if (!empty($projectIds)) {
				foreach ($projectIds as $projectId) {
					$project = $this->Project->get('projects/' . $projectId);
					if ($project['kind'] !== 'error') {
						$projects[] = $project;
					}
					$activities[$projectId] = count($this->Project->get('projects/' . $projectId . '/activity', array('?' => array('limit' => 1000, 'occurred_after' => date('c', strtotime('-30 days'))))));
				}
				$this->set('activities', $activities);
				$this->set('projects', $projects);
			}
		}

		$this->layout = 'projects';
		return $this->set('user', $user);
	}

/**
 * Create a real client
 */
	public function procreate($userRoleId = 6) {
		$this->set('userRoleId', $userRoleId);
		// we should only add real clients to clients that the Client Bot is associated with
		$this->Project->token = __TRACKER_CLIENT_TOKEN;
		$this->set('projects', $projects = Set::combine($this->Project->get('projects'), '{n}.id', '{n}.name'));
	}
}

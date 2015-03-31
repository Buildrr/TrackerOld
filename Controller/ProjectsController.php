<?php
class ProjectsController extends TrackerAppController {

	public $name = 'Projects';
    
	public $uses = array('Tracker.Project', 'Users.User');
	
	public $prefix = __TRACKER_CLIENT_USERNAME; // the username of a user in pt is good for this
	
	public $clientId = __TRACKER_CLIENT_ID; // if you use a single client, what is their person id
	
	public $allowedActions = array('hook');
	
	
	public function __construct($id = false, $table = null, $ds = null) {
		if (!defined('__TRACKER_CLIENT_USERNAME')) {
			debug('must have a client username setup');
			exit;
			// note we could default this to pub for easier setup purposes
		}
    	parent::__construct($id, $table, $ds);
		$this->prefix = '@' . __TRACKER_CLIENT_USERNAME;
	}
	
	public function beforeFilter() {
		$this->set('page_title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
		$this->set('title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
		$this->layout = 'projects';
		$this->set('clientId', $this->clientId);
		
		 // hmmm... handle permissions outside of the Privileges plugin
		$this->Auth->allow();
		if (!$this->Session->read('Auth.User.id')) {
			if (!in_array($this->request->action, $this->allowedActions)) {
				$this->redirect(array('plugin' => 'users', 'controller' => 'users', 'action' => 'login'));
			}
		}
	}

/**
 * index method
 */
	public function index() {
		// this will be fore the view() function		$endpoint = 'projects/' . $this->projectId . '/stories';
		$this->set('projects', $this->Project->get());
		$this->set('page_title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
		$this->set('title_for_layout', __SYSTEM_SITE_NAME . ' Project Success Manager');
	}

/**
 * view method
 */
	public function view($projectId) {
		$this->set('project', $project = $this->Project->get('projects/' . $projectId));
		
		$options = $this->_userOptions();
		
		if (!empty($options['with_label'])) {
			// admin has placed a filter for labels on which stories a user can see
			$stories = $this->Project->get('projects/' . $projectId . '/stories', array('?' => array('with_label' => $options['with_label'])));
			$activities = array();
			foreach ($stories as $story) {
				$activities = array_merge($activities, $this->Project->get('projects/' . $projectId . '/stories/' . $story['id'] . '/activity'));
			}
			$activities = array_reverse($activities);
		} else {
			// default shows all activities for this project
			$activities = $this->Project->get('projects/' . $projectId . '/activity', array('?' => array('limit' => 50)));
		}
		$this->set(compact('activities')); 
	}
/**
 * message method
 * 
 */
	public function message($projectId, $storyId) {
		$this->set('project', $project = $this->Project->get('projects/' . $projectId));
		$endpoint = 'projects/' . $projectId . '/stories/' . $storyId;
		$story = $this->Project->get($endpoint);
		$story['comments'] = array_reverse($this->comments($this->Project->get('projects/' . $story['project_id'] . '/stories/' . $story['id'] . '/comments', array('?' => array('fields' => 'story_id,text,person_id,created_at,updated_at,file_attachments')))));
		if (!empty($story['error'])) {
			$this->Session->setFlash('Scope Item No Longer Exists<br><small>Additional error detail : ' . $story['error'] . '</small>');
			$this->redirect($this->referer());
		}
		$this->set('story', $story);
	}

/**
 * messages method
 * 
 */
	public function messages($projectId) {
		
		$this->set('project', $project = $this->Project->get('projects/' . $projectId));
		$endpoint = 'projects/' . $projectId . '/stories';
		$stories = $this->Project->get($endpoint);
		$options = $this->_userOptions();
		
		// done
		$this->set('accepted', $accepted = $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'accepted'), $options))));
		// needs feedback
		$this->set('delivered', $delivered = $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'delivered'), $options))));
		// icebox
		$this->set('unscheduled', $unscheduled = $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'unscheduled'), $options))));
		
		// combine all others, finished;started;rejected;planned;unstarted
		$stories = $this->Project->get('projects/' . $projectId . '/stories', array('?' => array('with_state' => 'finished')));
		$stories = array_merge($stories, $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'started'), $options))));
		$stories = array_merge($stories, $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'rejected'), $options))));
		$stories = array_merge($stories, $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'planned'), $options))));
		$stories = array_merge($stories, $this->Project->get('projects/' . $projectId . '/stories', array('?' => array_merge(array('with_state' => 'unstarted'), $options))));
		
		$this->set('stories', $stories);
		
		// $this->set('project', $project = $this->Project->get('projects/' . $projectId));
		// $endpoint = 'projects/' . $projectId . '/stories';
		// $stories = $this->Project->get($endpoint, array('?' => array('filter' => '@public')));
		// for ($i=0; $i < count($stories); $i++) {
			// $comments = $this->Project->get('projects/' . $stories[$i]['project_id'] . '/stories/' . $stories[$i]['id'] . '/comments', array('?' => array('fields' => 'story_id,text,person_id,created_at,updated_at,file_attachments')));
			// $comments = Set::combine($comments, '{n}.updated_at', '{n}.text');
			// $stories[$i] = array_merge($stories[$i], $comments);
		// }
		// $this->set('stories', $stories);
		// debug($stories);
		// exit;
	}

	protected function _userOptions() {
		$options = array();
		$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
		
		if (!empty($user['User']['about_me'])) { // label filtering
			$options['with_label'] = $user['User']['about_me'];
		}
		return $options;
	}

/**
 * messages method
 * 
 */
	public function files($projectId) {
		
		$this->set('project', $project = $this->Project->get('projects/' . $projectId));
		// $endpoint = 'projects/' . $projectId . '/stories';
		// $stories = $this->Project->get($endpoint);		
		$endpoint = 'projects/' . $projectId . '/search'; // trying to make the page faster
		$stories = $this->Project->get($endpoint, array('?' => array('query' => $this->prefix)));
		$stories = $stories['stories']['stories'];
		
		$attachments = array();
		for ($i=0; $i < count($stories); $i++) {
			$stories[$i]['comments'] = $this->comments($this->Project->get('projects/' . $stories[$i]['project_id'] . '/stories/' . $stories[$i]['id'] . '/comments', array('?' => array('fields' => 'story_id,text,person_id,created_at,updated_at,file_attachments'))));
			foreach ($stories[$i]['comments'] as $comment) {
				$files = Set::extract('/file_attachments', $comment);
				$attachments = !empty($files[0]['file_attachments']) ? array_merge($attachments, $files) : $attachments;
			}
		}
		$this->set('stories', $stories);
		$this->set('attachments', array_reverse($attachments));
	}

/**
 * 
 */
 	public function download($fileAttachmentId = null) {
 		$file = $this->Project->download($fileAttachmentId);
		$fileName = str_replace(array('attachment; filename=', '"'), array(''), $file->getHeader('Content-Disposition'));
		$this->response->body($file);
		$this->response->download($fileName);
		return $this->response;
 	}
 	
/**
 * Add a story to a project
 */
	public function story() {
		if ($this->request->is('post') && defined('__TRACKER_ALLOW_ADD') && __TRACKER_ALLOW_ADD == true) {
			$projectId = $this->request->data['Tracker']['project_id'];
			$this->request->data['Tracker']['story_type'] = 'feature';
			$this->request->data['Tracker']['current_state'] = 'unscheduled';
			$this->request->data['Tracker']['requested_by_id'] = intval(__TRACKER_CLIENT_ID);
			$this->Project->token = __TRACKER_CLIENT_TOKEN;
			$response = $this->Project->post('projects/' . $projectId . '/stories', json_encode($this->request->data['Tracker']));
			if ($response->code == 200) {
				$body = json_decode($response->body, true);
				$this->Session->setFlash(__('Success, "%s" added.', $body['name']));
			} else {
				$this->Session->setFlash($responseBody['error'] . ' ' . $responseBody['general_problem']);
			}
		}
		$this->redirect($this->referer());
	}

/**
 * Comment method
 * 
 * Post a comment to pivotal tracker
 */
	public function comment() {
		// adding a comment (with post)
		if ($this->request->is('post')) {
			$data['text'] = $this->prefix . ' ' . $this->Session->read('Auth.User.email') . ' ' . $this->request->data['Comment']['text'];
			if (!empty($this->request->data['Comment']['file']['tmp_name'])) {
				$upload = $this->Project->upload($this->request->data['Comment']);
				$data['file_attachments'][] = json_decode($upload, true);
			}
			$response = $this->Project->post('projects/' . $this->request->data['Comment']['project_id'] . '/stories/' . $this->request->data['Comment']['story_id'] . '/comments', json_encode($data));
			$responseBody = json_decode($response->body, true);
			if ($responseBody['kind'] == 'error') {
				$this->Session->setFlash($responseBody['error'] . ' ' . $responseBody['general_problem']);
				$this->__sendMail('richard@buildrr.com', 'Comment Posting ERROR (Line 130 ProjectsController.php)', $message . '<br><br><pre>' . print_r(get_defined_vars(), true) . '</pre>');
			} // else {
				// $message .= 'http://' . $_SERVER['HTTP_HOST'] . '/tracker/projects/message/' . $this->request->data['Comment']['project_id'] . '/' . $this->request->data['Comment']['story_id'];
				// $this->__sendMail('richard@buildrr.com', 'Comment Submitted', $message);
				// $this->Session->setFlash('Comment submitted');
			// }
		}
		// updating a comment with put
		if ($this->request->is('put')) {
			$data['text'] = $this->prefix . ' ' . $this->request->data['Comment']['text'];
			$response = $this->Project->put('projects/' . $this->request->data['Comment']['project_id'] . '/stories/' . $this->request->data['Comment']['story_id'] . '/comments/' . $this->request->data['Comment']['id'], json_encode($data));
			$responseBody = json_decode($response->body, true);
			if ($responseBody['kind'] == 'error') {
				$this->Session->setFlash($responseBody['error'] . ' ' . $responseBody['general_problem']);
				$this->__sendMail('richard@buildrr.com', 'Comment Posting ERROR (Line 130 ProjectsController.php)', $message . '<br><br><pre>' . print_r(get_defined_vars(), true) . '</pre>');
			} // else {
				// $message .= 'http://' . $_SERVER['HTTP_HOST'] . '/projects/Project_tracker/message/' . $this->request->data['Comment']['project_id'] . '/' . $this->request->data['Comment']['story_id'];
				// $this->__sendMail('richard@buildrr.com', 'Comment Updated', $message);
				// $this->Session->setFlash('Comment updated');
			// }
		}
		$this->redirect($this->referer());
	}

/**
 * Filters comments so that only public ones appear
 */
	protected function comments($comments = array()) {
		$return = array(); 
		
		for ($i=0; $i < count($comments); $i++) {
			if (strpos($comments[$i]['text'], $this->prefix) || strpos($comments[$i]['text'], $this->prefix) === 0) {
				$comments[$i]['text'] = trim(str_replace(array($this->prefix), array(''), $comments[$i]['text']));
				// see if we can attach this comment to an actual user in this system
				$words = explode(' ', $comments[$i]['text']);
				foreach ($words as $word) {
					if ($email = filter_var($word, FILTER_VALIDATE_EMAIL)) {
						$user = $this->User->find('first', array('conditions' => array('User.email' => $email)));
						if (!empty($user)) {
							// add the user to the comment data
							$comments[$i]['text'] = trim(str_replace($email, '', $comments[$i]['text']));
							$comments[$i]['User'] = $user['User'];
						}
					}
				}
				$return[] = $comments[$i];
			}
		}
		return $return;
	}
	
/**
 * Hook method
 * 
 * The callback from pivotal tracker to our system when an activity has happened
 */
	public function hook() {
		// example post
		// debug(json_decode('{"kind":"comment_create_activity","guid":"1117616_224","project_version":224,"message":"Richard Kersey added comment: \"this is a test\"","highlight":"added comment:","changes":[{"kind":"comment","change_type":"create","id":73778144,"new_values":{"id":73778144,"story_id":74572998,"text":"this is a test","person_id":1160310,"created_at":1404924524000,"updated_at":1404924524000,"file_attachment_ids":[],"google_attachment_ids":[],"file_attachments":[],"google_attachments":[]}},{"kind":"story","change_type":"update","id":74572998,"original_values":{"updated_at":1404924381000},"new_values":{"updated_at":1404924524000},"name":"Another story, maybe this one is in the backlog.","story_type":"feature"}],"primary_resources":[{"kind":"story","id":74572998,"name":"Another story, maybe this one is in the backlog.","story_type":"feature","url":"https:\/\/www.Pivotal.com\/story\/show\/74572998"}],"project":{"kind":"project","id":1117616,"name":"Rich Tester"},"performed_by":{"kind":"person","id":1160310,"name":"Richard Kersey","initials":"RK"},"occurred_at":1404924524000}', true));
		// exit;
		if ($this->request->data['kind'] === 'comment_create_activity') {
			$projectId = $this->request->data['project']['id'];
			$storyId = $this->request->data['changes'][0]['new_values']['story_id'];
			$projectName = $this->request->data['project']['name'];
			$authorId = $this->request->data['changes'][0]['new_values']['person_id'];
			$text = $this->request->data['changes'][0]['new_values']['text'];
			
			if ( (strpos($text, $this->prefix) === 0 || strpos($text, $this->prefix)) && $authorId != $this->clientId) {
				// get the email using the $projectId
				$users = $this->User->find('all', array('conditions' => array("User.other LIKE '%$projectId%'")));
				foreach ($users as $user) {
					$message = 'Hi ' . $user['User']['first_name'] . ', <br><br>Updates have been made to your project (we may require your response before being able to continue work), please review at this address <br><br>';
					$message .= 'http://' . $_SERVER['HTTP_HOST'] . '/tracker/projects/message/' . $projectId . '/' . $storyId;
					$projects = explode(',', $user['User']['other']);
					if (in_array($projectId, $projects)) {
						$this->__sendMail($user['User']['email'], $projectName . ' Update ', $message);
					} else {
						$this->__sendMail('richard@buildrr.com', 'User Found - EMAIL NOT SENT TO CLIENT', $message . '<br><pre>' . print_r(get_defined_vars(), true)) . '</pre>';
					}
				}
				if (empty($users)) {
					$this->__sendMail('richard@buildrr.com', 'No Users Found', $message . '<br><pre>' . print_r(get_defined_vars(), true)) . '</pre>';
				}
				
			}
		}
		$this->render(false);
	}
}

<div id="userRegister" class="user form">
	<h2>Create a Client in PROJECT buildrr</h2>
	<p class="text-muted">This user does not need an account with Pivotal Tracker.</p>
	<hr>
	<?php echo $this->Form->create('User', array('url' => array('plugin' => 'users', 'controller' => 'users', 'action' => 'procreate'), 'type' => 'file')); ?>
	<?php echo $this->Form->input('Contact.id', array('type' => 'hidden')); ?>  
	<fieldset>
		<?php echo $this->Form->input('Override.redirect', array('type' => 'hidden', 'value' => '/tracker/members/view/' . $this->Session->read('Auth.User.id'))); ?>
		<?php echo !empty($userRoleId) ? $this->Form->hidden('User.user_role_id', array('value' => $userRoleId)) : $this->Form->input('User.user_role_id'); ?>
		<?php echo $this->Form->input('User.full_name', array('label' => 'Full Name')); ?>
		<?php echo $this->Form->input('User.username', array('label' => 'Email (will also be their username)')); ?>
		
		<?php $label = $userRoleId == 6 ? 'Project to Access' : 'Pivotal Tracker Api Key'; ?>
		<?php echo $this->Form->input('User.other', array('label' => $label, 'options' => $projects)); ?>
		
		<?php echo $this->Form->input('User.notify', array('label' => 'Notify User? (Should we send an email with password instructions?)', 'type' => 'checkbox')); ?>

	</fieldset>
	<?php echo $this->Form->end('Submit'); ?>
</div>
<?php
// set the contextual menu items
$this->set('context_menu', array('menus' => array( array(
			'heading' => 'Users',
			'items' => array(
				$this->Html->link(__('Login'), array(
					'plugin' => 'users',
					'controller' => 'users',
					'action' => 'login'
				)),
				$this->Html->link(__('Logout'), array(
					'plugin' => 'users',
					'controller' => 'users',
					'action' => 'logout'
				)),
			)
		))));

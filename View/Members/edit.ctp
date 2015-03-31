<div id="userRegister" class="user form">
	<h2>Create a Client in PROJECT buildrr</h2>
	<p class="text-muted">This user does <strong>not</strong> need an account with Pivotal Tracker.</p>
	<hr>
	<div class="row">
		<div class="col-md-2">
			<div id="userEditThumb">
			    <?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'thumbSize' => 'large', 'thumbLink' => '', 'thumbLinkOptions' => array('style' => 'color: #333;font-size: 10px;'), 'defaultImage' => '/img/icon.client.png', 'model' => 'User', 'foreignKey' => $this->request->data['User']['id'])); ?>
				<?php echo $this->Html->link('Change', '/', array('id' => 'userEditThumbLink', 'class' => 'toggleClick', 'data-target' => '#GalleryEditForm')); ?>
			</div>
			<?php echo $this->Form->create('Gallery', array('url' => '/galleries/galleries/mythumb', 'enctype' => 'multipart/form-data')); ?>
			<?php echo $this->Form->input('GalleryImage.is_thumb', array('type' => 'hidden', 'value' => 1)); ?>
			<?php echo $this->Form->input('GalleryImage.filename', array('label' => 'Choose image', 'type' => 'file')); ?>
			<?php echo $this->Form->input('Gallery.model', array('type' => 'hidden', 'value' => 'User')); ?>
			<?php echo $this->Form->input('Gallery.foreign_key', array('type' => 'hidden', 'value' => $this->request->data['User']['id'])); ?>
			<?php echo $this->Form->end('Upload'); ?>
		</div>
		<div class="col-md-10">
			<?php echo $this->Form->create('User', array('url' => array('plugin' => 'users', 'controller' => 'users', 'action' => 'edit'), 'type' => 'file')); ?>
			<?php echo $this->Form->input('User.id', array('type' => 'hidden')); ?>  
			<fieldset>
				<?php echo $this->Form->input('Override.redirect', array('type' => 'hidden', 'value' => '/tracker/members/view/' . $this->Session->read('Auth.User.id'))); ?>
				<?php echo !empty($userRoleId) ? $this->Form->hidden('User.user_role_id', array('value' => $userRoleId)) : $this->Form->input('User.user_role_id'); ?>
				<?php echo $this->Form->input('User.full_name', array('label' => 'Full Name')); ?>
				<?php echo $this->Form->input('User.username', array('label' => 'Email (will also be their username)')); ?>
				
				<?php $label = $userRoleId == 6 ? 'Project to Access' : 'Pivotal Tracker Api Key'; ?>
				<?php $type = $userRoleId == 6 ? 'select' : 'text'; ?>
				<?php echo $this->Form->input('User.other', array('label' => $label, 'type' => $type, 'options' => $projects, 'empty' => '-- Select Project --')); ?>
				
				<?php if (!empty($labels)) : ?>
					<?php echo $this->Form->input('User.about_me', array('label' => 'Limit this user\'s view to Pivotal Tracker Stories with this label.', 'type' => $type, 'options' => $labels, 'empty' => '-- Select Label --')); ?>
				<?php endif; ?>
				
			</fieldset>
			<?php echo $this->Form->end('Submit'); ?>
		</div>
	</div>
	
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

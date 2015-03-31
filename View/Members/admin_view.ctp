<div class="users view row">
	<div class="col-md-12">
	    <h3>Welcome <?php echo $user['User']['first_name'] . ' ' . $user['User']['last_name'] ?></h3>
	    <h4><small>Your PROJECT buildrr Admin.</small></h4>
	    <hr>
	    	<div class="row">
	    		<div class="col-md-2">
	    			<h4>Client Bot Projects</h4>
	    			<p><small class="text-muted">Only projects that your Client Bot has access to can be associated with actual clients.</small></p>
	    			<?php if (!empty($projects)) : ?>
	    				<ul class="list-group">
						<?php foreach ($projects as $project) : ?>
							<li class="list-group-item"><?php echo $this->Html->link($project['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'view', $project['id'])); ?></li>
						<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<div class="row">
							<div class="col-md-12">
								Client Bot has no projects assigned. 
							</div>
						</div>
					<?php endif; ?>
					<hr>
					<p class="text-center"><a href="/tracker/members/tie" class="btn btn-default btn-xs">Manage Client Bot Projects</a></p>
	    		</div>
	    		<div class="col-md-8">
	    			<h4>Users</h4>
	    			<?php if (count($users) < 2) : ?>
	    				<div class="well text-center">
	    					<p>Looks like you don't have any real clients added, <?php echo $this->Html->link(__('Create Your First Real Client'), array('plugin' => 'tracker', 'controller' => 'members', 'action' => 'procreate', $userRoleId), array('class' => 'btn-block btn btn-lg btn-success')); ?></p>
	    				</div>
	    			<?php endif; ?>
	    			<table>
			            <thead>
			            	<th><?php echo $this->Paginator->sort('last_name', 'Name'); ?></th>
			            	<th><?php echo $this->Paginator->sort('last_login', 'Last Login'); ?></th>
			            	<th>Project</th>
			            	<th>Actions</th>
			            </thead>
			            <?php foreach ($users as $user) : ?>
			                <tr>
			                    <td>
			                    	<div style="width: 24px" class="pull-left">
			                    		<?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'thumbSize' => 'large', 'thumbLink' => '', 'defaultImage' => '/img/icon.client.png', 'model' => 'User', 'foreignKey' => $user['User']['id'])); ?>
			                    	</div>
			                    	
			                    	&nbsp;<?php echo $this->Html->link(__('%s, %s', $user['User']['last_name'], $user['User']['first_name']), array('action' => 'view', $user['User']['id'])); ?>
			                    </td>
			                    <td>
			                    	<?php echo !empty($user['User']['last_login']) ? $this->Time->timeagoinwords($user['User']['last_login']) : 'Never logged in'; ?>
			                    </td>
			                    <td><?php echo $user['Project']['kind'] == 'project' ? $this->Html->link($user['Project']['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'view', $user['Project']['id'])) : 'unassigned'; ?></td>
			                    <td>
			                        <a class="btn btn-success btn-xs" href="<?php echo $this->Html->url(array('admin' => false, 'action' => 'view', $user['User']['id'])); ?>">
			                            <i class="glyphicon glyphicon-zoom-in"></i>
			                            View
			                        </a>
			                        <a class="btn btn-info btn-xs" href="<?php echo $this->Html->url(array('action' => 'edit', $user['User']['id'])); ?>">
			                            <i class="glyphicon glyphicon-edit"></i>
			                            Edit
			                        </a>
									<?php echo $user['User']['id'] != $this->Session->read('Auth.User.id') ? $this->Html->link(
											'<i class="glyphicon glyphicon-trash"></i> Delete',
											array('action' => 'delete', $user['User']['id']),
											array('class' => 'btn btn-danger btn-xs', 'escape' => false),
											sprintf('Are you sure you want to delete %s?', $user['User']['full_name'])
									) : null; ?>
			                    </td>
			                </tr>
			            <?php endforeach; ?>
			        </table>
			        <?php echo $this->element('paging'); ?>
	    		</div>
	    		<div class="col-md-2">
	    			<h4>Quick Links</h4>
	    			
			        <div class="list-group">
			        	<?php foreach ($userRoles as $userRoleId => $userRole) : ?>
			            	<?php echo $this->Html->link(__('Add %s', Inflector::humanize(Inflector::singularize($userRole))), array('plugin' => 'tracker', 'controller' => 'members', 'action' => 'procreate', $userRoleId), array('class' => 'list-group-item')); ?>
			            <?php endforeach; ?>
			            <?php echo $this->Html->link('Create a Project', 'https://www.pivotaltracker.com/projects', array('target' => '_blank', 'class' => 'list-group-item')); ?>
			        </div>
			        
			        
	    			<h4>Theme Editor</h4>
	    			<p class="text-muted"><small>Control your user's experience, and add useful resources for your clients. You might even see that every time a client logs into your system there is the opportunity to show the latest news, and additional offers and services from your company.</small></p>
					
	    			<?php foreach ($webpages as $webpage) : ?>
						<div class="list-group-item clearfix">
							<div class="media">
								<div class="media-body">
									<?php echo $webpage['Webpage']['name']; ?><br />					
									<?php echo strpos($webpage['Webpage']['content'], '<?php') !== false ? null : $this->Html->link('<i class="glyphicon glyphicon-edit"></i> WYSIWYG', array('admin' => true, 'plugin' => 'webpages', 'controller' => 'webpages', 'action' => 'edit', $webpage['Webpage']['id']), array('class' => 'btn btn-default btn-success btn-xs', 'escape' => false)); ?>
									<?php echo $this->Html->link('<i class="glyphicon glyphicon-edit"></i> HTML', array('admin' => true, 'plugin' => 'webpages', 'controller' => 'webpages', 'action' => 'edit', $webpage['Webpage']['id'], '?' => array('advanced' => 1)), array('class' => 'btn btn-default btn-warning btn-xs', 'escape' => false)); ?>
									
								</div>
							</div>
						</div>
					<?php endforeach; ?>
	    			
	    			<h4>Your Logo</h4>
	    			<div id="userEditThumb">
					    <?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'thumbSize' => 'large', 'thumbLink' => '', 'thumbLinkOptions' => array('style' => 'color: #333;font-size: 10px;'), 'defaultImage' => '/img/icon.buildrr.png', 'model' => 'Tracker', 'foreignKey' => 1)); ?>
						<?php echo $this->Html->link('Change', '/', array('id' => 'userEditThumbLink', 'class' => 'toggleClick', 'data-target' => '#GalleryViewForm')); ?>
					</div>
					<?php echo $this->Form->create('Gallery', array('url' => '/galleries/galleries/mythumb', 'enctype' => 'multipart/form-data')); ?>
					<?php echo $this->Form->input('GalleryImage.is_thumb', array('type' => 'hidden', 'value' => 1)); ?>
					<?php echo $this->Form->input('GalleryImage.filename', array('label' => 'Choose image', 'type' => 'file')); ?>
					<?php echo $this->Form->input('Gallery.model', array('type' => 'hidden', 'value' => 'Tracker')); ?>
					<?php echo $this->Form->input('Gallery.foreign_key', array('type' => 'hidden', 'value' => 1)); ?>
					<?php echo $this->Form->end('Upload'); ?>
					
					<hr>
					<h4>Optional Settings</h4>
					
					
					<?php if (defined('__TRACKER_ALLOW_ADD') && __TRACKER_ALLOW_ADD == true) : ?>
						<?php echo $this->Form->create('Setting', array('url' => array('admin' => true, 'plugin' => false, 'controller' => 'settings', 'action' => 'add'))); ?>
						<?php echo $this->Form->input('Setting.type', array('value' => 'Tracker', 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Setting.name', array('value' => 'ALLOW_ADD', 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Setting.value', array('value' => 0, 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Override.redirect', array('value' => $this->request->here, 'type' => 'hidden')); ?>
						<p><small class="text-muted">Your user's can currently create scope items, you can turn this off here.</small></p>
						<?php echo $this->Form->end('Turn Off'); ?>
					<?php else : ?>
						<?php echo $this->Form->create('Setting', array('url' => array('admin' => true, 'plugin' => false, 'controller' => 'settings', 'action' => 'add'))); ?>
						<?php echo $this->Form->input('Setting.type', array('value' => 'Tracker', 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Setting.name', array('value' => 'ALLOW_ADD', 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Setting.value', array('value' => 1, 'type' => 'hidden')); ?>
						<?php echo $this->Form->input('Override.redirect', array('value' => $this->request->here, 'type' => 'hidden')); ?>
						<p><small class="text-muted">Would you like to allow your user's to create scope items? (They will be put into the Pivotal Tracker Icebox when created, and be listed as a "Future Scope Item" in PROJECT buildrr)</small></p>
						<?php echo $this->Form->end('Turn On'); ?>
					<?php endif; ?>
	    		</div>
	    	</div>
	</div>
</div>

<?php 
// set the contextual breadcrumb items
// $this->set('context_crumbs', array('crumbs' => array(
	// $this->Html->link(__('Admin Dashboard'), '/admin'),
	// $this->Html->link('User Dashboard', array('action' => 'dashboard')),
	// $this->request->data['User']['full_name']
// )));
// set the contextual menu items
// $this->set('context_menu', array('menus' => array(
	// array(
		// 'heading' => 'Users',
		// 'items' => array(
			// $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
			// $this->Html->link(__('Delete'), array('action' => 'delete', $user['User']['id']), array(), 'Are you sure you want to delete ' . $user['User']['full_name']),
			// ),
		// ),
	// )));

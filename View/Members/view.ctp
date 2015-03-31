<div class="users view">
	<div class="row">
		<div class="col-md-2">
			<div id="userEditThumb">
			    <?php echo $this->element('Galleries.thumb', array('thumbClass' => 'img-responsive', 'thumbSize' => 'large', 'thumbLink' => '', 'thumbLinkOptions' => array('style' => 'color: #333;font-size: 10px;'), 'defaultImage' => '/img/icon.client.png', 'model' => 'User', 'foreignKey' => $user['User']['id'])); ?>
				<?php echo $this->Html->link('Change', '/', array('id' => 'userEditThumbLink', 'class' => 'toggleClick', 'data-target' => '#GalleryViewForm')); ?>
			</div>
			<?php echo $this->Form->create('Gallery', array('url' => '/galleries/galleries/mythumb', 'enctype' => 'multipart/form-data')); ?>
			<?php echo $this->Form->input('GalleryImage.is_thumb', array('type' => 'hidden', 'value' => 1)); ?>
			<?php echo $this->Form->input('GalleryImage.filename', array('label' => 'Choose image', 'type' => 'file')); ?>
			<?php echo $this->Form->input('Gallery.model', array('type' => 'hidden', 'value' => 'User')); ?>
			<?php echo $this->Form->input('Gallery.foreign_key', array('type' => 'hidden', 'value' => $user['User']['id'])); ?>
			<?php echo $this->Form->end('Upload'); ?>
		</div>
		<div class="col-md-10">
		    <h2>Welcome <?php echo $user['User']['first_name']; ?>,</h2>
		    <div class="row">
			    <hr>
				<div class="col-md-3">
					<h4>Your Project(s)</h4>
					<?php if (!empty($projects)) : ?>
						<?php foreach ($projects as $project) : ?>
						<div class="row">
							<a href="/tracker/projects/view/<?php echo $project['id']; ?>">
							<div class="col-md-12">
								<strong><?php echo $project['name']; ?> Project</strong> Activities <br>
								<strong><span style="font-size: 80px"><?php echo $activities[$project['id']]; ?></span></strong><br>
								<span class="glyphicon glyphicon-star">&nbsp;</span>
								<span class="glyphicon glyphicon-star">&nbsp;</span>
								<span class="glyphicon glyphicon-star">&nbsp;</span>
								<span class="glyphicon glyphicon-star">&nbsp;</span>
								<span class="glyphicon glyphicon-star">&nbsp;</span>
							</div>
							</a>
						</div>
						<hr />
						<?php endforeach; ?>
					<?php else : ?>
						<div class="row">
							<div class="col-md-12">
								No projects assigned yet, contact the admin to request access.
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-md-4">
					<?php echo $this->element('asset-space-01'); ?>
				</div>
				<div class="col-md-5">
					<?php echo $this->element('asset-space-02'); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (CakeSession::read('Auth.User.user_role_id') == 6) : ?>
<style>
	.navbar-fixed-bottom  {
		display: none;
	}
</style>
<?php endif; ?>

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

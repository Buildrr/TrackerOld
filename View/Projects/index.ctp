<h1>Projects</h1>
<div class="list-group">
	<?php foreach ($projects as $project) : ?>
	<div class="list-group-item">
		<?php echo $this->Html->link($project['name'], array('plugin' => 'tracker', 'controller' => 'projects', 'action' => 'view', $project['id'])); ?>
	</div>
	<?php endforeach; ?>
</div>


<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Go To',
		'items' => array(
			$this->Html->link(__('Pivotal Tracker'), 'https://www.pivotaltracker.com/'),
			)
		)
	)));
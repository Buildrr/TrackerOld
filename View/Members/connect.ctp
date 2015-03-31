<div class="row">
		<h3 class="text-center">Let's Connect to <img src="//project.buildrr.com/img/logo-pt.png" alt="Pivotal Tracker" style="width: 230px;" class="image-responsive"></h3>
	<div class="col-sm-4 col-sm-offset-2">
		
		<p class="well">By entering your Pivotal Tracker credentials we can get an API Key, and you will see the Pivotal Tracker projects that this username has access to.  This user should be the main, admin or project manager who will access this system and has permission to add Members in the Pivotal Tracker system.</p>
	</div>
	
	<div class="col-sm-4">
		<?php echo $this->Form->create(); ?>
		<?php echo $this->Form->input('Tracker.username', array('label' => 'Your Pivotal Tracker Email <small>(* this is not stored)</small>')); ?>
		<?php echo $this->Form->input('Tracker.password', array('label' => 'Your Pivotal Tracker Password <small>(* this is not stored)</small>', 'type' => 'password')); ?>
		<?php echo $this->Form->end('Submit'); ?>
	</div>
</div>
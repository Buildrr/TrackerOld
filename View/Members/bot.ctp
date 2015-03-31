<div class="row">
		<h3 class="text-center">Now we need to reserve an Account Member in <img src="//project.buildrr.com/img/logo-pt.png" alt="Pivotal Tracker" style="width: 200px;" class="image-responsive"></h3>
	<div class="col-sm-4 col-sm-offset-2">
		<div class="well">
			<p>We need one user in Pivotal Tracker to act as the catch all for your client accounts on PROJECT buildrr. We call this the <strong>"Client Bot"</strong>. </p>
			<p>Don't worry, even though there is only one client on the Pivotal Tracker side, we will still control which messages your client sees when they login to PROJECT buildrr by tying their PROJECT buildrr user account with a specific project. </p>
			
			<p><strong>How to Use Tip...</strong> If your team is using Pivotal Tracker for an internal discussion, just make comments as normal.  As soon as you want to make a comment that the client should see, just prefix it with @[client-bot-username].  <i>Changing "client-bot-username" to the actual username you will receive on the next page.</i></p>
		</div>
	</div>
	
	<div class="col-sm-4">
		<?php echo $this->Form->create(); ?>
		<?php echo count($accounts) > 1 ? $this->Form->input('Tracker.account', array('label' => 'Pivotal Tracker Account')) : $this->Form->input('Tracker.account', array('type' => 'text', 'readonly' => true, 'value' => $accounts[key($accounts)], 'label' => 'Pivotal Tracker Account')) . $this->Form->input('Tracker.account', array('type' => 'hidden', 'value' => key($accounts))); ?>
		<?php echo $this->Form->input('Tracker.name', array('label' => 'Bot Name', 'readonly' => true, 'value' => 'Client Bot')); ?>
		<?php echo $this->Form->input('Tracker.name', array('type' => 'hidden', 'value' => 'Client Bot')); ?>
		<?php echo $this->Form->input('Tracker.initials', array('label' => 'Bot Initials', 'readonly' => true, 'value' => 'CB', 'required' => 'required')); ?>
		<?php echo $this->Form->input('Tracker.initials', array('type' => 'hidden', 'value' => 'CB')); ?>
		<?php echo $this->Form->input('Tracker.email', array('label' => 'Email Address (Must be an email that you can check.)', 'after' => '<small>You will need to check this email and confirm the account with Pivotal Tracker one time only.', 'type' => 'email', 'required' => 'required')); ?>
		<?php echo $this->Form->end('Submit'); ?>
	</div>
</div>
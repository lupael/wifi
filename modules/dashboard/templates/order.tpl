<div class="process-order">
	{if isset($error)}
		<div class="alert alert-danger" role="alert"><i class="far fa-times-circle"></i>{$error}</div>
	{/if}
	{if isset($success)}
		<div class="alert alert-success" role="alert"><i class="fas fa-spinner fa-pulse"></i>{$success}</div>
	{/if}
</div>
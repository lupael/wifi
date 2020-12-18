<div class="content"><div class="contentsub register">
	<div class="container form form2"><div class="space3">
			{if isset($error)}
				<div class="alert alert-danger" role="alert"><i class="{$icon}"></i>{$error}{if $icon !== 'fas fa-spinner fa-pulse'}<br /><br /><a href="/{$lang}/">{$translate.37} &raquo;</a>{/if}</div>
			{/if}
			{if isset($success)}
				<div class="alert alert-success" role="alert"><i class="{$icon}"></i>{$success}<br /><br /><a href="/{$lang}/">{$translate.37} &raquo;</a></div>
			{/if}
	</div></div>		
</div></div>
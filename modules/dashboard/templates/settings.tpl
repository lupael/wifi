<form autocomplete="off" method="post" action="">
	{if isset($error) && !empty($error)}
		<div class="alert alert-danger" role="alert">{eval echo implode("<br />", $error);}</div>
	{/if}
	{if isset($success)}
		<div class="alert alert-success" role="alert">{$success}</div>
	{/if}
	<div class="form-group row">
		<label class="col-lg-3 col-form-label form-control-label">{$translate.8}</label>
		<label class="col-lg-3 col-form-label form-control-label"><strong>{$userdata.username}</strong></label>
	</div>
	<div class="form-group row">
		<label class="col-lg-3 col-form-label form-control-label">{$translate.16}</label>
		<div class="col-lg-9"><input name="email" class="form-control{if isset($error.email)} is-invalid{/if}" type="text"{if isset($form.email)} value="{$form.email}"{else}{if isset($userdata.email)} value="{$userdata.email}"{/if}{/if}></div>
	</div>
	<div class="form-group row">
		<label class="col-lg-3 col-form-label form-control-label">{$translate.9}</label>
		<div class="col-lg-9"><input name="password" class="form-control{if isset($error.password)} is-invalid{/if}" type="password" pattern=".{ 5,}" title="{$translate.17}" {if isset($form.password)} value="{$form.password}"{/if}></div>
	</div>
	<div class="form-group row">
		<label class="col-lg-3 col-form-label form-control-label">{$translate.18}</label>
		<div class="col-lg-9"><input name="repeat" class="form-control{if isset($error.repeat)} is-invalid{/if}" type="password" pattern=".{ 5,}" title="{$translate.17}"{if isset($form.repeat)} value="{$form.repeat}"{/if}></div>
	</div>
	<input class="btn btn-lg btn-block btn2" value="{$translate.55}" type="submit">
</form>	
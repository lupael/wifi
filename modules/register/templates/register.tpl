<div class="content"><div class="contentsub register">
		{if !isset($success)}
			<div class="container top">
				<h2>{$translate.11}</h2>
			</div>
			<div class="container">
				<div class="row offerline">
					{include packages.tpl}
				</div>
			</div>
		{/if}
		<div class="container form{if isset($success)} form2{/if}"><div class="space3">
			{if !isset($success)}<h4>{$translate.14}</h4>{/if}
			
			{if isset($error) && !empty($error)}
				<div class="alert alert-danger" role="alert">{eval echo implode("<br />", $error);}</div>
			{/if}
			{if isset($success)}
				<div class="alert alert-success" role="alert"><i class="fas fa-spinner fa-pulse"></i>{$success}</div>
			{else}
				<form autocomplete="off" method="post" action="">
					<div class="form-group row">
						<label class="col-lg-3 col-form-label form-control-label">{$translate.8}</label>
						<div class="col-lg-9">
							<input name="username" class="form-control{if isset($error.username)} is-invalid{/if}" type="text" title="{$translate.15}"{if isset($user.username)} value="{$user.username}"{/if}>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-lg-3 col-form-label form-control-label">{$translate.16}</label>
						<div class="col-lg-9">
							<input name="email" class="form-control{if isset($error.email)} is-invalid{/if}" type="text" {if isset($user.email)} value="{$user.email}"{/if}>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-lg-3 col-form-label form-control-label">{$translate.9}</label>
						<div class="col-lg-9">
							<input name="password" class="form-control{if isset($error.password)} is-invalid{/if}" type="password" title="{$translate.17}">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-lg-3 col-form-label form-control-label">{$translate.18}</label>
						<div class="col-lg-9">
							<input name="repeat" class="form-control{if isset($error.repeat)} is-invalid{/if}" type="password" title="{$translate.17}">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-lg-3 col-form-label form-control-label">{$translate.19}</label>
						<div class="col-lg-9"><select class="form-control{if isset($error.package)} is-invalid{/if}" name="package">{foreach $packages as $row}<option value="{$row.id}"{if isset($user.package) && $user.package == $row.id}  selected{/if}>{$row.name}</option>{/foreach}</select></div>
					</div>

					<input class="btn btn-lg btn-block btn2" value="{$translate.20}" type="submit">

					<div class="terms">{$translate.21}.</div>
				</form>	
			{/if}
		</div></div>		
	</div>
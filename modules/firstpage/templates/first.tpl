	<div class="content"><div class="contentsub">
		<div class="container"><div class="row welcome">
			<div class="col-sm-8 first">

				<div class="langspace"><div id="langpick" class="border border-white rounded">
					<a href="/{$lang}/" class="langselector">{eval echo $config.languages[$lang]}</a>
					{foreach $config.languages as $k=>$v}
						{if $k!==$lang}<div class="droplist"><a href="/{$k}/">{$v}</a></div>{/if}
					{/foreach}
				</div></div>
				
				<div class="space1">
				<h1>{$translate.1}</h1><h2>{$translate.2}</h2><hr />
				<ul class="fa-ul">
					<li><span class="fa-li" ><i class="fas fa-wifi"></i></span>{$translate.3}</li>
					<li><span class="fa-li"><i class="fas fa-calendar-check"></i></span>{$translate.4}</li>
					<li><span class="fa-li"><i class="fas fa-lock"></i></span>{$translate.5}</li>
				</ul>
				<a href="/{$lang}/register" class="button">{$translate.6} &raquo;</a>
			</div></div>
			<div class="col-sm-4 second"><div class="space2">
				<h2>{$translate.7}</h2><hr />
				{if isset($error)}<div class="alert alert-danger" role="alert">{$error}</div>{/if}
				<form id="loginform" name="login" action="/{$lang}/" method="post">
					<span><i class="fas fa-user"></i></span><input type="text" placeholder="{$translate.8}" class="fi" name="username" />
					<span><i class="fas fa-key"></i></span><input type="password" placeholder="{$translate.9}" class="si" name="password" />
					<button class="btn btn-lg btn-primary btn-block" type="submit">{$translate.10}</button>
				</form>				
			</div></div>
		</div></div>		
	</div></div>
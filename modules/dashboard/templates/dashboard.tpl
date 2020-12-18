	<div class="content"><div class="contentsub">
		<div class="container dashboard">
			<div class="row">
				<div class="col-4 left">
				
					<ul class="list-group list-group-flush">
					
						<a href="" class="list-group-item menutoggle">
							<div class="row"><div class="col-1"><i class="fas fa-bars"></i></div><div class="col-11">{$translate.81}</div></div>
						</a>
						
						<a href="/{$lang}/dashboard/" class="list-group-item sublist{if $module=='general'} listg-1{/if} listg-2">
							<div class="row"><div class="col-1"><i class="fas fa-info-circle"></i></div><div class="col-11">{$translate.39}</div></div>
						</a>
						
						<a href="/{$lang}/dashboard/payment/" class="list-group-item sublist{if $module=='payment'} listg-1{/if}">
							<div class="row"><div class="col-1"><i class="far fa-credit-card"></i></div><div class="col-11">{$translate.40}</div></div>
						</a>

						<a href="/{$lang}/dashboard/settings/" class="list-group-item sublist{if $module=='settings'} listg-1{/if}"><div class="row"><div class="col-1"><i class="fas fa-user-cog"></i></div><div class="col-11">{$translate.41}</div></div></a>
						
						<a href="/{$lang}/dashboard/help/" class="list-group-item sublist{if $module=='help'} listg-1{/if}"><div class="row"><div class="col-1"><i class="fas fa-question-circle"></i></div><div class="col-11">{$translate.42}</div></div></a>
						
						<a href="/{$lang}/dashboard/logout/" class="list-group-item sublist{if $module=='logout'} listg-1 listg-3{/if}"><div class="row"><div class="col-1"><i class="fas fa-sign-out-alt"></i></div><div class="col-11">{$translate.43}</div></div></a>

					</ul>
				
				</div>
				<div class="col-8 right">
					<div class="top">
						<h2>{$title}</h2>
					</div>
					<div class="spacing">{$content}</div>
				</div>
			</div>
		</div>		
	</div></div>
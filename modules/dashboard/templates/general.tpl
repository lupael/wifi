<div class="general">
{if isset($user.statistics) && !empty($user.statistics)}
	<h3>{$translate.82}</h3>
	<div class="container">
		<div class="row statsline">
			<div class="col col-sm-2 iconl align-middle"><i class="far fa-clock"></i></div>
			<div class="col">
				<div class="progress">
						<div class="progress-bar" role="progressbar" style="width: {$user.statistics.time}%" aria-valuenow="{$user.statistics.time}" aria-valuemin="0" aria-valuemax="100">{$user.statistics.time}%</div>
				</div>

				<div class="container statsinfo"><div class="row"><div class="col">{$user.statistics.time_used}</div><div class="col">{$user.statistics.time_left}</div></div></div>
			</div>
		</div>
		<div class="row">
			<div class="col col-sm-2 iconl align-middle"><i class="fas fa-exchange-alt"></i></div>
			<div class="col">
				<div class="progress">
						<div class="progress-bar" role="progressbar" style="width: {$user.statistics.usage}%" aria-valuenow="{$user.statistics.usage}" aria-valuemin="0" aria-valuemax="100">{$user.statistics.usage}%</div>
				</div>
				<div class="container statsinfo"><div class="row"><div class="col">{$user.statistics.transfer_used} Gb</div><div class="col">{$user.statistics.transfer_left} Gb</div></div></div>
			</div>
		</div>
	</div>
{/if}
	<h3{if isset($user.statistics) && !empty($user.statistics)} class="h31"{/if}>{$translate.83}</h3>
	<div class="table-responsive"><table class="table">
		<table class="table table-striped"><tbody>
			<tr><th scope="row">{$translate.8}</th><td>{$user.username}</td></tr> 
			<tr><th scope="row">{$translate.84}</th><td>{$user.local_address}</td></tr> 
			<tr><th scope="row">{$translate.85}</th><td>{$user.local_mac}</td></tr> 
			<tr><th scope="row">{$translate.86}</th><td>{if $package}{$package.package.name}{else}{$translate.87}{/if}</td></tr>
			{if $package}
				<tr><th scope="row">{$translate.88}</th><td>{$package.date_start}</td></tr> 
				<tr><th scope="row">{$translate.89}</th><td>{$package.date_end}</td></tr>
			{/if}
		</tbody></table>
	</table></div>
</div>
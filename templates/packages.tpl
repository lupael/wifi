{foreach $packages as $row}
<div class="col-sm">
	<div class="border rounded-top text-center">
		<h3>{$row.name}</h3>
		<ul class="list-group list-group-flush">
			<li class="list-group-item">{$row.duration.display} {$row.duration.unit} {$translate.12}</li>
			<li class="list-group-item">{$row.transfer.display} Gb {$translate.13}</li>
			<li class="list-group-item">{$row.price.display} EUR</li>
			{if isset($button_buy) && $button_buy == true}<a href="/{$lang}/dashboard/payment/new/{$row.id}/" class="list-group-item">{$translate.46}</a>{/if}
		</ul>
	</div>
</div>
{/foreach}
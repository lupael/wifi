{if !empty($transactions)}
<h2>{$translate.44}</h2>

<div class="table-responsive"><table class="table">
	<table class="table table-striped"><thead><tr>
      <th scope="col">#</th>
      <th scope="col">{$translate.47}</th>
      <th scope="col">{$translate.48}</th>
      <th scope="col">{$translate.49}</th>
	  <th scope="col">{$translate.50}</th>
	  <th scope="col"></th>
	</tr></thead><tbody>
	{eval $i=1;}
	{foreach $transactions as $row}	
		<tr><th scope="row">{$i}</th>
		<td>{$row.package.name}</td>
		<td>{if isset($row.date_created)}{$row.date_created}{/if}</td>
		<td>{if isset($row.date_start)}{$row.date_start}{/if}</td>
		<td>{$row.package.price.display} EUR</td>
		<td class="lh15">
			{if $row.confirmed==0}<i class="far fa-question-circle text-warning" title="{$translate.58}"></i>{/if}
			{if $row.confirmed==1}<i class="far fa-check-circle text-success" title="{$translate.59}"></i>{/if}
			{if $row.confirmed==2}<i class="far fa-times-circle text-danger" title="{$translate.60}"></i>{/if}
		</td></tr>
		{eval $i++;}
	{/foreach}
	</tbody></table>
</table></div>
{/if}
{if $active == false}
	<h4{if empty($transactions)} class="nt4"{/if}>{$translate.45}</h4>
	{eval $button_buy=true;}
	<div class="container"><div class="row packages">
		{include packages.tpl}
	</div></div>
{/if}
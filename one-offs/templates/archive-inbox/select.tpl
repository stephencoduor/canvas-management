{assign var="__DIR__" value=$smarty.current_dir}
{extends file="subpage.tpl"}

{block name="subcontent"}

	<div class="container">
		<div class="readable-width">
			<p>Select the user whose inbox will be archived and choose a cutoff date before which all conversations will be archived.</p>
		</div>
	</div>
	
	{include file="$__DIR__/user.tpl"}

{/block}
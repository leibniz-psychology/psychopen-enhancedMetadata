<div {if $itm['condition']}class="section em-hidden-field {$itm['class']}" data-condition="{json_encode($itm['condition'])|escape|trim}"
     {else}class="section {$itm['class']}"{/if}>
    {if $itm['title'] && ($itm['title'][$currentLocale] || $itm['title']['en_US'])}
		<span class="label {$itm['title']['class']}">
					{if $itm['title'][$currentLocale]}
                        {$itm['title'][$currentLocale]|trim}
                    {else}
                        {$itm['title']['en_US']|trim}
                    {/if}
				</span>
    {/if}
    {if $itm['description'] &&($itm['description'][$currentLocale]  || $itm['description']['en_US'])}
		<label class="description {$itm['description']['class']}">
            {if $itm['description'][$currentLocale]}
                {$itm['description'][$currentLocale]|trim}
            {else}
                {$itm['description']['en_US']|trim}
            {/if}
		</label>
    {/if}
	<ul class="checkbox_and_radiobutton">
        {foreach from=$itm['fields'] item=$field}
            {assign var="uuid" value=""|uniqid|escape}
			<li class="{if $itm['inline']}em-inline{/if}">
				<label>
                    {if $itm['type']=='radio'}
                    {assign var="itmName" value=$itm['name']}
                    {else}
                    {assign var="itmName" value=$field['name']}
                    {/if}
					<input type="{$itm['type']}" id="{$itmName}-{$uuid}" value="{$field['value']}" name="{$itmName}"
					       class="field {$field['class']} {$itm['type']}{if $field['required']} required" validation="required"{else}"{/if}
                    {if $field['value'] == $enhMetaDataJson[$itmName]} checked{elseif !$enhMetaDataJson[$itmName] && $field['selected']}checked{/if}>
                    {if $field['desc'][$currentLocale]}
                    {$field['desc'][$currentLocale]|trim}
                    {else}
                    {$field['desc']['en_US']|trim}
                    {/if}
				</label>
			</li>
        {/foreach}
	</ul>
</div>
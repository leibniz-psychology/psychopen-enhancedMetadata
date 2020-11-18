<div {if $itm['condition']}class="section em-hidden-field {$itm['class']}" data-condition="{json_encode($itm['condition'])|escape|trim}"
     {else}class="section {$itm['class']}"{/if}>
	<label class="{$itm['title']['class']}">
        {if $itm['title'][$currentLocale]}
            {$itm['title'][$currentLocale]|trim}
        {else}
            {$itm['title']['en_US']|trim}
        {/if}
        {if $itm['required']}
			&nbsp;
			<span class="req">*</span>
        {/if}
		<label class="description {$itm['description']['class']}">
            {if $itm['description'][$currentLocale]}
                {$itm['description'][$currentLocale]|trim}
            {else}
                {$itm['description']['en_US']|trim}
            {/if}
		</label>
        {foreach from=$itm['fields'] item=$field}
            {if !$field['size'] || $field['size']|upper == 'LARGE'}
                {assign var="fbvSize" value=$fbvStyles.size.LARGE}
            {elseif $field['size']|upper == 'MEDIUM'}
                {assign var="fbvSize" value=$fbvStyles.size.MEDIUM}
            {elseif $field['size']|upper == 'SMALL'}
                {assign var="fbvSize" value=$fbvStyles.size.SMALL}
            {/if}
            {fbvElement type=$itm['type'] multilingual=$field['multilingual'] id=$field['name'] value=$enhMetaDataJson[$field['name']] rich=$field['rich']
            required=$field['required'] maxlength=$field['maxLength'] class=$field['class'] size=$fbvSize}
        {/foreach}
	</label>
</div>
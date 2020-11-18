<div class="section {$itm['class']}">
    {if $itm['title'] && ($itm['title'][$currentLocale] || $itm['title']['en_US'])}
		<label class="{$itm['title']['class']}">
            {if $itm['title'][$currentLocale]}
                {$itm['title'][$currentLocale]|trim}
            {else}
                {$itm['title']['en_US']|trim}
            {/if}
		</label>
    {/if}
    {if $itm['description'] && ($itm['description'][$currentLocale] || $itm['description']['en_US'])}
		<p class="{$itm['description']['class']}">
            {if $itm['description'][$currentLocale]}
                {$itm['description'][$currentLocale]|trim}
            {else}
                {$itm['description']['en_US']|trim}
            {/if}
		</p>
    {/if}
</div>
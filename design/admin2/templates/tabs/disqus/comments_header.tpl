{def $hasDisqusDatatype = false()}
{foreach $node.data_map as $attribute}
    {if $attribute.data_type_string|eq( 'disquscomments' )}
        {set $hasDisqusDatatype = true()}
        {break}
    {/if}
{/foreach}

{if $hasDisqusDatatype}
    <li id="node-tab-disquscomments" class="{if $last}last{else}middle{/if}{if $node_tab_index|eq( 'disquscomments' )} selected{/if}">
        {if $tabs_disabled}
            <span class="disabled">{'Disqus comments'|i18n( 'ezdisqus/admin' )}</span>
        {else}
            <a href={concat( $node_url_alias, '/(tab)/disquscomments#disqus_thread' )|ezurl}
               title="{'Show comments from Disqus'|i18n( 'ezdisqus/admin' )}"
               data-disqus-identifier="{$node.contentobject_id}">{'Loading comments...'|i18n( 'ezdisqus/admin' )}</a>
        {/if}
    </li>

    {include uri="design:disqus_count.tpl"}
{/if}

{def $hasDisqusDatatype = false()}
{foreach $node.data_map as $attribute}
    {if $attribute.data_type_string|eq( 'disquscomments' )}
        {set $hasDisqusDatatype = true()}
        {break}
    {/if}
{/foreach}

{if $hasDisqusDatatype}
    {include uri="design:disqus.tpl"
        disqus_identifier=$node.contentobject_id}
{/if}
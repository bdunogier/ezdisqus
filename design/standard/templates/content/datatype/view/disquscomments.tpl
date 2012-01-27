{def $attributeContent = $attribute.content}
{if $attributeContent.comments_enabled}
    {include uri="design:disqus.tpl"
         disqus_shortname=$attributeContent.shortname
         disqus_identifier=$attributeContent.identifier
         disqus_title=$attributeContent.title
         disqus_url=$attributeContent.url|ezurl( 'no', 'full' )}
{/if}
{undef $attributeContent}
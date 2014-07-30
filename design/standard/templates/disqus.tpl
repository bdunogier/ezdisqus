{*
    Disqus template for comments.
    Available variables :
      - $disqus_shortname (defaults to disqus.ini/[Base].DisqusShortname)
      - $disqus_identifier
      - $disqus_url : Full absolute URL (e.g. http://www.mysite.com/my-article)
      - $disqus_title

    You can pass those variables via the include parameters :
    {include uri="design:standard/disqus.tpl" disqus_identifier="some_identifier" disqus_title="My custom title"}
*}
<div id="disqus_thread"></div>
<script type="text/javascript">
    var disqus_shortname = '{cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )}';

{if ezini( 'Base', 'DevelopmentMode', 'disqus.ini' )|eq( 'enabled' )}
    var disqus_developer = 1

{/if}
{if is_set( $disqus_identifier )}
    var disqus_identifier = '{$disqus_identifier}';

{/if}
{if is_set( $disqus_url )}
    var disqus_url = '{$disqus_url}';

{/if}
{if is_set( $disqus_title )}
    var disqus_title = '{$disqus_title|wash('javascript')}';

{/if}

{literal}
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
{/literal}
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>

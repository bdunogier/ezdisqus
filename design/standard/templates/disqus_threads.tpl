{*
    Disqus template for popular threads.
    Available variables :
      - $disqus_shortname (defaults to disqus.ini/[Base].DisqusShortname)
      - $num_items (default to disqus.ini/[Threads].items)

	You can pass those variables via the include parameters :
    {include uri="design:standard/disqus_threads.tpl" num_items=5}
*}

{def 	$disqus_shortname = cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )
		$num_items = cond( is_set( $num_items ), $num_items, ezini( 'Threads', 'items', 'disqus.ini' ) ) }


<div id="popularthreads" class="dsq-widget">
	<h2 class="dsq-widget-title">Popular Threads</h2>
	<script type="text/javascript" src="http://{$disqus_shortname}.disqus.com/popular_threads_widget.js?num_items={$num_items}"></script>
</div>
<a href="http://disqus.com/">Powered by Disqus</a>

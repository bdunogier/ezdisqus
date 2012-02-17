{*
    Disqus template for popular threads.
    
	How to include?
    {include uri="design:standard/disqus_threads.tpl"}
*}

{def $disqus_shortname = cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )};


<div id="popularthreads" class="dsq-widget">
	<h2 class="dsq-widget-title">Popular Threads</h2>
	<script type="text/javascript" src="http://{$disqus_shortname}.disqus.com/popular_threads_widget.js?num_items={ezini( 'Threads', 'items', 'disqus.ini' )}"></script>
</div>
<a href="http://disqus.com/">Powered by Disqus</a>

{*
    Disqus template for recent comments.
    Available variables :
      - disqus_shortname (defaults to disqus.ini/[Base].DisqusShortname)
      - num_items (default to disqus.ini/[Comments].items)
	  - hide_avatars (0/1) - Show/hide avatars (default to disqus.ini/[Comments].items)
	  - avatar_size (valid values are 24, 32, 48, 92, 128) (default to disqus.ini/[Comments].items)
	  - excerpt_length comment excerpt length (default to disqus.ini/[Comments].items)
	  
	You can pass those variables via the include parameters :
    {include uri='design:disqus_recente_comments.tpl' num_items=5 hide_avatars=0 avatar_size=24 excerpt_length=200}
*}

{def 	$disqus_shortname = cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )
		$num_items = cond( is_set( $num_items ), $num_items, ezini( 'Comments', 'items', 'disqus.ini' ) )
		$avatars = cond( is_set( $hide_avatars ), $hide_avatars, ezini( 'Comments', 'hide_avatars', 'disqus.ini' ) )
		$avatar_size = cond( is_set( $avatar_size ), $avatar_size, ezini( 'Comments', 'avatar_size', 'disqus.ini' ) )
		$length = cond( is_set( $excerpt_length ), $excerpt_length, ezini( 'Comments', 'excerpt_length', 'disqus.ini' ) )}


<div id="recentcomments" class="dsq-widget">
	<h2 class="dsq-widget-title">Recent Comments</h2>
	<script type="text/javascript" src="http://{$disqus_shortname}.disqus.com/recent_comments_widget.js?num_items={$num_items}&hide_avatars={$avatars}&avatar_size={$avatar_size}&excerpt_length={$length}"></script>
</div>
<a href="http://disqus.com/">Powered by Disqus</a>

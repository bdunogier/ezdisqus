{*
    Disqus template for top commenters.
    Available variables :
      - disqus_shortname (defaults to disqus.ini/[Base].DisqusShortname)
      - num_items (default to disqus.ini/[Commenters].items)
	  - hide_mods (0/1) - Show/hide moderators (default to disqus.ini/[Commenters].items)
	  - hide_avatars (0/1) - Show/hide avatars (default to disqus.ini/[Commenters].items)
	  - avatar_size (valid values are 24, 32, 48, 92, 128) (default to disqus.ini/[Commenters].items)
	  
	You can pass those variables via the include parameters :
    {include uri='design:disqus_top_commenters.tpl' num_items=5 hide_mods=0 hide_avatars=0 avatar_size=24}
*}

{def 	$disqus_shortname = cond( is_set( $disqus_shortname ), $disqus_shortname, ezini( 'Base', 'DisqusShortname', 'disqus.ini' ) )
		$num_items = cond( is_set( $num_items ), $num_items, ezini( 'Commenters', 'items', 'disqus.ini' ) )
		$moderators = cond( is_set( $hide_mods ), $hide_mods, ezini( 'Commenters', 'hide_mods', 'disqus.ini' ) )
		$avatars = cond( is_set( $hide_avatars ), $hide_avatars, ezini( 'Commenters', 'hide_avatars', 'disqus.ini' ) )
		$avatar_size = cond( is_set( $avatar_size ), $avatar_size, ezini( 'Commenters', 'avatar_size', 'disqus.ini' ) )}


<div id="topcommenters" class="dsq-widget">
<script type="text/javascript" src="http://{$disqus_shortname}.disqus.com/top_commenters_widget.js?num_items={$num_items}&hide_mods={$moderators}&hide_avatars={$avatars}&avatar_size={$avatar_size}"></script>
</div>
<a href="http://disqus.com/">Powered by Disqus</a>

<div class="comments_area" id="comments">
    <h2 class="comments_title">
        {l s='All comments' mod='thnxblog'}
    </h2>
    <ol class="comment_list">
		{foreach from=$thnxblog_commets item=thnxblog_commet}
        <li class="comment" id="comment_{$thnxblog_commet.id_thnx_comments}">
            <article class="comment_body">
				<div class="comment_author vcard">
				    <img alt="" class="thnxblog_img avatar avatar-70 photo" height="70" src="http://2.gravatar.com/avatar/597a1e6b0dfdf57f53ef8fb80fa190d7?s=70&d=mm&r=g" width="70">
				</div>
				<div class="comment_content">
					<div class="comment_meta">
					    <div class="comment_meta_author">
					    	<b class="fn">{$thnxblog_commet.name}</b>
					    </div>
					    <div class="comment_meta_date">
					    	<time datetime="2016-03-07T04:33:23+00:00">
					    	    {$thnxblog_commet.created|date_format:"%e %B, %Y"}
					    	</time>
					    </div>
					    <div class="reply">
					        <a aria-label="Reply to raihan@sntbd.com" class="comment-reply-link" href="#" onclick='return addComment.moveForm( "div-comment-3", "3", "respond", "38" )' rel="nofollow">
					            Reply
					        </a>
					    </div>
					</div>
					<div class="comment_content_bottom">
						<p>
							{$thnxblog_commet.content}
						</p>
					</div>
				</div>
            </article>
        </li>
		{/foreach}
    </ol>
</div>
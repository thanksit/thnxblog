{extends file='page.tpl'}


{block name='page_header_container'}{/block}

{block name="page_content_container"}
	<section id="content" class="page-content">
	{if isset($thnxblogpost) && !empty($thnxblogpost)}
	<div class="kr_blog_post_area">
		<div class="kr_blog_post_inner blog_style_{$thnxblogsettings.blog_style} column_{$thnxblogsettings.blog_no_of_col}">
			{foreach from=$thnxblogpost item=xpblgpst}
				<article id="blog_post" class="blog_post blog_post_{$xpblgpst.post_format} clearfix">
					<div class="blog_post_content">
						<div class="blog_post_content_top">
							<div class="post_thumbnail">
							{block name="thnxblog_post_thumbnail"}
								{if $xpblgpst.post_format == 'video'}
									{assign var="postvideos" value=','|explode:$xpblgpst.video}
									{if $postvideos|@count > 1 }
										{assign var="class" value='carousel'}
									{else}
										{assign var="class" value=''}
									{/if}
									{include file="module:thnxblog/views/templates/front/default/post-video.tpl" postvideos=$postvideos width='870' height="360" class=$class}
								{elseif $xpblgpst.post_format == 'audio'}
									{assign var="postaudio" value=','|explode:$xpblgpst.audio}
									{if $postaudio|@count > 1 }
										{assign var="class" value='carousel'}
									{else}
										{assign var="class" value=''}
									{/if}
									{include file="module:thnxblog/views/templates/front/default/post-audio.tpl" postaudio=$postaudio width='870' height="360" class=$class}
								{elseif $xpblgpst.post_format == 'gallery'}
									{if $xpblgpst.gallery_lists|@count > 1 }
										{assign var="class" value='carousel'}
									{else}
										{assign var="class" value=''}
									{/if}
									{include file="module:thnxblog/views/templates/front/default/post-gallery.tpl" gallery_lists=$xpblgpst.gallery_lists imagesize="medium" class=$class}
								{else}
									<img class="img-responsive" src="{$xpblgpst.post_img_medium}" alt="{$xpblgpst.post_title}">

									<div class="blog_mask">
										<div class="blog_mask_content">

											<a class="thumbnail_lightbox" href="{$xpblgpst.post_img_medium}">
												<i class="icon-expand"></i>
											</a>

											{* <a class="post_link" href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">
												<i class="icon-link"></i>
											</a> *}
											
										</div>
									</div>

								{/if}

								<div class="post_meta_date">
									{$xpblgpst.post_date|date_format:"<b>%e</b> %b" nofilter}
								</div>
								
							{/block}
							</div>
						</div>

						<div class="blog_post_content_bottom">
							<h3 class="post_title"><a href="{$xpblgpst.link}">{$xpblgpst.post_title}</a></h3>
							
							<div class="post_meta clearfix">
								
									
								
								<div class="meta_author">
									{* <i class="icon-user"></i> *}
									<span>{l s='By' mod='thnxblog'} {$xpblgpst.post_author_arr.firstname} {$xpblgpst.post_author_arr.lastname}</span>
								</div>

								{* <div class="post_meta_date">
									<i class="icon-calendar"></i>
									{$xpblgpst.post_date|date_format:"%b %dTH, %Y"}
								</div> *}

								<div class="meta_category">
									{* <i class="icon-tag"></i> *}
										<span>{l s='In' mod='thnxblog'}</span>
										<a href="{$xpblgpst.category_default_arr.link}">{$xpblgpst.category_default_arr.name}</a>
								</div>
								<div class="meta_comment">
									{* <i class="icon-eye"></i> *}
									<span>{l s='Views' mod='thnxblog'} ({$xpblgpst.comment_count})</span>
								</div>
							</div>

							<div class="post_content">
								{if isset($xpblgpst.post_excerpt) && !empty($xpblgpst.post_excerpt)}
									{$xpblgpst.post_excerpt|truncate:300:'...'|escape:'html':'UTF-8'}
								{else}
									{$xpblgpst.post_content|truncate:400:'...'|escape:'html':'UTF-8'}
								{/if}
							</div>
							<div class="content_more">
								<a class="read_more" href="{$xpblgpst.link}">{l s='Read More' mod='thnxblog'}</a>
							</div>
						</div>

					</div>
				</article>
			{/foreach}
		</div>
	</div>
	{/if}
	</section>
{/block}
{include file="module:thnxblog/views/templates/front/default/pagination.tpl"}
{/block}
{block name="left_column"}
	{assign var="layout_column" value=$layout|replace:'layouts/':''|replace:'.tpl':''|strval}
	{if ($layout_column == 'layout-left-column')}
		<div id="left-column" class="sidebar col-xs-12 col-sm-12 col-md-3 pull-md-9">
			{if ($thnxblog_column_use == 'own_ps')}
				{hook h="displaythnxblogleft"}
			{else}
				{hook h="displayLeftColumn"}
			{/if}
		</div>
	{/if}
{/block}
{block name="right_column"}
	{assign var="layout_column" value=$layout|replace:'layouts/':''|replace:'.tpl':''|strval}
	{if ($layout_column == 'layout-right-column')}
		<div id="right-column" class="sidebar col-xs-12 col-sm-4 col-md-3">
			{if ($thnxblog_column_use == 'own_ps')}
				{hook h="displaythnxblogright"}
			{else}
				{hook h="displayRightColumn"}
			{/if}
		</div>
	{/if}
{/block}
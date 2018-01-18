<div class="comment_respond clearfix m_bottom_50" id="respond">
    <h3 class="comment_reply_title" id="reply-title">
        Leave a Reply
        <small>
            <a href="/wp_showcase/wp-supershot/?p=38#respond" id="cancel-comment-reply-link" rel="nofollow" style="display:none;">
                Cancel reply
            </a>
        </small>
    </h3>
    <form class="comment_form" action="" method="post" id="thnxblogs_commentfrom" role="form" data-toggle="validator">
    	<div class="form-group thnxblogs_message"></div>
    	<div class="form-group thnxblog_name_parent">
    	  <label for="thnxblog_name">Your Name:</label>
    	  <input type="text"  id="thnxblog_name" name="thnxblog_name" class="form-control thnxblog_name" required>
    	</div>
    	<div class="form-group thnxblog_email_parent">
    	  <label for="thnxblog_email">Your Email:</label>
    	  <input type="email"  id="thnxblog_email" name="thnxblog_email" class="form-control thnxblog_email" required>
    	</div>
    	<div class="form-group thnxblog_website_parent">
    	  <label for="thnxblog_website">Website Url:</label>
    	  <input type="url"  id="thnxblog_website" name="thnxblog_website" class="form-control thnxblog_website">
    	</div>
    	<div class="form-group thnxblog_subject_parent">
    	  <label for="thnxblog_subject">Subject:</label>
    	  <input type="text"  id="thnxblog_subject" name="thnxblog_subject" class="form-control thnxblog_subject" required>
    	</div>
    	<div class="form-group thnxblog_content_parent">
    	  <label for="thnxblog_content">Comment:</label>
    	  <textarea rows="15" cols="" id="thnxblog_content" name="thnxblog_content" class="form-control thnxblog_content" required></textarea>
    	</div>
    	<input type="hidden" class="thnxblog_id_parent" id="thnxblog_id_parent" name="thnxblog_id_parent" value="0">
    	<input type="hidden" class="thnxblog_id_post" id="thnxblog_id_post" name="thnxblog_id_post" value="{$thnxblogpost.id_thnxposts}">
    	<input type="submit" class="btn btn-default pull-left thnxblog_submit_btn" value="Submit Button">
    </form>
</div>
{thnxblog_js name="single_comment_form"}
<script type="text/javascript">
// disabled
$('.thnxblog_submit_btn').on("click",function(e) {
	e.preventDefault();
	if(!$(this).hasClass("disabled")){
		var data = new Object();
		$('[id^="thnxblog_"]').each(function()
		{
			id = $(this).prop("id").replace("thnxblog_", "");
			data[id] = $(this).val();
		});
		function logErrprMessage(element, index, array) {
		  $('.thnxblogs_message').append('<span class="thnxblogs_error">'+element+'</span>');
		}
		function thnxremove() {
		  $('.thnxblogs_error').remove();
		  $('.thnxblogs_success').remove();
		}
		function logSuccessMessage(element, index, array) {
		  $('.thnxblogs_message').append('<span class="thnxblogs_success">'+element+'</span>');
		}
		$.ajax({
			url: thnx_base_dir + 'modules/thnxblog/ajax.php',
			data: data,
			type:'post',
			dataType: 'json',
			beforeSend: function(){
				thnxremove();
				$(".thnxblog_submit_btn").val("Please wait..");
				$(".thnxblog_submit_btn").addClass("disabled");
			},
			complete: function(){
				$(".thnxblog_submit_btn").val("Submit Button");
				$(".thnxblog_submit_btn").removeClass("disabled");	
			},
			success: function(data){
				thnxremove();
				if(typeof data.success != 'undefined'){
					data.success.forEach(logSuccessMessage);
				}
				if(typeof data.error != 'undefined'){
					data.error.forEach(logErrprMessage);
				}
			},
			error: function(data){
				thnxremove();
				$('.thnxblogs_message').append('<span class="error">Something Wrong ! Please Try Again. </span>');
			},
		});	
	}
});
</script>
{/thnxblog_js}
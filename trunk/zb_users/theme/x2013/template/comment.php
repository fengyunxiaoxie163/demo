<ol class="commentlist" id="cmt{$comment.ID}">
	<li class="comment odd alt thread-odd thread-alt depth-{$comment.ID}" id="comment-{$comment.ID}">
		<div class="c-floor"><a href="#cmt{$comment.ID}">#{$key+1}</span>
</a></div>
		<div class="c-avatar">
			<img class="avatar" src="http://0.gravatar.com/avatar/{php}echo md5($comment->Author->Email);{/php}?s=36&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D55&amp;r=G" width="36" height="36">
		</div>
		<div class="c-main" id="div-comment-{$comment.ID}">
			<div class="c-meta"><span class="c-author">{$comment.Author.Name}</span>{$comment.Time()} <a class='comment-reply-link' href='#respond' onclick="RevertComment('{$comment.ID}')">回复</a></div>
			<p>{$comment.Content}
{foreach $comment.Comments as $key => $comment}
	{template:comment}
{/foreach}	
			</p>
		</div>
	</li>
</ol>
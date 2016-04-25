jQuery(document).ready(function($){
	$('#like-form').submit(function(e){
		e.preventDefault();
		var x = document.getElementById("likebtn").value;
		if(x =='Dislike')
		{
			var post_id =document.getElementById("postid").value;
			var post_data = {
			 	action:'dislike_it',
			 	post_id: post_id,
			 	liketon_nonce: liketondata.nonce
			};
			$.post(liketondata.ajaxurl,post_data,function(response){
				if(response != 'disliked') {
			 		alert('Dislike Failed');
			 	}
			});
			document.getElementById("likebtn").value='Like';
		} else if(x=='Like'){

			var post_id =document.getElementById("postid").value;
			var post_data = {
			 	action:'like_it',
			 	post_id: post_id,
			 	liketon_nonce: liketondata.nonce
			};
			$.post(liketondata.ajaxurl,post_data,function(response){
				if(response != 'liked') {
			 		alert('Like Failed');
			 	}
			});
			document.getElementById("likebtn").value='Dislike';
		}
	});
	$('.dislike-btn').click(function(e){
		e.preventDefault();
		var post_id = $(this).data("id");
		var post_data = {
			 	action:'dislike_it',
			 	post_id: post_id,
			 	liketon_nonce: liketondata.nonce
			};
			$.post(liketondata.ajaxurl,post_data,function(response){
				if(response == 'disliked') {
					location.reload();
			 	} else {
			 		alert('Dislike Failed');
			 	}
			});
	});
});

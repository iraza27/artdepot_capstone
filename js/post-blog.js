
$(document).ready(function(){
	$('#blog_post_form').on('submit', function(e){
		e.preventDefault();

		var form_data = $(this).serialize();
		$.ajax({
			url: "php/post-blog.php",
			type: "POST",
			data:form_data,
			success:function(data){
			}
		});
	});
});
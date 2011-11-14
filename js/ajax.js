/**
 * 
 */

$(document).ready(function(){
	$('#contacts_google_sync').click(function() {

		$('#waiting').show(500);
		$('#demoForm').hide(0);
		$('#message').hide(0);

		$.ajax({
			type : 'POST',
			url : 'post.php',
			dataType : 'json',
			data: {
				email : $('#email').val()
			},
			success : function(data){
				$('#waiting').hide(500);
				$('#message').removeClass().addClass((data.error === true) ? 'error' : 'success')
					.text(data.msg).show(500);
				if (data.error === true)
					$('#demoForm').show(500);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				$('#waiting').hide(500);
				$('#message').removeClass().addClass('error')
					.text('There was an error.').show(500);
				$('#demoForm').show(500);
			}
		});

		return false;
	});
});
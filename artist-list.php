<?php require('header.php'); ?>
<style>
	#artistlist-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<!-- Header -->
<section class="body-content">

<!-- Search Section -->
	<section class="artist-list-search">
	<!-- Search by Name -->
		<div class="artist-list-search-textbox control has-icons-left has-icons-right">
			<input id="alst-search-artist" class="input is-rounded" type="text" placeholder="Search Artist" name="alst-search-artist">
			<span class="icon is-small is-left">
			   <i class="fas fa-search"></i>
			</span>
		</div>
	<!-- Search by Alphabete -->
		<div class="artist-list-search-alphabet">
			<label>Search by: </label>
			<?php
				foreach(range('A','Z') as $v){
	    			echo '<span class="artist-list-search-az">'.$v.'</span>';
				}
				
			?>
		</div>
	</section>
<!-- Search Result Section -->
	<section id="artist-list-results"></section>

</section>
<script>
	$(document).ready(function(e){
		// On Page Load
		$.ajax({
			url: "php/artist-search.php",
			method: "POST",
			data: "",
			success: function(data){
				$("#artist-list-results").html(data);
			}
		});

		//On search by alphabet

		$(".artist-list-search-az").click(function(){
			var search = $(this).text();
			$.ajax({
				url: "php/artist-search.php",
				method: "POST",
				data: ({searchbyletter:search}),
				success: function(data){
					$("#artist-list-results").html(data);
					$("#alst-search-artist").val('');
				}
			});
		});

		// On Search
		$("#alst-search-artist").keyup(function(){
			var search = $(this).val();

			$.ajax({
				url: "php/artist-search.php",
				method: "POST",
				data: ({searched:search}),
				success: function(data){
					$("#artist-list-results").html(data);
				}
			});
		});
	});
</script>
<!-- Footer -->
<?php require('footer.php'); ?>
<?php require('header.php'); ?>
<!-- Header -->

<!-- Specfic Images -->
<style>
  #commissions-nav-btn{background-color: #d9f204; color: #000000;}
</style>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<section class="body-content">
  <div class="commissions-list-container">
      <div class="tabs is-centered is-boxed is-medium">
      <ul>
        <li class="is-active">
          <a class="tabbed-menu-commissions" id="tm-request">
            <span class="icon is-small"><i class="far fa-bell" aria-hidden="true"></i></span>
            <span>Requests</span>
          </a>
        </li>
        <li>
          <a class="tabbed-menu-commissions" id="tm-mycommissions">
            <span class="icon is-small"><i class="far fa-list-alt" aria-hidden="true"></i></span>
            <span>My Commissions</span>
          </a>
        </li>
        <li>
          <a class="tabbed-menu-commissions" id="tm-approvedcommissions">
            <span class="icon is-small"><i class="far fa-thumbs-up" aria-hidden="true"></i></span>
            <span>Approved Commissions</span>
          </a>
        </li>
        <li>
          <a class="tabbed-menu-commissions" id="tm-pendingcommissions">
            <span class="icon is-small"><i class="far fa-clock" aria-hidden="true"></i></span>
            <span>Pending Commissions</span>
          </a>
        </li>
        <li>
          <a class="tabbed-menu-commissions" id="tm-completedcomissions">
            <span class="icon is-small"><i class="far fa-check-circle" aria-hidden="true"></i></span>
            <span>Completed Commissions</span>
          </a>
        </li>
      </ul>
    </div>

    <div id="commissions-content-container"></div>
  </div>
</section>
<script>
  $(document).ready(function(e){
    //Generic Tabbed Controls
    $(".tabbed-menu-commissions").click(function(){
      $(this).parent().parent().find("li").removeClass('is-active');
      $(this).parent().addClass('is-active');
    });
    var type = "request";
    //Default AJAX call
    $.ajax({
      url: "php/commissions-function.php",
      method: "POST",
      data: ({type:type}),
      success: function(data){
        $("#commissions-content-container").html(data);
      }
    });

    //AJAX calls on specific Tabs
    $("#tm-request").click(function(){
      type = "request";
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({type:type}),
        success: function(data){
          $("#commissions-content-container").html(data);
        }
      });
    });
    $("#tm-mycommissions").click(function(){
      type = "commissions";
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({type:type}),
        success: function(data){
          $("#commissions-content-container").html(data);
        }
      });
    });
    $("#tm-approvedcommissions").click(function(){
      type = "approved";
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({type:type}),
        success: function(data){
          $("#commissions-content-container").html(data);
        }
      });
    });
    $("#tm-pendingcommissions").click(function(){
      type = "pending";
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({type:type}),
        success: function(data){
          $("#commissions-content-container").html(data);
        }
      });
    });
    $("#tm-completedcomissions").click(function(){
      type = "completed";
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({type:type}),
        success: function(data){
          $("#commissions-content-container").html(data);
        }
      });
    });

    //Approve button click
    $("#commissions-content-container").on("click", ".approve-btn", function(){
      var commID = $(this).parent().find(".commID").val();
      var approved = true;

      //Added values

      var additionalNotes = $(this).parent().find(".artistNotes").val()
      var finalPrice = $(this).parent().find(".finalprice").val()

      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({approve:approved, commID:commID, additionalNotes:additionalNotes, finalPrice:finalPrice}),
        success: function(data){
          location.reload();
        }
      });
    });

    //reject button click
    $("#commissions-content-container").on("click", ".reject-btn", function(){
      var commID = $(this).parent().find(".commID").val();
      var rejected = true;
      $.ajax({
        url: "php/commissions-function.php",
        method: "POST",
        data: ({reject:rejected, commID:commID}),
        success: function(data){
          location.reload();
        }
      });
    });
  });
</script>
<!-- Footer -->
<?php require('footer.php'); ?>
<!DOCTYPE html> 
<html> 
<head>
   <meta content="text/html; charset=UTF-8" http-equiv="content-type">
   <title><?php echo TITLE;?></title> 
   <link href="<?php echo INCLUDE_;?>/style/bootstrap.css" media="screen" rel="stylesheet">
   <link href="<?php echo INCLUDE_;?>/style/bootstrap-table.css" media="screen" rel="stylesheet">
   <link href="<?php echo INCLUDE_;?>/style/bootstrap-select.min.css" media="screen" rel="stylesheet">
   <script src="<?php echo INCLUDE_;?>/style/jquery-2.1.1.min.js"></script>
   <script src="<?php echo INCLUDE_;?>/style/bootstrap.min.js"></script>
   <script src="<?php echo INCLUDE_;?>/style/bootstrap-table.js"></script>
   <script src="<?php echo INCLUDE_;?>/style/bootstrap-select.js"></script>
   <script src="<?php echo INCLUDE_;?>/style/core.js"></script>
   <script src="<?php echo INCLUDE_;?>/style/bootbox.min.js"></script>
   <script type="text/javascript">
	   var MESSAGE_Short = '<?php GetText_('settings_password_too_short'); ?>';
	   var MESSAGE_Match = '<?php GetText_('settings_password_dont_match'); ?>';
	   $('.selectpicker').selectpicker();
	   
	$(document).on('click', "a.thumb_img", function() 
	{
		$('#modal-body').empty();
		$('<img style="width: 90%" src="'+this.getAttribute("id")+'">').appendTo('#modal-body');
		$('#IMGMod').modal({show:true});
	});
	

   </script>
   
   <style type="text/css">
	html, body {   padding-top:30px;}
	#wrap 
	{
		padding: 10px;
		min-height: -webkit-calc(100% - 50px);     /* Chrome */
		min-height: -moz-calc(100% - 50px);     /* Firefox */
		min-height: calc(100% - 50px);     /* native */
	}
		
	.borderless tbody tr td, .borderless thead tr th 
	{ border-left: none;  border-bottom: none; }

	.retd tbody tr td, .retd thead tr th 
	{
		width:90%;
	}
   </style>
</head>
	<body>
		<div class="wrap"> 
			<div class="container">
				<div class="navbar navbar-default navbar-fixed-top"> 
				   <div class="container"> 
					  <div class="navbar-header"><a class="navbar-brand"><?php echo TITLE ?></a></div>
					  <div class="navbar-collapse collapse"> 
						 <ul class="nav navbar-nav">
							<?php
								$TitlePage = TITLE;
								foreach($Page as $Elements)
								{
									if(isset($Elements[2]))
									{
										echo '
										<li class="dropdown">
										  <a href="?' .ACTVALUE_. '=' .$Elements[0]. '" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' .$Elements[1]. ' <span class="caret"></span></a>
										  <ul class="dropdown-menu">';
											foreach($Elements[2] as $SubElements)
											{
												if($SubElements[1] == "divider")
												{
													 echo '<li class="nav-divider"></li>';
												}
												else
												{
													if($Action == $SubElements[0])
													{
														$TitlePage .= " - " . $SubElements[1];
														echo '<li class="active"><a href="?' .ACTVALUE_. '=' .$SubElements[0]. '">' .$SubElements[1]. '</a></li>';
													}
														
													else
														echo '<li><a href="?' .ACTVALUE_. '=' .$SubElements[0]. '">' .$SubElements[1]. '</a></li>';
												}
											}
								
										echo	
										  '</ul>
										</li>';
									}
									else
									{
										if($Action == $Elements[0])
										{
											$TitlePage .= " - " . $Elements[1];
											echo '<li class="active"><a href="?' .ACTVALUE_. '=' .$Elements[0]. '">' .$Elements[1]. '</a></li>';
										}
										else
											echo '<li><a href="?' .ACTVALUE_. '=' .$Elements[0]. '">' .$Elements[1]. '</a></li>';
									}
								}
							?>
						</ul>
						<script> document.title = "<?php echo $TitlePage;?>";</script>
						<p id="dbg" class="navbar-text navbar-default navbar-right"></p>
					  </div>
				   </div>
				</div>

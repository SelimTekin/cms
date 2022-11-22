<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view("includes/head"); ?>
</head>
	
<body class="menubar-left menubar-unfold menubar-light theme-primary">
	<!--============= start main area -->

	<!-- APP NAVBAR ==========-->
	<?php $this->load->view("includes/navbar"); ?>
	<!--========== END app navbar -->

	<!-- APP ASIDE ==========-->
	<?php $this->load->view("includes/aside"); ?>
	<!--========== END app aside -->

	<!-- navbar search -->
	<?php $this->load->view("includes/navbar-search"); ?>
	<!-- .navbar-search -->

	<!-- APP MAIN ==========-->
	<main id="app-main" class="app-main">
		<div class="wrap">
			<section class="app-content">
				<?php $this->load->view("{$viewFolder}/{$subViewFolder}/content"); ?>
			</section><!-- #dash-content -->
		</div><!-- .wrap -->
		<!-- APP FOOTER -->
		<?php $this->load->view("includes/footer"); ?>
		<!-- /#app-footer -->
	</main>
	<!--========== END app main -->

	<!-- APP CUSTOMIZER -->
	<!--<div id="app-customizer" class="app-customizer">	kullanmayacağız
			<a href="javascript:void(0)" 
				class="app-customizer-toggle theme-color" 
				data-toggle="class" 
				data-class="open"
				data-active="false"
				data-target="#app-customizer">
				<i class="fa fa-gear"></i>
			</a>
			<div class="customizer-tabs">
				<!-- tabs list -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#menubar-customizer" aria-controls="menubar-customizer" role="tab" data-toggle="tab">Menubar</a></li>
					<li role="presentation"><a href="#navbar-customizer" aria-controls="navbar-customizer" role="tab" data-toggle="tab">Navbar</a></li>
				</ul><!-- .nav-tabs -->

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane in active fade" id="menubar-customizer">
						<div class="hidden-menubar-top hidden-float">
							<div class="m-b-0">
								<label for="menubar-fold-switch">Fold Menubar</label>
								<div class="pull-right">
									<input id="menubar-fold-switch" type="checkbox" data-switchery data-size="small" />
								</div>
							</div>
							<hr class="m-h-md">
						</div>
						<div class="radio radio-default m-b-md">
							<input type="radio" id="menubar-light-theme" name="menubar-theme" data-toggle="menubar-theme" data-theme="light">
							<label for="menubar-light-theme">Light</label>
						</div>

						<div class="radio radio-inverse m-b-md">
							<input type="radio" id="menubar-dark-theme" name="menubar-theme" data-toggle="menubar-theme" data-theme="dark">
							<label for="menubar-dark-theme">Dark</label>
						</div>
					</div><!-- .tab-pane -->
					<div role="tabpanel" class="tab-pane fade" id="navbar-customizer">
						<!-- This Section is populated Automatically By javascript -->
					</div><!-- .tab-pane -->
				</div>
			</div><!-- .customizer-taps -->
			<hr class="m-0">
			<div class="customizer-reset">
				<button id="customizer-reset-btn" class="btn btn-block btn-outline btn-primary">Reset</button>
				<a href="https://themeforest.net/item/infinity-responsive-web-app-kit/16230780" class="m-t-sm btn btn-block btn-danger">Buy Now</a>
			</div>
	<!--</div><!-- #app-customizer -->

	<?php $this->load->view("includes/include_script"); ?>
	<?php $this->load->view("{$viewFolder}/{$subViewFolder}/page_script"); ?>
</body>
</html>
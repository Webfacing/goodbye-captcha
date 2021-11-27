<div class="row">
	<article class="col-sm-12">
		<div id="wid-id-0" class="gdbcwidget clearfix">

			<header>

				<span class="widget-icon icon-primary"><span class="glyphicon glyphicon-dashboard"></span></span>

				<h2> <?php  _e('Dashboard', GoodByeCaptcha::PLUGIN_SLUG) ?> </h2>

				<ul id="dashboard-navigation" class="nav nav-tabs pull-right in">
					<li class="active">
						<a>
							<i class="glyphicon glyphicon-stats"></i>
							<span class="hidden-mobile hidden-tablet"><?php  _e('Stats', GoodByeCaptcha::PLUGIN_SLUG) ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo $arrReportsNavigationTabUrl[1]; ?>">
							<i class="glyphicon glyphicon-list-alt"></i>
							<span class="hidden-mobile hidden-tablet"><?php  _e('Detailed', GoodByeCaptcha::PLUGIN_SLUG) ?></span>
						</a>
					</li>
				</ul>

			</header>

			<div class="no-padding">
				<div class="widget-body" class="tab-content">
					<div class="tab-pane fade active in padding-10 no-padding-bottom" id="s1">
						<div class="row no-space">
							<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
								<div id="chart-container">
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<div class="row">
									<div class="" id = "gdbc-barchart-holder" style = "position: relative; height: 240px;">
									</div>

									<div class="col-xs-6 col-sm-6 col-md-12 col-lg-12 text-justify">
										<p class="text-center pull-right" style="margin: 0">
           
											<a  class="btn btn-rate-gdbc btn-labeled btn-primary" target = "_blank" href = "https://wordpress.org/support/plugin/goodbye-captcha/reviews/">
												<span class="btn-label">
													<i class="glyphicon glyphicon-star" style = "color:#ffff00"></i>
												</span>
												<?php _e('Rate WPBruiser', GoodByeCaptcha::PLUGIN_SLUG); ?>
											</a>
										</p>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
</div>


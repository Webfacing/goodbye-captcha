<?php

foreach(array_keys(GdbcModulesController::getRegisteredModules()) as $moduleName){
	$moduleId = GdbcModulesController::getModuleIdByName($moduleName);

	if (empty($moduleId) || 0 == GdbcDbAccessController::getNumberOfAttemptsByModuleId($moduleId))
		continue;

	?>
		<div class="row">
			<article class="col-sm-12">
				<div id="wid-id-<?php echo $moduleId ?>" class="gdbcwidget clearfix module">
					<header>
						<span class="widget-icon icon-primary"><span class="glyphicon glyphicon-th-list"></span></span>
						<h2><?php echo $moduleName; ?> Attempts</h2>
					</header>
					<div class="no-padding">
						<div class="widget-body" class="tab-content">
							<div class="tpadding-10">
								<div class="row no-space">
									<table class="table table-hover">
										<thead>
											<tr>

											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
									<div class="module-pagination" id="mp-<?php echo $moduleId; ?>" style="text-align: right;margin-right: 20px">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</article>

		</div>

<?php
	}
?>



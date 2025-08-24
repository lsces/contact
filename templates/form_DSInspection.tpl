{strip}
<div class="form contact">
	<div class="header">
		<h1>Monthly Door Supervisor Inspection Report</h1>
	</div>

	<div class="body">
		<form id="testForm" class="form-horizontal" method="post" novalidate="novalidate">
			<fieldset>
				<input id="sig1" name="sig1" type="hidden" value="test1" />
				<input id="sig2" name="sig2" type="hidden" value="test2" />
				<!-- Text input-->
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label class="col-sm-4 control-label" for="DoorSupervisor">Door Supervisor</label>  
						<div class="col-sm-6">
							<input id="DoorSupervisor" name="DoorSupervisor" placeholder="-----" class="form-control input-md" type="text">
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-info" data-toggle="modal" data-target="#DSSig">Signature</button>
							<!-- Modal -->
							<div id="DSSig" class="modal fade" role="dialog">
								<div class="modal-dialog">

									<!-- Modal content-->
									<div id="sigpad1" class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Door Supervisor Signature</h4>
										</div>
										<div class="modal-body">
											<canvas></canvas>
										</div>
										<div class="modal-footer">
											<div class="description">Sign above</div>
											<button id="DSSig-clear" class="button clear" type="button">Clear</button>
											<button id="DSSig-save" class="button save" type="button">Save</button>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="Location">Site/Venue</label>  
						<div class="col-sm-8">
							<input id="Location" name="Location" placeholder="-----" class="form-control input-md" type="text">
						</div>
					</div>
				</div>
				<div class="col-md-6 col-sm-12">
					<div class="form-group">
						<label class="col-sm-4 control-label" for="Supervisor">Supervisor</label>  
						<div class="col-sm-6">
							<input id="Supervisor" name="Supervisor" placeholder="-----" class="form-control input-md" type="text">
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-info" data-toggle="modal" data-target="#SSig">Signature</button>
							<!-- Modal -->
							<div id="SSig" class="modal fade" role="dialog">
								<div class="modal-dialog">

									<!-- Modal content-->
									<div id="sigpad2" class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Supervisor Signature</h4>
										</div>
										<div class="modal-body">
											<canvas></canvas>
										</div>
										<div class="modal-footer">
											<div class="description">Sign above</div>
											<button id="SSig-clear" class="button clear" type="button">Clear</button>
											<button id="SSig-save" class="button save" type="button">Save</button>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="Date">Date</label>  
						<div class="col-sm-8">
							<input id="Date" name="Date" placeholder="-----" class="form-control input-md" type="text">
						</div>
					</div>
				</div>
				<div class="clear"></div>
				<!-- Body Left -->
					<div class="col-md-6 col-sm-12">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Uniform">Uniform</label>  
							<div class="col-sm-8">
								<input id="Uniform" name="Uniform" placeholder="-----" class="form-control input-md" type="text">
							</div>
						</div>
						<!-- Select Badge -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Badge">I.D. Badge</label>
							<div class="col-sm-8">
								<select id="Badge" name="Badge" class="form-control">
									<option value="-">-</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
						<!-- Select SIA Badge -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="SIAB">SIA Badge</label>
							<div class="col-sm-8">
								<select id="SIAB" name="SIAB" class="form-control">
									<option value="-1">-</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
						<!-- Select Alertness -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Alertness">Alertness</label>
							<div class="col-sm-8">
								<select id="Alertness" name="Alertness" class="form-control">
									<option value="-1">-</option>
									<option value="1">1 Bad</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4 Excellent</option>
								</select>
						  </div>
						</div>
						<!-- Select Accuracy -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Accuracy">Site / Venue Log & Timesheets</label>
							<div class="col-sm-8">
								<select id="Accuracy" name="Accuracy" class="form-control">
									<option value="-1">-</option>
									<option value="1">1 Bad</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4 Excellent</option>
								</select>
						  </div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Assign">Assignment Instructions</label>  
							<div class="col-sm-8">
								<input id="Assign" name="Assign" placeholder="placeholder" class="form-control input-md" type="text">
							</div>
						</div>

					</div>
				<!-- Body Right -->
					<div class="col-md-6 col-sm-12">
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Shortage">Site / Venue Box:<br />(Record any forms missing or those which need replenishing)</label>  
							<div class="col-sm-8">
								<textarea class="form-control" id="Shortage" name="Shortage" rows="5">Site Shortages</textarea>
							</div>
						</div>
						<!-- Select Knowledge -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Knowledge">Site / Venue Knowledge</label>
							<div class="col-sm-8">
								<select id="Knowledge" name="Knowledge" class="form-control">
									<option value="-1">-</option>
									<option value="1">1 Bad</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4 Excellent</option>
								</select>
							</div>
						</div>
						<!-- Select Handbook -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Handbook">Employee Handbook</label>
							<div class="col-sm-8">
								<select id="Handbook" name="Handbook" class="form-control">
									<option value="-1">-</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
						<!-- Select Assessment -->
						<div class="form-group">
							<label class="col-sm-4 control-label" for="Assessment">Risk Assessment</label>
							<div class="col-sm-8">
								<select id="Assessment" name="Assessment" class="form-control">
									<option value="-1">-</option>
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>

					</div>
				<div class="clear"></div>
					<!-- Textarea -->
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="textarea">Potential Hazards</label>
						<div class="col-sm-10">                     
							<textarea class="form-control" id="Hazards" name="Hazards" rows="10">Record Identified Hazards</textarea>
						</div>
					</div>
				</div>
				<!-- Button (Double) -->
				<div class="form-group">
					<div class="col-md-2 col-sm-3 col-xs-12">
						<input class="btn btn-primary" type="submit" value="Save" name="fSaveForm">
						<input class="btn pull-right" type="submit" value="Cancel" name="fCancel" formnovalidate>
					</div>
				</div>
			</fieldset>
		</form>
	</div><!-- end .body -->
</div><!-- end .form_page -->
{/strip}


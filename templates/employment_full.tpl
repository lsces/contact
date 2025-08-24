<form id="employmentForm" class="form-horizontal" method="post" action="process.php?for=employment"
  	data-fv-framework="bootstrap"
	data-fv-icon-valid="icon-tick"
	data-fv-icon-invalid="icon-cross"
	data-fv-icon-validating="icon-recycle">

	<input type="hidden" value="form_employment" name="formID" />
	<div class="col-lg-2 col-md-1 hidden-sm hidden-xs">&nbsp;</div>
	<div class="form-all col-lg-8 col-md-10 col-sm-12 col-xs-12">

        <div class="form-header-group">
          <div class="header-text httal htvam">
            <h3 class="form-header" id="header_1">
              Employee Details
            </h3>
          </div>
        </div>

		<div class="form-input-wide" id="cid_18">
			<div class="form-html" id="text_18">
				<p>
					<span style="color: #ff0000;"><strong>This form must be completed
							in full for work to commence and wages to be issued.</strong></span>
				</p>
			</div>
		</div>

 		<div id="name" class="form-group"
			data-toggle="tooltip"
			data-placement="top"
			title="Please provide your full name including any middle names">
			<label class="col-xs-3 control-label">Full name</label>
			<div class="col-xs-2">
				<input type="text" class="form-control" name="name_title" placeholder="Title"
					data-fv-row=".col-xs-2"
					data-fv-notempty="true"
					data-fv-notempty-message="The title is required" />
			</div>
			<div class="col-xs-2">
				<input type="text" class="form-control" name="name_first"
					placeholder="First name" data-fv-row=".col-xs-2"
					data-fv-notempty="true"
					data-fv-notempty-message="The first name is required" />
			</div>
			<div class="col-xs-2">
				<input type="text" class="form-control" name="name_middle"
					placeholder="Middle names" data-fv-row=".col-xs-2"
					data-fv-notempty="false" />
			</div>
			<div class="col-xs-2">
				<input type="text" class="form-control" name="name_last"
					placeholder="Last name" data-fv-row=".col-xs-2"
					data-fv-notempty="true"
					data-fv-notempty-message="The last name is required" />
			</div>
		</div>

	    <div id="name_nino" class="form-group" 
    		data-toggle="tooltip"
    		data-placement="top"
			title="National insurance number should be 2 letters, 6 numbers, 1 letter. You will not be paid if this number is incorrect.">
			<label class="col-xs-3 control-label">National Insurance No.</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" name="name_nino"
					data-fv-notempty="true"
					data-fv-notempty-message="The national insurance number is required"
					pattern="^[a-ceghj-nopr-tw-zA-CEGHJ-NOPR-TW-Z]{ldelim}1{rdelim}[a-ceghj-nopr-tw-zA-CEGHJ-NPR-TW-Z]{ldelim}1{rdelim}\d{ldelim}6{rdelim}[a-dA-D]{ldelim}1{rdelim}$"
					data-fv-regexp-message="The National insurance numbers should be 2 letters, 6 numbers, 1 letter." />
			</div>
		</div>

		<div id="sia1" class="form-group">
			<label class="col-xs-3 control-label">SIA License Number</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" name="sia1"
					data-fv-notempty="true"
					data-fv-notempty-message="SIA License Number is required"
					data-fv-stringlength="true" data-fv-stringlength-min="9"
					data-fv-stringlength-max="9"
					data-fv-stringlength-message="SIA License Number" />
			</div>
		</div>

		<div id="sia2" class="form-group" data-toggle="tooltip"
			data-placement="top"
			title="Use this box if you have second SIA license such as CCTV">
			<label class="col-xs-3 control-label">2nd SIA License Number</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" name="sia2" />
			</div>
		</div>

		<div id="address" class="form-group group" data-toggle="tooltip"
			data-placement="top"
			title="Please supply your current residential address as it appears on the electoral register.">
			<label class="col-xs-3 control-label">Address</label>
			<div>
				<div class="col-xs-6">
					<div class="col-xs-12 form-group">
						<input type="text" class="form-control" name="address_house"
							placeholder="House number or name ( or flat details )" data-fv-row=".col-xs-12"
							data-fv-notempty="true"
							data-fv-notempty-message="The house name or number is required" />
					</div>
					<div class="col-sm-6 col-xs-8 form-group">
						<input type="text" class="address_postcode form-control" name="address_postcode"
							placeholder="Post Code" data-fv-row=".col-xs-8"
							onkeydown="upperPostcode(this)"
							data-fv-notempty="true"
							data-fv-notempty-message="The post code is required"
							pattern="^(([A-Z]\d{ldelim}2{rdelim}[A-Z]{ldelim}2{rdelim})|([A-Z]\d{ldelim}3{rdelim}[A-Z]{ldelim}2{rdelim})|([A-Z]{ldelim}2{rdelim}\d{ldelim}2{rdelim}[A-Z]{ldelim}2{rdelim})|([A-Z]{ldelim}2{rdelim}\d{ldelim}3{rdelim}[A-Z]{ldelim}2{rdelim})|([A-Z]\d[A-Z]\d[A-Z]{ldelim}2{rdelim})|([A-Z]{ldelim}2{rdelim}\d[A-Z]\d[A-Z]{ldelim}2{rdelim})|(GIR0AA))$"
							data-fv-regexp-message="Postcodes should be 1 or 2 letters, 2 or 3 numbers, 2 letters. Please ignore space." />
					</div>
					<div class="col-sm-6 col-xs-4">
						<button type="button" class="btn btn-primary" onclick="update()" />Postcode Lookup</button>
					</div>
					<div class="postcode_detail_reply col-xs-12 panel panel-default group collapse">
						<div id="postcode_detail_match" class="panel-body">
						</div>
					</div>
					<div class="postcode_detail_new col-xs-12 panel panel-default group collapse">
						<div id="postcode_detail_data" class="panel-body">
							<div class="col-xs-12 form-group">
								<input type="text" class="form-control" id="address_street1" name="address_street1"
									placeholder="Street Address" data-fv-row=".col-xs-8"
									data-fv-notempty="false"
									data-fv-notempty-message="The street address is required" />
							</div>
							<div class="col-xs-12 form-group">
								<input type="text" class="form-control" id="address_street2" name="address_street2"
									placeholder="Street Address Line 2" data-fv-row=".col-xs-8"
									data-fv-notempty="false" />
							</div>
							<div class="col-sm-6 col-xs-12 form-group">
								<input type="text" class="form-control" name="address_town"
									placeholder="City / Town" data-fv-row=".col-xs-6"
									data-fv-notempty="false"
									data-fv-notempty-message="The last name is required" />
							</div>
							<div class="col-sm-6 col-xs-12">
								<input type="text" class="form-control"  id="address_county" name="address_county"
									placeholder="County" data-fv-row=".col-xs-4"
									data-fv-notempty="false" />
							</div>
						</div>
						<div class="panel-footer">We are too tight to pay for postcode data updates so can you please supply the details for your postcode as we do not have them!
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="email" class="form-group">
			<label class="col-xs-3 control-label">Email address</label>
			<div class="col-xs-6">
				<input type="text" class="form-control" name="email"
					data-fv-notempty="true"
					data-fv-notempty-message="The email address is required"
					data-fv-emailaddress="true"
					data-fv-emailaddress-message="The input is not a valid email address" />
			</div>
		</div>

		<div id="birthday" class="form-group">
			<label class="col-xs-3 control-label">Date of birth</label>
			<div class="col-xs-3 date">
				<input type="text" class="form-control" id="datebirthday"
					name="birthday" placeholder="DD-MM-YYYY" data-fv-notempty="true"
					data-fv-notempty-message="The date of birth is required"
					data-fv-date="true" data-fv-date-format="DD-MM-YYYY"
					data-fv-date-message="The date of birth is not valid" /> <span
					class="input-group-addon add-on"><span class="icon-calendar"></span></span>
			</div>
		</div>

		<div id="gender" class="form-group">
			<label class="col-xs-3 control-label">Gender</label>
			<div class="col-xs-5">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default"> <input type="radio" name="gender"
						value="male" data-fv-notempty="true"
						data-fv-notempty-message="The gender is required" /> Male
					</label> <label class="btn btn-default"> <input type="radio"
						name="gender" value="female" /> Female
					</label> <label class="btn btn-default"> <input type="radio"
						name="gender" value="other" /> Other
					</label>
				</div>
			</div>
		</div>

		<div id="bank" class="form-group group" data-toggle="tooltip"
			data-placement="top"
			title="Your bank account details are required in order to make you salary transfers.">
			<div id="bank_name" class="form-group">
				<label class="col-xs-3 control-label">Bank Name</label>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="bank_name"
						data-fv-notempty="true"
						data-fv-notempty-message="The bank name is required" />
				</div>
			</div>

			<div id="bank_sort" class="form-group">
				<label class="col-xs-3 control-label">Sort Code</label>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="bank_sort"
						data-fv-notempty="true"
						data-fv-notempty-message="Bank Sort Code is required"
						data-fv-stringlength="true" data-fv-stringlength-min="6"
						data-fv-stringlength-max="6"
						data-fv-stringlength-message="Bank Sort Code is 6 numbers" />
				</div>
			</div>

			<div id="bank_account" class="form-group">
				<label class="col-xs-3 control-label">Account Number</label>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="bank_account"
						data-fv-notempty="true"
						data-fv-notempty-message="Bank Account Number is required"
						data-fv-stringlength="true" data-fv-stringlength-min="8"
						data-fv-stringlength-max="8"
						data-fv-stringlength-message="Bank Account Number is 8 numbers" />
				</div>
			</div>
		</div>

		<div id="student_loan" class="form-group">
			<label class="col-xs-3 control-label">Student Loan</label>
			<div class="col-xs-9">
				<div class="radio">
					<label>Do you have a student loan?</label>
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-default">
							<input type="radio" name="student_loan" value="sl_no" class="select"
								data-fv-notempty="true"
								data-fv-notempty-message="Student Load status is required" />
							No</label>
						<label class="btn btn-default">
							<input type="radio" name="student_loan" value="sl_yes" />
							Yes</label>
					</div>
				</div>
				<div class="loan_detail panel panel-default collapse">
					<div class="panel-body">
						<div class="radio">
							<input type="radio" name="student_loan" value="sl_old" /> <label>
								If you left a course of Higher Education before last 6 April and
								received your first Student Loan instalment on or after 1
								September 1998 and you have not fully repaid your Student Loan,
								then check box.</label>
						</div>
						<div class="radio">
							<input type="radio" name="student_loan" value="sl_new" /> <label>
								New student loan scheme.</label>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="current_circs" class="form-group">
			<label class="col-xs-3 control-label">Your present circumstances</label>
			<div class="col-xs-9">
				<div class="radio">
					<input type="radio" name="current_circs" value="cc_first"
						data-fv-notempty="true"
						data-fv-notempty-message="The present circumstances information is required" />
					<label> This is my first job since last 6 April and I have not been
						receiving taxable Jobseeker's Allowance or taxable Incapacity
						Benefit or a state or occupational pension.</label>
				</div>
				<div class="radio">
					<input type="radio" name="current_circs" value="cc_only" /> <label>
						This is now my only job, but since last 6 April I have had another
						job, or have received taxable Jobseeker's Allowance or Incapacity
						Benefit. I do not receive a state or occupational pension.</label>
				</div>
				<div class="cc_detail panel panel-info collapse">
					<div class="panel-heading">
						<h3 class="panel-title">P45 Details</h3>
					</div>
					<div class="panel-body">
						<div id="left_date" class="form-group">
							<label class="col-xs-3 control-label">Date left previous employment</label>
							<div class="col-xs-3 date">
								<input type="text" class="form-control" id="dateleft"
									name="left_date" placeholder="DD-MM-YYYY"
									data-fv-notempty="true"
									data-fv-notempty-message="The date of leaving previous job is required"
									data-fv-date="true" data-fv-date-format="DD-MM-YYYY"
									data-fv-date-message="The date of certification of details is not valid" />
								<span class="input-group-addon add-on"><span
									class="icon-calendar"></span></span>
							</div>
						</div>
					</div>
					<div id="p45-avail" class="form-group">
						<label class="col-xs-3 control-label">Did your previous employer
							give you a P45?</label>
						<div class="col-xs-3">
							<select id="circs-p45" name="circs-p45-avail" default circs-p45-avail="0"
								class="form-dropdown required form-p45-avail"
								data-fv-row=".col-xs-6" data-fv-notempty="true"
								data-fv-notempty-message="A P45 is required before startin a new job">

								<option selected="" value=""></option>
								<option value="yes">Yes - Please Supply ASAP</option>
								<option value="no">No</option>
							</select>
						</div>
					</div>
					<div id="no_p45" class="form-group">
						<label class="col-xs-3 control-label">If No are you waiting for
							them to send it to you?</label>
						<div class="col-xs-3">
							<select id="circs-no-p45" name="circs-no-p45" default circs-no-p45="0"
								class="form-dropdown required form-p45-avail"
								data-fv-row=".col-xs-6" data-fv-notempty="false"
								data-fv-notempty-message="The country is required">

								<option selected="" value=""></option>
								<option value="yes">Yes - Please Supply ASAP</option>
								<option value="no">No</option>
							</select>
						</div>
					</div>
				</div>
				<div class="radio">
					<input type="radio" name="current_circs" value="cc_other" /> <label>
						I have another job or receive a state or occupational pension.</label>
				</div>
			</div>
		</div>

		<div id="signed_sig" class="form-group">
			<label class="col-xs-3 control-label">Signature</label>
			<div class="col-xs-6 sigPad" id="linear" style="width: 404px;">
				<ul class="sigNav">
					<li class="clearButton"><a href="#clear">Clear</a></li>
				</ul>
				<div class="sig sigWrapper" style="height: auto;">
					<div class="typed"></div>
					<canvas class="pad" width="400" height="250"></canvas>
					<input type="hidden" name="output" class="output">
				</div>
			</div>
		</div>

		<div id="signed_date" class="form-group">
			<label class="col-xs-3 control-label">Signing Date</label>
			<div class="col-xs-3 date">
				<input type="text" class="form-control" id="datesigned"
					name="signed_date" placeholder="DD-MM-YYYY"
					data-fv-notempty="true"
					data-fv-notempty-message="The date of certification of details is required"

					data-fv-date="true"
					data-fv-date-format="DD-MM-YYYY"
					data-fv-date-message="The date of certification of details is not valid" />
				<span class="input-group-addon add-on"><span class="icon-calendar"></span></span>
			</div>
		</div>

		<div id="signed_name" class="form-group">
			<label class="col-xs-3 control-label">Signing name</label>
			<div class="col-xs-3">
				<input type="text" class="form-control" name="sig_first" placeholder="First name"
					data-fv-row=".col-xs-3"
                	data-fv-notempty="true"
                	data-fv-notempty-message="The signed first name is required" />
			</div>
			<div class="col-xs-3">
				<input type="text" class="form-control" name="sig_last" placeholder="Last name"
					data-fv-row=".col-xs-3"
 					data-fv-notempty="true"
 					data-fv-notempty-message="The signed last name is required" />
 			</div>
		</div>

		<div class="form-group">
			<div class="col-xs-9 col-xs-offset-3">
				<button type="submit" class="btn btn-primary" name="signup"
					value="Sign up">Submit</button>
			</div>
		</div>
	</div>
	<div class="col-lg-2 col-md-1 hidden-sm hidden-xs">&nbsp;</div>
</form>
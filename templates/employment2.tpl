{strip}
<form id="employmentForm" class="form-horizontal" method="post" action="process.php?form=employment"
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

		{foreach name=template_loop item=item from=$formInfo}
			{include file="bitpackage:form/`$item.form_template`.tpl" name=$gContent->mInfo.name}
		{/foreach}

		<div class="form-group">
			<div class="col-xs-9 col-xs-offset-3">
				<button type="submit" class="btn btn-primary" name="signup"
					value="Sign up">Submit</button>
				<button type="cancel" class="btn btn-primary" name="fCancel"
					value="fCancel">Cancel</button>
			</div>
		</div>
	</div>
	<div class="col-lg-2 col-md-1 hidden-sm hidden-xs">&nbsp;</div>
</form>
{/strip}
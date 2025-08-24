<form accept-charset="utf-8" id="form_employment" name="form_employment" method="post" action="process.php?for=employment" class="form" novalidate="true">
  <input type="hidden" value="form_employment" name="formID">
  <div class="col-lg-2 col-md-1 hidden-sm hidden-xs">&nbsp;</div>
  <div class="form-all col-lg-8 col-md-10 col-sm-12 col-xs-12">
    <ul class="form-section page-section">
      <li data-type="control_head" class="form-input-wide" id="cid_1">
        <div class="form-header-group">
          <div class="header-text httal htvam">
            <h3 class="form-header" id="header_1">
              Employee Details
            </h3>
          </div>
        </div>
      </li>
      <li id="id_18" data-type="control_text" class="form-line">
        <div class="form-input-wide" id="cid_18">
          <div class="form-html" id="text_18">
            <p><span style="color:#ff0000;"><strong>This form must be completed in full for work to commence and wages to be issued.</strong></span></p>
          </div>
        </div>
      </li>
      <li id="id_3" data-type="control_fullname" class="form-line required" data-toggle="tooltip" data-placement="top" title="Please provide your full name including any middle name">
        <label for="input_3" id="label_3" class="form-label form-label-left form-label-auto">
          Full Name
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_3">
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="prefix_3" size="4" name="q3_name[prefix]" class="form-textbox">
            <label style="min-height: 13px;" id="sublabel_prefix" for="prefix_3" class="form-sub-label"> Prefix </label>
          </span>
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="first_3" name="q3_name[first]" size="10" class="form-textbox validate[required]">
            <label style="min-height: 13px;" id="sublabel_first" for="first_3" class="form-sub-label"> First Name </label>
          </span>
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="middle_3" name="q3_name[middle]" size="10" class="form-textbox">
            <label style="min-height: 13px;" id="sublabel_middle" for="middle_3" class="form-sub-label"> Middle Name </label>
          </span>
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="last_3" name="q3_name[last]" size="15" class="form-textbox validate[required]">
            <label style="min-height: 13px;" id="sublabel_last" for="last_3" class="form-sub-label"> Last Name </label>
          </span>
        </div>
	  </li>
      <li id="id_4" class="form-line required" data-toggle="tooltip" data-placement="top" title="National insurance number should be 2 letters, 6 numbers, 1 letter. You will not be paid if this number is incorrect.">
        <label for="input_4" id="label_4" class="form-label form-label-left form-label-auto">
          National Insurance No.
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_4">
          <input type="text" maxlength="9" value="" size="15" name="q4_nationalInsurance4" id="input_4" data-type="input-textbox" class=" form-textbox validate[required, AlphaNumeric]" placeholder="AA123456C">
        </div>
      </li>
      <li id="id_19" class="form-line">
        <label for="input_19" id="label_19" class="form-label form-label-left form-label-auto"> SIA License Number </label>
        <div class="form-input required" id="cid_19">
          <input type="text" value="" size="20" name="q19_siaLicense19" id="input_19" data-type="input-textbox" class=" form-textbox">
        </div>
      </li>
      <li id="id_22" class="form-line" data-toggle="tooltip" data-placement="top" title="Use this box if you have second SIA license such as CCTV">
        <label for="input_22" id="label_22" class="form-label form-label-left form-label-auto"> 2nd SIA License Number </label>
        <div class="form-input required" id="cid_22">
          <input type="text" value="" size="20" name="q22_2ndSia" id="input_22" data-type="input-textbox" class=" form-textbox">
        </div>
      </li>
      <li id="id_5" data-type="control_address" class="form-line required">
        <label for="input_5" id="label_5" class="form-label form-label-left form-label-auto">
          Address
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_5">
          <table cellspacing="0" cellpadding="0" border="0" class="form-address-table" undefined="" summary="">
            <tbody><tr>
              <td colspan="2">
                <span style="vertical-align: top" class="form-sub-label-container">
                  <input type="text" id="input_5_addr_line1" name="q5_address[addr_line1]" class="form-textbox validate[required] form-address-line">
                  <label style="min-height: 13px;" id="sublabel_5_addr_line1" for="input_5_addr_line1" class="form-sub-label"> Street Address </label>
                </span>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <span style="vertical-align: top" class="form-sub-label-container">
                  <input type="text" size="46" id="input_5_addr_line2" name="q5_address[addr_line2]" class="form-textbox form-address-line">
                  <label style="min-height: 13px;" id="sublabel_5_addr_line2" for="input_5_addr_line2" class="form-sub-label"> Street Address Line 2 </label>
                </span>
              </td>
            </tr>
            <tr>
              <td width="50%">
                <span style="vertical-align: top" class="form-sub-label-container">
                  <input type="text" size="21" id="input_5_city" name="q5_address[city]" class="form-textbox validate[required] form-address-city">
                  <label style="min-height: 13px;" id="sublabel_5_city" for="input_5_city" class="form-sub-label"> City / Town </label>
                </span>
              </td>
              <td>
                <span style="vertical-align: top" class="form-sub-label-container">
                  <input type="text" size="22" id="input_5_state" name="q5_address[state]" class="form-textbox validate[required] form-address-state">
                  <label style="min-height: 13px;" id="sublabel_5_state" for="input_5_state" class="form-sub-label"> County </label>
                </span>
              </td>
            </tr>
            <tr>
              <td width="50%">
                <span style="vertical-align: top" class="form-sub-label-container">
                  <input type="text" size="10" id="input_5_postal" name="q5_address[postal]" class="form-textbox validate[required] form-address-postal">
                  <label style="min-height: 13px;" id="sublabel_5_postal" for="input_5_postal" class="form-sub-label"> Post Code </label>
                </span>
              </td>
              <td>
                <span style="vertical-align: top" class="form-sub-label-container">
                  <select id="input_5_country" name="q5_address[country]" defaultcountry="0" class="form-dropdown validate[required] form-address-country">
                    <option selected="" value=""> Please Select </option>
                    <option value="England"> England </option>
                    <option value="Scotland"> Scotland </option>
                    <option value="Wales"> Wales </option>
                    <option value="Northern Ireland"> Northern Ireland </option>
                    <option value="Isle of Man"> Isle of Man </option>
                    <option value="Guernsey"> Guernsey </option>
                    <option value="Jersey"> Jersey </option>
                    <option value="Gibraltar"> Gibraltar </option>
                    <option value="United Kingdom"> United Kingdom </option>
                    <option value="other"> Other </option>
                  </select>
                  <label style="min-height: 13px;" id="sublabel_5_country" for="input_5_country" class="form-sub-label"> Country </label>
                </span>
              </td>
            </tr>
          </tbody></table>
        </div>
      </li>
      <li id="id_6" data-type="control_email" class="form-line">
        <label for="input_6" id="label_6" class="form-label form-label-left form-label-auto"> E-mail </label>
        <div class="form-input required" id="cid_6">
          <input type="email" value="" size="50" name="q6_email" id="input_6" class=" form-textbox validate[Email]">
        </div>
      </li>
      <li id="id_9" data-type="control_widget" class="form-line required">
        <label for="input_9" id="label_9" class="form-label form-label-left form-label-auto">
          Date Of Birth
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_9">
          <div style="width:100%; text-align:Left;">
            <div class="widget-inputs-wrapper">
              <input type="hidden" value="" name="q9_dateOf9" class="form-hidden form-widget widget-required " id="input_9">
            </div>
          </div>
        </div>
      </li>
      <li id="id_8" data-type="control_radio" class="form-line required">
        <label for="input_8" id="label_8" class="form-label form-label-left form-label-auto">
          Gender
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_8">
          <div class="form-single-column">
            <span style="clear:left;" class="form-radio-item">
              <span class="dragger-item">
              </span>
              <input type="radio" value="Male" name="q8_gender" id="input_8_0" class="form-radio validate[required]">
              <label for="input_8_0" id="label_input_8_0"> Male </label>
            </span>
            <span style="clear:left;" class="form-radio-item">
              <span class="dragger-item">
              </span>
              <input type="radio" value="Female" name="q8_gender" id="input_8_1" class="form-radio validate[required]">
              <label for="input_8_1" id="label_input_8_1"> Female </label>
            </span>
          </div>
        </div>
      </li>
      <li id="id_23" class="form-line required">
        <label for="input_23" id="label_23" class="form-label form-label-left form-label-auto">
          Bank Name
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_23">
          <input type="text" value="" size="20" name="q23_bankName" id="input_23" data-type="input-textbox" class=" form-textbox validate[required]">
        </div>
      </li>
      <li id="id_24" class="form-line required">
        <label for="input_24" id="label_24" class="form-label form-label-left form-label-auto">
          Sort Code
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_24">
          <input type="text" maxlength="6" value="" size="20" name="q24_sortCode" id="input_24" data-type="input-textbox" class=" form-textbox validate[required]">
        </div>
      </li>
      <li id="id_25" class="form-line required">
        <label for="input_25" id="label_25" class="form-label form-label-left form-label-auto">
          Account Number
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_25">
          <input type="text" maxlength="8" value="" size="20" name="q25_accountNumber" id="input_25" data-type="input-textbox" class=" form-textbox validate[required]">
        </div>
      </li>
      <li id="id_11" data-type="control_checkbox" class="form-line">
        <label for="input_11" id="label_11" class="form-label form-label-left form-label-auto"> Student Loan </label>
        <div class="form-input required" id="cid_11">
          <div class="form-single-column">
            <span style="clear:left;" class="form-checkbox-item">
              <span class="dragger-item">
              </span>
              <input type="checkbox" value="If you left a course of Higher Education before last 6 April and received your first Student Loan    instalment on or after 1 September 1998 and you have not fully repaid your Student Loan, then check box." name="q11_studentLoan[]" id="input_11_0" class="form-checkbox">
              <label for="input_11_0" id="label_input_11_0"> If you left a course of Higher Education before last 6 April and received your first Student Loan instalment on or after 1 September 1998 and you have not fully repaid your Student Loan, then check box. </label>
            </span>
          </div>
        </div>
      </li>
      <li id="id_12" data-type="control_radio" class="form-line required">
        <label for="input_12" id="label_12" class="form-label form-label-left form-label-auto">
          Your present circumstances
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_12">
          <div class="form-single-column">
            <span style="clear:left;" class="form-radio-item">
              <span class="dragger-item">
              </span>
              <input type="radio" value="This is my first job since last 6 April and I have not been receiving taxable Jobseeker's Allowance or taxable Incapacity Benefit or a state or occupational pension.  1" name="q12_yourPresent" id="input_12_0" class="form-radio validate[required]">
              <label for="input_12_0" id="label_input_12_0"> This is my first job since last 6 April and I have not been receiving taxable Jobseeker's Allowance or taxable Incapacity Benefit or a state or occupational pension. 1 </label>
            </span>
            <span style="clear:left;" class="form-radio-item">
              <span class="dragger-item">
              </span>
              <input type="radio" value="This is now my only job, but since last 6 April I have had another job, or have received taxable Jobseeker's Allowance or Incapacity Benefit. I do not receive a state or occupational pension." name="q12_yourPresent" id="input_12_1" class="form-radio validate[required]">
              <label for="input_12_1" id="label_input_12_1"> This is now my only job, but since last 6 April I have had another job, or have received taxable Jobseeker's Allowance or Incapacity Benefit. I do not receive a state or occupational pension. </label>
            </span>
            <span style="clear:left;" class="form-radio-item">
              <span class="dragger-item">
              </span>
              <input type="radio" value="I have another job or receive a state or occupational pension." name="q12_yourPresent" id="input_12_2" class="form-radio validate[required]">
              <label for="input_12_2" id="label_input_12_2"> I have another job or receive a state or occupational pension. </label>
            </span>
          </div>
        </div>
      </li>
      <li id="id_26" data-type="control_widget" style="display:none;" class="form-line form-field-hidden">
        <label for="input_26" id="label_26" class="form-label form-label-left form-label-auto"> Date left previous employment </label>
        <div class="form-input required" id="cid_26">
          <div style="width:100%; text-align:Left;">
            <div class="widget-inputs-wrapper">
              <input type="hidden" value="" name="q26_dateLeft" class="form-hidden form-widget  " id="input_26">
            </div>
          </div>
        </div>
      </li>
      <li id="id_27" data-type="control_dropdown" style="display:none;" class="form-line form-field-hidden">
        <label for="input_27" id="label_27" class="form-label form-label-left form-label-auto"> Did your previous employer give you a P45? </label>
        <div class="form-input required" id="cid_27">
          <select name="q27_didYour27" id="input_27" style="width:150px" class="form-dropdown">
            <option value="">  </option>
            <option value="Yes - Please Supply ASAP"> Yes - Please Supply ASAP </option>
            <option value="No"> No </option>
          </select>
        </div>
      </li>
      <li id="id_28" data-type="control_dropdown" style="display:none;" class="form-line form-field-hidden">
        <label for="input_28" id="label_28" class="form-label form-label-left form-label-auto"> If No are you waiting for them to send it to you? </label>
        <div class="form-input required" id="cid_28">
          <select name="q28_ifNo28" id="input_28" style="width:150px" class="form-dropdown">
            <option value="">  </option>
            <option value="Yes - Please send once received "> Yes - Please send once received </option>
            <option value="No"> No </option>
          </select>
        </div>
      </li>
      <li id="id_15" data-type="control_signature" class="form-line">
        <label for="input_15" id="label_15" class="form-label form-label-left form-label-auto"> Signature </label>
        <div class="form-input required" id="cid_15">
          <div style="width:302px;height:202px;" class="signature-pad-wrapper" id="signature_pad_15">
            <!--[if IE 7]>
              <script src="/js/vendor/json2.js"></script>
            <![endif]-->
            <div style="width:302px;height:202px;" class="signature-line signature-wrapper">
              <div class="pad" data-required="false" data-id="15" data-height="200" data-width="300" id="sig_pad_15" style="width: 300px; height: 200px;">
              <div style="padding:0 !important; margin:0 !important;width: 100% !important; height: 0 !important; -ms-touch-action: none;margin-top:-1em !important; margin-bottom:1em !important;"></div><canvas style="margin: 0px; padding: 0px; border: medium none; height: 200px; width: 300px;" class="jSignature" width="300" height="200"></canvas><div style="padding:0 !important; margin:0 !important;width: 100% !important; height: 0 !important; -ms-touch-action: none;margin-top:-1.5em !important; margin-bottom:1.5em !important; position: relative;"></div></div>
              <input type="hidden" id="sig_pad_15" class="output4" name="q15_signature">
            </div>
            <span class="clear-pad-btn clear-pad">
              Clear
            </span>
          </div>
          <script>
          window.signatureForm = true
          </script>
        </div>
      </li>
      <li id="id_16" data-type="control_widget" class="form-line required">
        <label for="input_16" id="label_16" class="form-label form-label-left form-label-auto">
          Signing Date
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_16">
          <div style="width:100%; text-align:Left;">
            <div class="widget-inputs-wrapper">
              <input type="hidden" value="" name="q16_signingDate" class="form-hidden form-widget widget-required " id="input_16">
            </div>
            <script>
            </script>
          </div>
        </div>
      </li>
      <li id="id_17" data-type="control_fullname" class="form-line required">
        <label for="input_17" id="label_17" class="form-label form-label-left form-label-auto">
          Signing Name
          <span class="form-required">
            *
          </span>
        </label>
        <div class="form-input required" id="cid_17">
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="first_17" name="q17_signingName[first]" size="10" class="form-textbox validate[required]">
            <label style="min-height: 13px;" id="sublabel_first" for="first_17" class="form-sub-label"> First Name </label>
          </span>
          <span style="vertical-align: top" class="form-sub-label-container">
            <input type="text" id="last_17" name="q17_signingName[last]" size="15" class="form-textbox validate[required]">
            <label style="min-height: 13px;" id="sublabel_last" for="last_17" class="form-sub-label"> Last Name </label>
          </span>
        </div>
      </li>
      <li id="id_21" data-type="control_widget" class="form-line">
        <label for="input_21" id="label_21" class="form-label form-label-left form-label-auto"> Portrait Photo </label>
        <div class="form-input required" id="cid_21">
          <div style="width:100%; text-align:Left;">
            <div class="widget-inputs-wrapper">
              <input type="hidden" value="" name="q21_portraitPhoto" class="form-hidden form-widget  " id="input_21">
            </div>

          </div>
        </div>
      </li>
      <li id="id_2" data-type="control_button" class="form-line">
        <div class="form-input-wide" id="cid_2">
          <div class="form-buttons-wrapper" style="margin-left:156px">
            <button class="form-submit-button" type="submit" id="input_2">
              Submit
            </button>
       </div>
      </li>
      <li style="display:none">
        Should be Empty:
        <input type="text" value="" name="website">
      </li>
    </ul>
  </div>
  <div class="col-lg-2 col-md-1 hidden-sm hidden-xs">&nbsp;</div>
</form>
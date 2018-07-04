<div class="crm-block crm-form-block crm-logretention-form-block">
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
 
<fieldset>
    <table class="form-layout">
        <tr class="crm-logretention-form-price_fields">
          <td class="label">{$form.price_fields.label}</td>
          <td>
            {$form.price_fields.html}
          </td>
        </tr>
        
        <tr class="crm-logretention-form-late_fees">
          <td class="label">{$form.late_fees.label}</td>
          <td>
            {$form.late_fees.html}
          </td>
        </tr>
        
         <tr class="crm-logretention-form-days_prior">
          <td class="label">{$form.days_prior.label}</td>
          <td>
            {$form.days_prior.html}
          </td>
        </tr>
   </table>
 
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</fieldset>
 
</div>
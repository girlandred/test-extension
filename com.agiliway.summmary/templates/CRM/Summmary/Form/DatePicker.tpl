<div>
  <div> 
    <div class="label">{$form.start_date.label}</div>
    <div class="view-value">{$form.start_date.html}</div>
  </div>

  <div>
     <div class="label">{$form.end_date.label}  </div>
    <div class="view-value">{$form.end_date.html}  </div>
  </div>

</div>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

  <div class="crm-block crm-form-block">
    {foreach from=$startResults item=value key=k}
    <p>
    Quantity of items created in entity '{$k}' : {$value} <br>
    </p>
    {/foreach}
  </div>



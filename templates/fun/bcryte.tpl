<label for="ret">{nocache}{$val}{/nocache}</label>
<p id="ret" {nocache}{if isset($err)}class="alert-danger"{/if}{/nocache}>{nocache}{if isset($err)}{$err}{else}{$ret}{/if}{/nocache}</p>
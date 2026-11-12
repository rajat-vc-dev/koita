@php
  $honeypotField = \App\Support\Honeypot::field();
@endphp
{{-- Hidden from people; bots that auto-fill every input will trip server validation. --}}
<div class="hp-trap" aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
  <label for="{{ $honeypotField }}">Fax</label>
  <input type="text"
         name="{{ $honeypotField }}"
         id="{{ $honeypotField }}"
         value=""
         tabindex="-1"
         autocomplete="off">
</div>

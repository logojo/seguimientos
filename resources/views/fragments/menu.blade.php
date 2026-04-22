
@unlessrole('invitado')
<div>
   <input id="menu" type="checkbox" class="drawer-toggle" />
 
   <label for="menu" class="btn btn-sm btn-ghost drawer-button text-white hover:text-primary mr-2">
      <span class="material-symbols-outlined">menu</span>
   </label>

  <div class="drawer-side">
    <label for="menu" aria-label="close sidebar" class="drawer-overlay"></label>
    
   
    {{-- TODO: CREAR MENU --}}

  </div>
</div>
@endunlessrole
@props(['component', 'hasOptions' => true, 'acciones' => 'Acciones'])

@php
 $id = $id ?? md5($attributes->wire('model'));
@endphp

<section>

    <div class="px-6  min-w-full ">
        {{ $actions }}
    </div>

    <div class="overflow-x-auto mt-5">
      <table class="table table-xs md:table-md">
        <!-- head -->
        <thead>
          <tr>
            {{ $columns }}    
            @if($hasOptions)  
              <th class="uppercase">{{ $acciones }}</th>
            @endif
          </tr>
        </thead>
        <tbody>
          {{ $rows }}
        </tbody>
        <tfoot>
          {{ $footer }}
        </tfoot>
      </table>
    </div>
</section>
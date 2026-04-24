<div class="px-5">
   <section class="max-w-full mx-auto px-6 lg:px-8 xl:px-10 pt-8">
    <h1 class="text-center text-3xl font-patria text-primary">Seguimiento a Programas para la Igualdad</h1>

    <x-divider>
        <x-slot name="title">Listado de Actividades</x-slot>
    </x-divider>

    <section class="my-10">
        <div class="flex justify-end gap-2 mb-8 ">
            @include('livewire.pages.seguimientos.form-seguimientos')
        </div>
        <livewire:pages.seguimientos.seguimientos-table />
    </section>

   </section>
</div>

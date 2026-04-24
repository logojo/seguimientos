<tr>
    <td colspan="{{ count($this->visibleColumns()) }}" class="text-center py-16">
        <div class="flex flex-col items-center gap-3 text-base-content/40">
            <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4z"/>
            </svg>
            <p class="text-sm font-medium">Sin resultados</p>
            <button wire:click="clearFilters" class="btn btn-ghost btn-xs">
                Limpiar filtros
            </button>
        </div>
    </td>
</tr>
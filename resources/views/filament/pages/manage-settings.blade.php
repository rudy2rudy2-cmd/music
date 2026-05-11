<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
             <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </div>
    </form>

    <div class="mt-8 p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
        <h3 class="text-lg font-bold text-indigo-900 mb-2">How to apply branding?</h3>
        <p class="text-indigo-700 text-sm">
            Changing the primary color will update the dashboard highlights.
            The logo will be displayed in the top-left corner of the public site and admin panel.
        </p>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </form>

    @if($qrCodeUrl)
        <div class="mt-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold mb-4">Two-Factor Authentication QR Code</h3>
            <p class="mb-4 text-gray-600 text-sm">Scan this QR code with your Google Authenticator app to set up 2FA.</p>
            <div class="flex justify-center bg-gray-50 p-4 rounded-lg">
                {{-- In a real app we'd use a QR code generator package here --}}
                <div class="p-4 bg-white border border-gray-300 rounded shadow-inner">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code">
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

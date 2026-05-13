<x-mail::message>
# Thank you for your purchase!

Hi {{ $license->user->name }},

You have successfully purchased **{{ $license->platform->name }}**.

Your license key is:
<x-mail::panel>
{{ $license->license_key }}
</x-mail::panel>

You can download your product and manage your licenses from your dashboard.

<x-mail::button :url="route('dashboard')">
Go to Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

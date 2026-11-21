@component('mail::message')
@if (! empty($greeting))
# {{ $greeting }}
@endif

@foreach ($introLines as $line)
{{ $line }}

@endforeach

@isset($actionText)
@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent
@endisset

@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{ $salutation ?? 'Regards,' }}<br>
{{ config('app.name') }}

@isset($actionText)
@component('mail::subcopy')
If you're having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endcomponent

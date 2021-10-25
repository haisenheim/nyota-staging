@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
{{trans('emails.resetgreeting')}}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@if (isset($actionText))
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endif

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

<!-- Salutation -->
@if (! empty($salutation))
NYOTA, the first marketplace
@else
NYOTA, the first marketplace
@endif

<!-- Subcopy -->
@if (isset($actionText))
@component('mail::subcopy') 
{{trans('emails.linkbefore')}} "{{ $actionText }}" {{trans('emails.linkafter')}}: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endif
@endcomponent

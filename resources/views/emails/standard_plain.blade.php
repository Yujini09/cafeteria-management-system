{{ $fullSubject }}

{{ str_repeat('=', strlen($fullSubject)) }}

{{ $greeting }}

{{ $title }}

@foreach ($introLines as $line)
{{ $line }}

@endforeach
@if ($details)
Details
-------
@foreach ($details as $label => $value)
- {{ $label }}: {{ $value }}
@endforeach

@endif
@foreach ($sections as $section)
{{ $section['title'] }}
{{ str_repeat('-', strlen($section['title'])) }}
@if ($section['is_list'])
@foreach ($section['content'] as $item)
- {{ $item }}
@endforeach
@else
{{ $section['content'] }}
@endif

@endforeach
@if ($action)
{{ $action['text'] }}: {{ $action['url'] }}

@endif
@foreach ($outroLines as $line)
{{ $line }}

@endforeach
This email was sent by {{ $appName }}.

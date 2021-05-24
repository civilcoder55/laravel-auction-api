@component('mail::message')
# Hello {{ $name }}

{{ $head }}

## Auction Details

- <strong>Title</strong> : {{ $title }}
- <strong>Status</strong> : CLOSED
- <strong>Created At</strong> : {{ $created_at }}
- <strong>Ended At</strong> : {{ $ending_at }}
- <strong>Highest Bid</strong> : {{ $bid }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
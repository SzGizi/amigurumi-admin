<h1>{{ $pattern['title'] }}</h1>

@if(!empty($pattern['yarn_description']))
  <h3>{{ __('Yarn') }}</h3>
  <p>{{ $pattern['yarn_description'] }}</p>
@endif

@if(!empty($pattern['tools_description']))
  <h3>{{ __('Tools') }}</h3>
  <p>{{ $pattern['tools_description'] }}</p>
@endif

@foreach($pattern['sections'] as $section)
  <h2>{{ $section['title'] }}</h2>
  <ul>
    @foreach($section['rows'] as $row)
      <li>
        <strong>{{ $row['row_number'] }}:</strong>
        {{ $row['instructions'] }}
        @if(!empty($row['stitch_number']))
           <em>({{ $row['stitch_number'] }})</em>
        @endif
        @if(!empty($row['comment']))
          | <em>{{ $row['comment'] }}</em>
        @endif
      </li>
    @endforeach
  </ul>
@endforeach

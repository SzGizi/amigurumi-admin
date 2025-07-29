<h1>{{ $pattern['title'] }}</h1>

{{-- Főkép --}}
@if(!empty($pattern['main_image_base64']))
  <img src="{!! $pattern['main_image_base64'] !!}" alt="Main image" style="max-width: 300px;" />
@endif

@if(!empty($pattern['yarn_description']))
  <h3>{{ __('Yarn') }}</h3>
  <p>{{ $pattern['yarn_description'] }}</p>
@endif

@if(!empty($pattern['tools_description']))
  <h3>{{ __('Tools') }}</h3>
  <p>{{ $pattern['tools_description'] }}</p>
@endif

{{-- Többi kép --}}
@foreach($pattern['images'] as $image)
  @if(!empty($image['base64']))
    <img src="{!! $image['base64'] !!}" alt="Pattern image" style="max-width: 150px;" />
    @if(!empty($image['caption']))
      <span>{{ $image['caption'] }}</span>
    @endif
    
  @endif
@endforeach

{{-- Szekciók --}}
@foreach($pattern['sections'] as $section)
  <h2>{{ $section['title'] }}</h2>

  {{-- Csak az adott szekció képei --}}
  @if(!empty($section['images']))
    @foreach($section['images'] as $img)
      @if(!empty($img['base64']))
        <img src="{!! $img['base64'] !!}" alt="Section image" style="max-width: 120px;" />
         @if(!empty($img['caption']))
          <span>{{ $img['caption'] }}</span>
        @endif
      @endif
    @endforeach
  @endif

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

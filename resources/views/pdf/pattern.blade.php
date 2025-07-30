<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $pattern['title'] }}</title>
  @if(!empty($pattern['css_content']))
    <style>
      {!! $pattern['css_content'] !!}
    </style>
  @endif

</head>
<body>


  {{-- 🟣 Első oldal: csak cím és főkép --}}
  <h1>{{ $pattern['title'] }}</h1>
  <div>
    @if(!empty($pattern['main_image_base64']))
      <img src="{!! $pattern['main_image_base64'] !!}" class="cover-image" alt="Main image">
    @endif
  </div>


  {{-- 🧾 Oldaltörés --}}
  <div class="page-break"></div>

  {{-- 🧶 Fonal és eszközök --}}
  @if(!empty($pattern['yarn_description']))
    <h3>{{ __('Yarn') }}</h3>
    <p>{{ $pattern['yarn_description'] }}</p>
  @endif

  @if(!empty($pattern['tools_description']))
    <h3>{{ __('Tools') }}</h3>
    <p>{{ $pattern['tools_description'] }}</p>
  @endif

  {{-- 🖼️ Egyéb képek --}}
  @if(!empty($pattern['images']))
    <h3>{{ __('Images') }}</h3>
    <div class="image-gallery">
      @foreach($pattern['images'] as $image)
        @if(!empty($image['base64']))
          <img src="{!! $image['base64'] !!}" alt="Pattern image">
          @if(!empty($image['caption']))
            <span class="caption">{{ $image['caption'] }}</span>
          @endif
        @endif
      @endforeach
    </div>
  @endif

  {{-- 📦 Szekciók --}}
  @foreach($pattern['sections'] as $section)
    <h2 class="section-title">{{ $section['title'] }}</h2>

    {{-- Szekció képei --}}
    @if(!empty($section['images']))
      <div class="image-gallery">
        @foreach($section['images'] as $img)
          @if(!empty($img['base64']))
            <img src="{!! $img['base64'] !!}" alt="Section image">
            @if(!empty($img['caption']))
              <span class="caption">{{ $img['caption'] }}</span>
            @endif
          @endif
        @endforeach
      </div>
    @endif

    {{-- Sorok listája --}}
    <ul class="rows">
      @foreach($section['rows'] as $row)
        <li>
          <strong>{{ $row['row_number'] }}:</strong>
          {{ $row['instructions'] }}
          @if(!empty($row['stitch_number']))
            <em> ({{ $row['stitch_number'] }})</em>
          @endif
          @if(!empty($row['comment']))
            | <em>{{ $row['comment'] }}</em>
          @endif
        </li>
      @endforeach
    </ul>
     @endforeach
 
</body>
</html>


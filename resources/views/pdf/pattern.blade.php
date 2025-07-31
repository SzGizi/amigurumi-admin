<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <title>{{ $pattern['title'] ?? 'Amigurumi minta' }}</title>

    {{-- CSS fájlok helyi eléréssel --}}
    @if(!empty($pattern['css_files']) && is_array($pattern['css_files']))
        @foreach($pattern['css_files'] as $cssFile)
            <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path($cssFile)) }}" />
        @endforeach
    @else
        {{-- Ha nincs megadva css_files tömb, legalább az alap css --}}
        <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path('css/pdf-pattern.css')) }}" />
    @endif

</head>
<body>

<div class="container">

  {{-- Cím és fő kép --}}
  <h1>{{ $pattern['title'] ?? '' }}</h1>
  @if(!empty($pattern['main_image_url']))
    <div class="main-image">
      <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($pattern['main_image_url']))) }}" alt="Főkép" style="max-width:100%;" />
    </div>
  @endif

  <div class="page-break"></div>

  {{-- Fonal és eszközök --}}
  @if(!empty($pattern['yarn_description']))
    <h3>{{ __('Yarn') }}</h3>
    <p>{{ $pattern['yarn_description'] }}</p>
  @endif

  @if(!empty($pattern['tools_description']))
    <h3>{{ __('Tools') }}</h3>
    <p>{{ $pattern['tools_description'] }}</p>
  @endif

  {{-- Egyéb képek --}}
  @if(!empty($pattern['images']) && is_array($pattern['images']))
    <h3>{{ __('Images') }}</h3>
    <div class="image-gallery">
      @foreach($pattern['images'] as $image)
        @if(!empty($image['url']))
          <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($image['url']))) }}" alt="Minta kép" style="max-width:100%;" />
          @if(!empty($image['caption']))
            <div class="caption">{{ $image['caption'] }}</div>
          @endif
        @endif
      @endforeach
    </div>
  @endif

  {{-- Szekciók --}}
  @if(!empty($pattern['sections']) && is_array($pattern['sections']))
    @foreach($pattern['sections'] as $section)
      <div class="section">
        <h3 class="section-title">{{ $section['title'] ?? '' }}</h3>

        @if(!empty($section['images']) && is_array($section['images']))
          <div class="images-row">
            @foreach($section['images'] as $img)
              @if(!empty($img['url']))
                <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($img['url']))) }}" alt="Szekció kép" style="max-width:100%;" />
                @if(!empty($img['caption']))
                  <div class="image-caption">{{ $img['caption'] }}</div>
                @endif
              @endif
            @endforeach
          </div>
        @endif

        @if(!empty($section['rows']) && is_array($section['rows']))
          <ul class="rows-list">
            @foreach($section['rows'] as $row)
              <li>
                <strong>{{ $row['row_number'] ?? '' }}:</strong> {{ $row['instructions'] ?? '' }}
                @if(!empty($row['stitch_number'])) <em>({{ $row['stitch_number'] }})</em> @endif
                @if(!empty($row['comment'])) | <em>{{ $row['comment'] }}</em> @endif
              </li>
            @endforeach
          </ul>
        @endif

      </div>
    @endforeach
  @endif

</div>

</body>
</html>

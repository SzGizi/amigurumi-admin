<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $pattern['title'] }}</title>
  <style>
  @page {
    margin: 25mm 20mm;
    footer: myfooter;
    }
  }
 

  body {
    font-family: 'DejaVu Sans', sans-serif;
    color: #333;
    font-size: 12.5px;
    line-height: 1.6;
    background-color: #fff;
  }

  h1, h2, h3 {
    color: #d95478;
    margin-top: 0;
    margin-bottom: 12px;
    font-weight: normal;
  }

  h1 {
    text-align: center;
    font-size: 28px;
    margin-top: 80px;
  }

  h2 {
    font-size: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 4px;
    margin-top: 30px;
  }

  h3 {
    font-size: 16px;
    margin-top: 20px;
  }

  p {
    margin-bottom: 10px;
  }

  .cover-image {
    display: block;
    position: relative;
    margin: 30px auto 0;
    max-width: 320px;
    border: 3px solid #f6dbe1;
    padding: 4px;
    border-radius: 6px;
  }

  .section-title {
    font-size: 18px;
    color: #444;
    margin-top: 25px;
  }

  .image-gallery {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 10px;
    margin-top: 10px;
  }

  .image-gallery img {
    max-width: 140px;
    border-radius: 4px;
    border: 1px solid #ccc;
  }

  .caption {
    text-align: center;
    font-size: 11px;
    color: #666;
    margin-top: 3px;
    margin-bottom: 10px;
  }

  .page-break {
    page-break-after: always;
  }

  ul.rows {
    list-style-type: none;
    padding-left: 0;
    margin-top: 10px;
  }

  ul.rows li {
    padding: 6px 10px;
    border-bottom: 1px dotted #ddd;
    margin-bottom: 3px;
  }

  ul.rows li strong {
    color: #000;
  }

  em {
    font-style: italic;
    color: #666;
  }

  .footer {
    position: fixed;
    bottom: 20px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10px;
    color: #aaa;
  }

  .title-page {
    text-align: center;
    padding-top: 50px;
  }


  </style>
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
  <div class="page-break"></div>
   @for($i = 0; $i < 80; $i++)
    <p>Teszt sor {{ $i }}</p>
  @endfor

</body>
</html>


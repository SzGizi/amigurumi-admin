@php
    $primaryColor = '#54d95f'; // Alap szín
    $titleColor = '#54d966'; // Title szín
    $textColor = '#333'; // Szöveg szín
    $fontFamily = 'Montserrat, sans-serif'; // Alap betűtípus
    $titleFontFamily = "Poiret One, sans-serif"; // Cím betűtípus
    $subtitleFontFamily = 'Poiret One, sans-serif'; // Alcím betű
    $primaryBgTextColor = 'white'; // Alap háttérszín


@endphp

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <title>{{ $pattern['title'] ?? 'Amigurumi minta' }}</title>

       {{-- CSS fájlok helyi eléréssel (eredeti kódod alapján) --}}
    @if(!empty($pattern['css_files']) && is_array($pattern['css_files']))
        @foreach($pattern['css_files'] as $cssFile)
            <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path($cssFile)) }}" />
        @endforeach
    @else
        <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path('css/pdf-pattern.css')) }}" />
    @endif
    <style> 
        @font-face {
            font-family: 'Twinkle Star';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/TwinkleStar-Regular.ttf')) }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Oranienbaum';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Oranienbaum-Regular.ttf')) }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Charm';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Charm-Regular.ttf')) }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
         @font-face {
            font-family: 'Charm';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Charm-Bold.ttf')) }}") format('truetype');
            font-weight: 600;
            font-style: bold;
        }
        @font-face {
            font-family: 'Poiret One';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/PoiretOne-Regular.ttf')) }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Montserrat-Regular.ttf')) }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Montserrat-Thin.ttf')) }}") format('truetype');
            font-weight: 200;
            font-style: thin;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url("file:///{{ str_replace('\\', '/', public_path('fonts/Montserrat-Bold.ttf')) }}") format('truetype');
            font-weight: 600;
            font-style: bold;
        }

        
     
        body{
            color: {{ $textColor }};
            font-family: {{ $fontFamily }};
        }
        .section-title, .main-title{
            color: {{ $titleColor }};
            font-family: {{ $titleFontFamily }};
        }
        .subtitle{
            font-family: {{ $subtitleFontFamily }};
            color: {{ $textColor }};
        }
        .border-color{
            border-color: {{ $primaryColor }};
        }
        .primary-bg{
            text:{{$primaryBgTextColor}};
            background-color: {{ $primaryColor }};
        }

       
    </style>

    {{-- PDF stílusok --}}

 
    <style>
    </style>
</head>
<body>



<div class="pdf-wrapper">
    {{-- CÍMOLDAL --}}
    
        <div class="title-page">
            <h1 class="main-title">{{ $pattern['title'] ?? 'Amigurumi minta' }}</h1>
            <div class="subtitle">PDF amigurumi pattern</div>
            <div class="author">by {{ $pattern['user']['creator_name'] }}</div>
        </div>
            
            @if(!empty($pattern['main_image_url']))
                <div class="main-image">
                    <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($pattern['main_image_url']))) }}" 
                         alt="Főkép" class="cover-image" />
                </div>
            @endif
        
    <div class="page-break"></div>

   
    {{-- BEVEZETŐ OLDAL --}}
   
        <div class="intro-page">
           
            @if(!empty($pattern['introduction']))
                <div class="thank-you-box border-color">
                {!! $pattern['introduction'] !!}
                </div>
                
            @endif

           

           
        </div>
         {{-- LÁBLÉC --}}

        <div class="footer-page border-color">
            <div>{{ $pattern['user']['creator_name'] }}</div>
            @if(!empty($pattern['user']['logo']))
                <div class="logo-image">
                    <img src="file:///{{ str_replace('\\', '/', public_path('storage/logos/' . basename($pattern['user']['logo']))) }}" 
                          />
                </div>
            @endif
            @if(!empty($pattern['user']['socialLinks']))
                <div class="social-links">
                    @php
                        $socialLinks = $pattern['user']['socialLinks'] ?? [];
                    @endphp

                    @foreach($socialLinks as $sl)
                        <div class="social-item" style="margin-bottom:4px;">
                            @if(!empty($sl['icon']))
                                <img src="file:///{{ str_replace('\\', '/', public_path('storage/' . $sl['icon'])) }}" 
                                    alt="icon" width="16" height="16"
                                    style="vertical-align:middle; margin-right:4px;">
                            @endif
                            <span style="vertical-align:middle;">
                                <a href="{{ $sl['link'] }}" target="_blank">{{ $sl['title'] }}</a>
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="brand-info">
                <strong>{{ $pattern['brand'] ?? 'StockInDesign' }}</strong><br>
                <span>{{ $pattern['tagline'] ?? 'The LAB of InDesign Templates' }}</span><br>
                <span>{{ $pattern['website'] ?? 'www.stockindesign.com' }}</span><br>
                <span>{{ $pattern['facebook'] ?? 'fb.com/stockInDesign' }}</span>
            </div>
        </div>
    

    

    {{-- ANYAGOK ÉS RÖVIDÍTÉSEK KÉTOSZLOPBAN --}}
   
        <div class="materials-page">
            <div class="row">
                <div class="col-6">
                    @if(!empty($pattern['yarn_description']))
                        <h2 class="section-heading">FONALAK</h2>
                        <div class="materials-box">
                            {!! $pattern['yarn_description'] !!}
                        </div>
                    @endif

                    @if(!empty($pattern['tools_description']))
                        <h2 class="section-heading mt-20">ESZKÖZÖK</h2>
                        <div class="tools-box">
                            {!! $pattern['tools_description'] !!}
                        </div>
                    @endif
                </div>

                <div class="col-6">
                     @if(!empty($pattern['final_size']))
                        <div class="info-box border-color">
                            <p>Végső magasság: {{ $pattern['final_size'] }}</p>
                        </div>
                    @endif

                    @if(!empty($pattern['difficulty']))
                        <div class="info-box border-color">
                            <li>Nehézségi szint: {{ $pattern['difficulty'] }}</li>
                        </div>
                    @endif
                    <h2 class="section-heading">RÖVIDÍTÉSEK</h2>
                    <div class="abbreviations-box border-color">
                        @if(!empty($pattern['abbreviations']))
                            {!! $pattern['abbreviations']  !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    {{-- EGYÉB KÉPEK GALÉRIA --}}
    @if(!empty($pattern['images']) && is_array($pattern['images']))
       
            <div class="image-gallery-page">
                <div class="image-gallery">
                    @foreach($pattern['images'] as $image)
                        @if(!empty($image['url']) && !$image['is_main'])
                            <div class="gallery-item">
                                <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($image['url']))) }}" 
                                      />
                                @if(!empty($image['caption']))
                                    <div class="caption">{{ $image['caption'] }}</div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            
    @endif

    {{-- SZEKCIÓK ÚJRA STRUKTURÁLVA --}}
    @if(!empty($pattern['sections']) && is_array($pattern['sections']))
        
        @foreach($pattern['sections'] as $section)

            <div class="section-page">
                <div class="section-title-container">
                    <h2 class="section-title border-color">{{ $section['title'] }}</h2>
                </div>
               
               

                {{-- KÉPEK KÖZÉPRE IGAZÍTVA MAXIMUM 3 EGY SORBAN --}}
                @if(!empty($section['images']))
                    <div class="section-images-center">
                        @foreach($section['images'] as $img)
                           
                            @if(!empty($img['caption']))
                                    <div class="img-caption">{{ $img['caption'] }}</div>
                                @endif
                            <div class="section-image-item">
                                <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($img['url']))) }}" 
                                        alt="Section image" class="section-img" />
                                
                            </div>
                            
                        @endforeach
                    </div>
                @endif

                {{-- UTASÍTÁSOK  --}}
                @if(!empty($section['rows']))
                   
                   <ul class="instruction-list">
                    @foreach($section['rows'] as $row)
                        @if(!empty($row['color_change'])) 
                            <li class="without-border">
                                <div class="color_change"><strong>//</strong>{{ $row['color_change'] }}</div>
                            </li>
                        @endif
                        <li>
                            <div class="row-number">{{ $row['row_number'] }}:</div>
                            <div class="instruction">{{ $row['instructions'] }}</div>
                            @if(!empty($row['stitch_number'])) 
                            <div class="stitch-count">({{ $row['stitch_number'] }})</div>
                            @endif
                        </li>
                    
                        @if(!empty($row['comment'])) 
                            <li class="without-border">
                                <div class="comment ">{{ $row['comment'] }}</div>
                            </li>
                        @endif
                    
                    @endforeach
                </ul>



                @endif

               
            </div>
  
        @endforeach
    @endif

    {{-- ÖSSZEÁLLÍTÁSI ÚTMUTATÓ --}}
    @if(!empty($pattern['assemblySteps']))
      
            <div class="assembly-page">
                <h2 class="section-heading">ÖSSZEÁLLÍTÁS</h2>
                <div class="assembly-content">
                    @foreach($pattern['assemblySteps'] as $assemblyStep)
                        <div class="assembly-step">
                        @if(!empty($assemblyStep['text']))
                            <p class="assembly-text">{!! nl2br(e($assemblyStep['text'])) !!}</p>
                        @endif

                        @if(!empty($assemblyStep['images']) && count($assemblyStep['images']) > 0)
                            <div class="assembly-images">
                                @foreach($assemblyStep['images'] as $image)
                                    <div class="assembly-image">
                                        <img src="{{ public_path('storage/' . $image['path']) }}" alt="Step image">
                                        @if(!empty($image['caption']))
                                            <div class="caption">{{ $image['caption'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
       
        
    @endif

   

</div>

</body>
</html>

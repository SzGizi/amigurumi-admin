<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <title>{{ $pattern['title'] ?? 'Amigurumi minta' }}</title>
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
    </style>

    {{-- PDF stílusok --}}

    {{-- CSS fájlok helyi eléréssel (eredeti kódod alapján) --}}
    @if(!empty($pattern['css_files']) && is_array($pattern['css_files']))
        @foreach($pattern['css_files'] as $cssFile)
            <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path($cssFile)) }}" />
        @endforeach
    @else
        <link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path('css/pdf-pattern.css')) }}" />
    @endif
    <style>
    </style>
</head>
<body>



<div class="pdf-wrapper">
    {{-- CÍMOLDAL --}}
    
        <div class="title-page">
            <h1 class="main-title">{{ $pattern['title'] ?? 'Amigurumi minta' }}</h1>
            <div class="subtitle">PDF amigurumi pattern</div>
            <div class="author">by {{ $pattern['author'] ?? 'Szántó Gizella' }}</div>
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
           
            <div class="thank-you-box">
                <h2>Kedves horgolóbarát!</h2>
                <p>Köszönöm, hogy ezt a mintát választottad. Remélem, hogy ugyanolyan élvezettel készíted el, 
                   mint amilyen örömmel én terveztem.</p>
                <p>Ez a minta szerzői jogvédelem alatt áll és csak személyes használatra készült. 
                   A kész játékot szabadon eladhatod vagy ajándékba adhatod. 
                   Kérlek, tüntesd fel a készítő nevét ({{ $pattern['author'] ?? 'Szántó Gizella' }}) 
                   amikor megosztod a kész munkádat.</p>
            </div>

            <div class="info-box">
                <h3>Fontos információk</h3>
                <ul>
                    <li>Folyamatos spirálban horgolj, ne kapcsold össze a sorokat!</li>
                    <li>Használj jelölőt minden sor elején!</li>
                    <li>Végső magasság: {{ $pattern['final_height'] ?? '19-20cm' }}</li>
                    <li>Nehézségi szint: {{ $pattern['difficulty'] ?? 'közepes' }}</li>
                </ul>
            </div>

            <div class="contact-info">
                <div class="contact-box">
                    <strong>{{ $pattern['author'] ?? 'Szántó Gizella' }}</strong><br>
                    {{ $pattern['description'] ?? 'Rövid leírás' }}<br>
                    {{ $pattern['website'] ?? 'weboldal' }}<br>
                    {{ $pattern['social'] ?? 'fb.com/' }}<br>
                    {{ $pattern['etsy'] ?? 'etsy' }}
                </div>
            </div>
        </div>
    

    

    {{-- ANYAGOK ÉS RÖVIDÍTÉSEK KÉTOSZLOPBAN --}}
   
        <div class="materials-page">
            <div class="row">
                <div class="col-6">
                    @if(!empty($pattern['yarn_description']))
                        <h2 class="section-heading">ANYAGOK</h2>
                        <div class="materials-box">
                            {!! nl2br(e($pattern['yarn_description'])) !!}
                        </div>
                    @endif

                    @if(!empty($pattern['tools_description']))
                        <h2 class="section-heading mt-20">ESZKÖZÖK</h2>
                        <div class="tools-box">
                            {!! nl2br(e($pattern['tools_description'])) !!}
                        </div>
                    @endif
                </div>

                <div class="col-6">
                    <h2 class="section-heading">RÖVIDÍTÉSEK</h2>
                    <div class="abbreviations-box">
                        @if(!empty($pattern['abbreviations']))
                            <ul class="abbr-list">
                                @foreach($pattern['abbreviations'] as $abbr)
                                    <li><strong>{{ $abbr['short'] }}</strong> - {{ $abbr['desc'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <ul class="abbr-list">
                                <li><strong>ch</strong> - chain</li>
                                <li><strong>sc</strong> - single crochet</li>
                                <li><strong>dc</strong> - double crochet</li>
                                <li><strong>inc</strong> - increase - two single crochet in one stitch</li>
                                <li><strong>dec</strong> - decrease - two single crochet together</li>
                                <li><strong>*...*</strong> - repeat as instructed</li>
                                <li><strong>(12)</strong> - number of single crochet in the row</li>
                                <li><strong>sl</strong> - slip stitch</li>
                                <li><strong>st</strong> - stitch</li>
                                <li><strong>MR</strong> - magic ring</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    {{-- EGYÉB KÉPEK GALÉRIA --}}
    @if(!empty($pattern['images']) && is_array($pattern['images']))
       
            <div class="image-gallery-page">
                <h2 class="section-heading">KÉPEK</h2>
                <div class="image-gallery">
                    @foreach($pattern['images'] as $image)
                        @if(!empty($image['url']))
                            <div class="gallery-item">
                                <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($image['url']))) }}" 
                                     alt="Minta kép" />
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
                <h2 class="section-title">{{ $section['title'] }}</h2>
                <div class="section-subtitle">{{ $section['color'] ?? '' }}</div>

                {{-- KÉPEK KÖZÉPRE IGAZÍTVA MAXIMUM 3 EGY SORBAN --}}
                @if(!empty($section['images']))
                    <div class="section-images-center">
                        @foreach($section['images'] as $img)
                            <div class="section-image-item">
                                <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/' . basename($img['url']))) }}" 
                                        alt="Section image" class="section-img" />
                                @if(!empty($img['caption']))
                                    <div class="img-caption">{{ $img['caption'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- UTASÍTÁSOK KÉT OSZLOPBAN --}}
                @if(!empty($section['rows']))
                    {{-- <div class="instructions-container">
                        @php
                            $rows = $section['rows'];
                            $halfCount = ceil(count($rows) / 2);
                            $leftRows = array_slice($rows, 0, $halfCount);
                            $rightRows = array_slice($rows, $halfCount);
                        @endphp
                        
                        <div class="instructions-row">
                            <div class="instructions-col">
                                <ul class="instruction-list">
                                    @foreach($leftRows as $row)
                                        <li>
                                            <span class="row-number">{{ $row['row_number'] }}:</span> 
                                            <span class="instruction">{{ $row['instructions'] }}</span>
                                            @if(!empty($row['stitch_number'])) 
                                                <span class="stitch-count">({{ $row['stitch_number'] }})</span> 
                                            @endif
                                            @if(!empty($row['comment'])) 
                                                <span class="comment">{{ $row['comment'] }}</span> 
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="instructions-col">
                                <ul class="instruction-list">
                                    @foreach($rightRows as $row)
                                        <li>
                                            <span class="row-number">{{ $row['row_number'] }}:</span> 
                                            <span class="instruction">{{ $row['instructions'] }}</span>
                                            @if(!empty($row['stitch_number'])) 
                                                <span class="stitch-count">({{ $row['stitch_number'] }})</span> 
                                            @endif
                                            @if(!empty($row['comment'])) 
                                                <span class="comment">{{ $row['comment'] }}</span> 
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div> --}}
                   <ul class="instruction-list">
                @foreach($section['rows'] as $row)
                <li>
                    <span class="row-number">{{ $row['row_number'] }}:</span>
                    <span class="instruction">{{ $row['instructions'] }}</span>
                    @if(!empty($row['stitch_number'])) 
                    <span class="stitch-count">({{ $row['stitch_number'] }})</span>
                    @endif
                    @if(!empty($row['comment'])) 
                    <span class="comment">{{ $row['comment'] }}</span>
                    @endif
                </li>
                @endforeach
                </ul>




                @endif

                {{-- JEGYZETEK --}}
                @if(!empty($section['notes']))
                    <div class="section-notes">
                        <ul class="notes-list">
                            @foreach($section['notes'] as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
  
        @endforeach
    @endif

    {{-- ÖSSZEÁLLÍTÁSI ÚTMUTATÓ --}}
    @if(!empty($pattern['assembly_instructions']))
       
            <div class="assembly-page">
                <h2 class="section-heading">ÖSSZEÁLLÍTÁS</h2>
                <div class="assembly-content">
                    {!! nl2br(e($pattern['assembly_instructions'])) !!}
                </div>
            </div>
       
        
    @endif

    {{-- LÁBLÉC --}}

    <div class="footer-page">
        <div class="social-links">
            <span>instagram.com/{{ $pattern['instagram'] ?? 'stockindesign' }}</span>
        </div>
        <div class="brand-info">
            <strong>{{ $pattern['brand'] ?? 'StockInDesign' }}</strong><br>
            <span>{{ $pattern['tagline'] ?? 'The LAB of InDesign Templates' }}</span><br>
            <span>{{ $pattern['website'] ?? 'www.stockindesign.com' }}</span><br>
            <span>{{ $pattern['facebook'] ?? 'fb.com/stockInDesign' }}</span>
        </div>
    </div>

</div>

</body>
</html>

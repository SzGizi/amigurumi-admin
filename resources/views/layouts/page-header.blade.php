
@if(View::hasSection('page-title') || View::hasSection('breadcrumbs'))
    <div class="page-header mb-4">
        @hasSection('page-title')
            <h1 class="h2">@yield('page-title')</h1>
        @endif

        @hasSection('breadcrumbs')
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @yield('breadcrumbs')
                </ol>
            </nav>
        @endif
    </div>
@endif
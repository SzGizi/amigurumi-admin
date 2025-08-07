<nav class="navbar navbar-expand-lg navbar-light bg-white main-nav border-curve">
    <div class="container-xl">
        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('logo.svg') }}" alt="Logo" height="36">
        </a>

        {{-- Hamburger / toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible content --}}
        <div class="collapse navbar-collapse" id="mainNavbar">
            {{-- Bal oldal: fő linkek --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route('amigurumi-patterns.index') }}">
                        {{ __('Amigurumi Manager') }}
                    </a>
                </li>
            </ul>

            {{-- Jobb oldal: user dropdown (csak ha be van jelentkezve) --}}
            @if(Auth::check())
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a  class="nav-link dropdown-toggle"
                            href="#"
                            id="userDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    {{ __('Profile') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div><!-- /.collapse -->
    </div><!-- /.container-xl -->
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            new bootstrap.Dropdown(dropdown);
        });
    });
</script>

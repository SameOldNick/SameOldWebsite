<nav id="topNavbar" class="navbar navbar-expand-lg bg-black navbar-dark">
    <div class="container">
        <div class="logo">
            <a class="navbar-brand text-cursive fs-3" href="{{ url('/') }}">
                SameOldNick.com
            </a>
        </div>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse w-100" id="navbarSupportedContent">
            <x-menu name="main"/>

            <ul class="navbar-nav gap-2">
                <li class="nav-item dropdown dropdown-search">
                    <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                    <form action="{{ route('blog.search') }}" class="dropdown-menu dropdown-menu-dark dropdown-menu-end p-4">
                        <div class="ibc-container">
                            <input type="search" name="q" class="form-control" placeholder="Search" aria-label="Search">
                            <button class="ibc-button"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </form>

                </li>

                @guest
                <li class="nav-item dropdown dropdown-login">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" title="{{ __('Authentication') }}" role="button" aria-expanded="false">
                        <i class="fa-solid fa-lock-open"></i>
                        <span class="visually-hidden-focusable">{{ __('Authentication') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        @if (Route::has('login'))
                        <li><a href="{{ route('login') }}" class="dropdown-item">{{ __('Login') }}</a></li>
                        @endif

                        @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="dropdown-item">{{ __('Create Account') }}</a></li>
                        @endif
                    </ul>
                </li>
                @else
                <li class="nav-item dropdown dropdown-account">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" title="{{ __('Account') }}" role="button" aria-expanded="false">
                        <i class="fa-solid fa-user me-1"></i>
                        {{ Auth::user()->email }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        @can('admin')
                        <li>
                            <a class="dropdown-item" href="{{ URL::temporarySignedRoute('admin.sso', now()->addMinutes(15), ['user' => Auth::user()->getKey()]) }}">
                                {{ __('Dashboard') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @endcan
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.change-password') }}">
                                {{ __('Change Password') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" id="logoutFormLink" href="{{ route('logout') }}">
                                {{ __('Logout') }}
                            </a>
                        </li>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                            <x-return-url-input :returnUrl="url()->current()" />
                        </form>
                    </ul>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

@auth
    @push('scripts')
        <script type="text/javascript">
            document.getElementById('logoutFormLink').onclick = function(e) {
                e.preventDefault();

                document.getElementById('logoutForm').submit();
            };
        </script>
    @endpush
@endauth

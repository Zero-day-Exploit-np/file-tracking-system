            @if (Route::has('login'))
            <nav>
                @auth
                <a
                    href="{{ url('/dashboard') }}">
                    Dashboard
                </a>
                @else
                <a
                    href="{{ route('login') }}">
                    Log in
                </a>

                @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"> Register
                </a>
                @endif
                @endauth
            </nav>
            @endif






            @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
            @endif
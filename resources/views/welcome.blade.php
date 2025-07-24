
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container py-5">
        <header class="mb-4">
            @if (Route::has('login'))
                <nav class="d-flex justify-content-end gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            Dashboardwww
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <main class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title mb-3">Let's get started</h1>
                        <p class="card-text text-muted mb-4">
                            Laravel has an incredibly rich ecosystem.<br>
                            We suggest starting with the following.
                        </p>
                        <ul class="list-group mb-3">
                            <li class="list-group-item">
                                Read the <a href="https://laravel.com/docs" target="_blank" class="link-primary">Documentation</a>
                            </li>
                            <li class="list-group-item">
                                Watch video tutorials at <a href="https://laracasts.com" target="_blank" class="link-primary">Laracasts</a>
                            </li>
                        </ul>
                        <a href="https://cloud.laravel.com" target="_blank" class="btn btn-success">
                            Deploy now rr
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
    <body>
        <div class="container">
            <header class="mb-4 bg-primary text-white text-center py-3">
                Laravel
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
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
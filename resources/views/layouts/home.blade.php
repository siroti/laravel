
@extends('base.app')

@section('content')
<main>                                            
    <div class="container py-5">
        <h1 class="mb-4">Bem-vindo ao Laravel</h1>
        <p class="lead">Laravel é um framework PHP para desenvolvimento de aplicações web.</p>
        <p>Para começar, você pode explorar os seguintes recursos:</p>
        <ul class="list-group mb-3">
            <li class="list-group-item">
                <a href="https://laravel.com/docs" target="_blank" class="link-primary">Documentação</a>
            </li>
            <li class="list-group-item">
                <a href="https://laracasts.com" target="_blank" class="link-primary">Laracasts</a>
            </li>
        </ul>
        <a href="https://cloud.laravel.com" target="_blank" class="btn btn-success">
            Deploy agora
        </a>
    </div>
</main>
@endsection

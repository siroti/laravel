<?php

namespace App\Http\ViewComposer;

use Illuminate\View\View;

class ConfigComposer
{
    public function compose(View $view)
    {
        $host = request()->getHost();
       // $client = session('client');
        $config = config('clients.' .$host);
        session(['client' => $host]);
        $view->with('config', $config);
    }
}
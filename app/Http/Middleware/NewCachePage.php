<?php

namespace App\Http\Middleware;

use Cache;
use Closure;

class NewCachePage
{
    public function handle($request, Closure $next)
    {
        // Pula cache para admin, requests POST ou ambiente local
       if ($request->isMethod('post') || 
            $request->is('admin/*') || 
            str_contains($request->fullUrl(), 'http://carmona.localhost') || str_contains($request->fullUrl(), 'http://novosite.localhost')) {
            return $next($request);
        }

        // Limpar cache se solicitado
        if ($request->has('clear_cache')) {
            $this->clearCache($request);
            return redirect($request->url());
        }

        $version = $this->getVersion();
        $clientId = $this->getClientId(); // ✅ Adicionar cliente no cache key
        
        $key = 'new_page_cache_v17_' . $clientId . '_' . $version . '_' . md5($request->fullUrl());
        
        // Verifica se tem cache
        if (Cache::has($key)) {
            $content = Cache::get($key);
            return response($content)
                ->header('Cache-Control', 'public, max-age=1800') // 30 min
                ->header('X-Cache', 'HIT')
                ->header('X-Client-Id', $clientId); // ✅ Debug header
        }

        $response = $next($request);
        
        // Só cacheia se for sucesso (200)
        if ($response->getStatusCode() === 200) {
            $cacheTime = $this->getCacheTime($request);
            Cache::put($key, $response->getContent(), $cacheTime);
        }

        return $response->header('Cache-Control', 'public, max-age=1800')
                       ->header('X-Cache', 'MISS')
                       ->header('X-Client-Id', $clientId); // ✅ Debug header
    }

    private function getCacheTime($request)
    {
        if ($request->is('images/*')) {
            return 1440; // 24 horas
        }

        // Páginas dinâmicas (imóveis) = cache menor
        if ($request->is('imovel/*') || $request->is('imoveis/*')) {
            return 5; // 5 minutos
        }

        // Páginas estáticas = cache maior
        return 30; // 30 minutos
    }

    // ✅ NOVA FUNÇÃO: Obter ID do cliente
    private function getClientId()
    {
        $propertyIds = getPropertyId();
        
        if (is_array($propertyIds)) {
            return implode(',', $propertyIds);
        }
        
        return (string) $propertyIds;
    }

    private function getVersion()
    {
        $clientId = $this->getClientId(); 
        return Cache::remember('dbversio_' . $clientId, 300, function() use ($clientId) { // 5 minutos
            //$cssVersion = filemtime(public_path('new_model/css/main.css')) ?: time();
            $propertyIds = getPropertyId();
            if (!is_array($propertyIds)) {
                $propertyIds = [$propertyIds];
            }
            
            if (!empty($propertyIds)) {
                // Usar select específico em vez de max() que é mais lento
                $dbVersion = \DB::table('imoveis')
                    ->selectRaw('UNIX_TIMESTAMP(MAX(dataAtualizacao)) as timestamp')
                    ->whereIn('CodigoCliente', $propertyIds)
                    ->value('timestamp') ?: time();
            } else {
                $dbVersion = time();
            }
            
            return $clientId . '_' . $dbVersion;
        });
    }

    private function clearCache($request)
    {
        $version = $this->getVersion();
        $clientId = $this->getClientId(); // ✅ Incluir cliente no clear
        $key = 'new_page_cache_v17_' . $clientId . '_' . $version . '_' . md5($request->fullUrl());
        Cache::forget($key);
        
        // ✅ Opcional: Limpar toda a versão do cliente
        if ($request->has('clear_all_client_cache')) {
            Cache::forget('db_version_' . $clientId);
        }
    }
}
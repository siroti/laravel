<style rel="preload">
:root {
    --cor-topo: {{ $config['colors']['color_top'] ?? '#fff' }};
    --cor-topo-font: {{ $config['colors']['color_top_font'] }};

    --cor-pesquisa: {{ $config['colors']['color_search'] }};
    --cor-pesquisa-rgb: {{ rgba($config['colors']['color_search']) }};
    --cor-pesquisa-font: {{ $config['colors']['color_search_font'] }};

    --cor-button: {{ $config['colors']['color_button'] }};
    --cor-primaria: {{ $config['colors']['color_primary'] }};
    --cor-secundaria: {{ $config['colors']['color_secondary'] }};
    --cor-terciaria: {{ $config['colors']['color_tertiary'] }};

    --cor-primaria-rgb: {{ rgba($config['colors']['color_primary']) }};
    --cor-primaria-hex: {{ str_replace("#", '', $config['colors']['color_primary']) }};
    --cor-primaria-rgb-hover: {{ ColorRgb($config['colors']['color_primary'], -10) }};
    --cor-primaria-rgb-active: {{ ColorRgb($config['colors']['color_primary'], 15) }};
    
    --cor-secundaria-rgb: {{ rgba($config['colors']['color_secondary']) }};
    --cor-secundaria-hex: {{ str_replace("#", '', $config['colors']['color_secondary']) }};
    --cor-secundaria-rgb-hover: {{ ColorRgb($config['colors']['color_secondary'], -10) }};
    --cor-secundaria-rgb-active: {{ ColorRgb($config['colors']['color_secondary'], 15) }};
    
    --cor-footer: {{ $config['colors']['color_footer'] }};
    --cor-footer-font: {{ $config['colors']['color_footer_font'] }};
}
</style>

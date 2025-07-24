<?php
use Illuminate\Support\Str;


// Converte "Nova Maringá" -> "nova_maringa"
/*
function str($texto)
{
    return Str::slug($texto, '_'); 
}
*/
function acento($texto) {
    static $dicionario = null;

    if (!$dicionario) {
        $caminho = 'https://s3.amazonaws.com/sites-sub100/json/dicionario.json'; 
        //$caminho = storage_path('app/dicionario.json');
        /*if (!file_exists($caminho)) {
            throw new \Exception("Arquivo de dicionário não encontrado: $caminho");
        }*/
        $dicionario = json_decode(file_get_contents($caminho), true);
    }

    return str_ireplace(array_keys($dicionario), array_values($dicionario), $texto);
}

function formatarLabel($item) {
    $tipo = Str::contains($item, '-bioma') ? '-bioma' : '-cultivo';
    $labelRaw = ucwords(acento(str_replace(['_', $tipo], [' ', ''], $item)));
    $minusculas = ['De', 'Da', 'Do', 'Das', 'Dos', 'E'];

    $partes = explode(' ', $labelRaw);
    foreach ($partes as $idx => $parte) {
        if ($idx > 0 && in_array($parte, $minusculas)) {
            $partes[$idx] = strtolower($parte);
        }
    }

    return implode(' ', $partes);
}


//Follow work
function vfollow()
{
     // if (!session()->has('hasFollow')) {
        $menu = Panel::getMenu();
        session()->put('hasStatus', 0);

        if (count($menu) > 0) {
            if ($menu->situacao == 1 and count($menu->exibiracompanhe) > 0) {
                session()->put('hasStatus', 1);
            }
            session()->put('hasFollow', true);
        } else {
            session()->put('hasFollow', false);
        }
   // }
    return array("follow" => session('hasFollow'), "status" => session('hasStatus'));
}

//Verifica se existe um módulo.
function hasModule($name)
{
    return in_array_r($name, getModules());
}

//Converter Maiúsculas e Minúsculas
function formatarTexto($texto, $option = "cada") {
    // Lista de palavras que devem permanecer minúsculas
    $minusc = ['dos', 'das', 'do', 'da', 'de', 'e', 'em', 'com', 'para', 'por', 'a', 'o', 'as', 'os', 'um', 'uma', 'uns', 'umas', 'no', 'na', 'nos', 'nas', 'sobre', 'entre', 'até', 'contra', 'após', 'perante', 'desde', 'sem', 'sob'];

    // Remove espaços extras antes de processar
    $texto = trim($texto);

    switch ($option) {
        case "mai":
            return mb_strtoupper($texto, 'UTF-8'); // Tudo maiúsculo

        case "min":
            return mb_strtolower($texto, 'UTF-8'); // Tudo minúsculo

        case "cada":
            // Converte cada palavra para iniciar com maiúscula
            $textoFormatado = mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');

            // Substituir palavras que devem permanecer minúsculas
            return preg_replace_callback('/\b(' . implode('|', $minusc) . ')\b/u', 
            function ($match) {
                return mb_strtolower($match[0], 'UTF-8');
            }, $textoFormatado);
            
        case "frase":
            // Converte tudo para minúsculas e apenas a primeira palavra em maiúscula
            $texto = mb_strtolower($texto, 'UTF-8');
            return ucfirst($texto);

        default:
            return $texto; // Retorna o texto original caso a opção seja inválida
    }
}

    function tipoPalavra($titulo) {
        $padrao = ["APARTAMENTOS", "CASAS", "SOBRADOS", "TERRENOS", "CHÁCARAS", "FAZENDAS", "SÍTIOS"];
        $resultado = [];

        $tituloUpper = strtoupper($titulo);

        foreach ($padrao as $palavra) {
            if (strpos($tituloUpper, $palavra) !== false) {
                $resultado[] = $palavra;
            }
        }
        return !empty($resultado) ? implode(', ', $resultado) : null;
    }

function pluralize($text) {
    // Lista de palavras e seus plurais
    $plurals = [
        'Casa' => 'Casas',
        'Sobrado' => 'Sobrados',
        'Apartamento' => 'Apartamentos',
        'Condomínio' => 'Condomínios',
        'Comercial' => 'Comerciais',
        'Barracão' => 'Barracões',
        'Terreno' => 'Terrenos',
        'Loja' => 'Lojas',
        'Sala' => 'Salas',
        'Galpão'=> 'Galpões',
        'Rural' => 'Rurais',
        'Sítio' => 'Sítios',
        'Fazenda' => 'Fazendas',
        'Chacára' => 'Chacáras',
        'chacara' => 'chacaras',
        'Edíficio' => 'Edíficios',
        'Studio' => 'Studios',
        'Loja' => 'Lojas',
        'Industrial' => 'Industriais',
        'Negócio' => 'Negócios'
    ];

    // Substituição das palavras pelo plural
    foreach ($plurals as $singular => $plural) {
        $text = preg_replace('/\b' . preg_quote($singular, '/') . '\b/', $plural, $text);
    }

    return $text;
}


function getPalavraChaveFromUrl($url) {
    // Remove a primeira barra, se houver
    $url = ltrim($url, '/');
    
    // Explode a URL em partes
    $parts = explode('/', $url);
    
    // Percorre os elementos
    foreach ($parts as $part) {
        if (strpos($part, 'palavra-chave-') === 0) {
            // Remove "palavra-chave-" e substitui "-" por espaço
            $palavraChave = str_replace('-', ' ', substr($part, strlen('palavra-chave-')));
            
            // Coloca cada palavra com a primeira letra maiúscula
            return ucwords($palavraChave);
        }
    }

    // Se não encontrar, retorna null
    return null;
}

function getFilters($filters)
{
    $filters = explode('/', $filters);
    $url = explode('/', str_replace(\Illuminate\Support\Facades\URL::to('/') . "/", '', url()->current()));

    $URLlink = isset($url[0]) ? $url[0] : '';
    $URL2 = explode("-", isset($url[2]) ? $url[2] : "");
    // Filtros utilizados para comparação
    $arrFiltro = array('r$-', 'quarto', 'suite', 'm2', 'posicao', 'andares', '-garage', 'novo-ou-pronto', 'em-construcao', 'na-planta', 'foto', 'mobiliado', 'financiavel', 'permuta', 'quitado', 'portaria', 'churrasqueira', 'piscina', 'playground', 'quadra', 'elevador', 'universitario', 'pag', 'lista');
    $getpalavrachave = null;
    $gettipo = null;
    $getvalormin = null;
    $getvalormax = null;
    $getuf = null;
    $getbairro = null;
    $getendereco = null;
    $getfiltro = null;
    $getsuites = null;
    $getambientes = null;
    $getpessoas = null;
    $getgaragens = null;
    $getareamin = null;
    $getareamax = null;
    $getcidade = null;
    $_GET['b_edificio'] = null;
    $_GET['b_rua'] = null;
    $_GET['ordem'] = null;
    $_GET['inicio'] = null;
    $_GET['limite'] = null;
    $_GET['b_valor_min'] = null;
    $_GET['b_valor_max'] = null;


    // Procura por "palavra-chave"
    foreach ($url as $part) {
        if (strpos($part, 'palavra-chave-') === 0) {
            $getpalavrachave = ucwords(str_replace('-', ' ', substr($part, strlen('palavra-chave-'))));
            break; 
        }
    }

    if (isset($url[2]) and $url[2] == 'palavra-chave' and trim($url[3])):

        $getpalavrachave = htmlentities($url[3]);

        // DEFINE OS FILTROS DE PALAVRAS-CHAVE
        $arrPalavras = explode(" ", str_replace(',', ' ', $getpalavrachave));

        $interpretacao = letters();

        Properties::insertKey($getpalavrachave, $interpretacao[1]);

        if (!$getcidade and $getbairro):
            $getcidade = $_GET['b_cidade'] = IMOVEIS_CIDADE;
        elseif (!$getcidade and !$getbairro):
            $getcidade = $_GET['b_cidade'] = IMOVEIS_CIDADE;
        elseif ($getcidade):
            $_GET['b_cidade'] = $getcidade;
        endif;

    elseif (isset($url[2]) and strpos($url[2], 'pag-') === false):

        /* Se o link possui cidade e nao possui  */

       
        if ((is_numeric($URL2[0])) and (count($URL2) > 2) and strlen($URL2[count($URL2) - 1]) == 2):
            $gettipo = "TODOS";
            $expcidade = explode("-", $url[2]);
             

            $getcidade = ucfirst($URL2[0]);
            $getuf = strtoupper($URL2[count($expcidade) - 1]);
            session()->put('EMP' . getPropertyId() . '.pesquisa.estado', strtoupper($URL2[count($expcidade) - 1]));
            $indbairro = 3;

        else:
            foreach ($arrFiltro as $palavraFiltro):
                if (strpos($url[2], $palavraFiltro) !== false) $falseTipo = 1;
            endforeach;
            if (!isset($falseTipo)):
                $gettipo = strtoupper($url[2]);
            else:
                $gettipo = "TODOS";
            endif;
            $expcidade = isset($url[3]) ? explode("-", $url[3]) : $expcidade = null;

            if (isset($expcidade) && count($expcidade) > 1 && strlen($expcidade[count($expcidade) - 1]) == 2) {
                $getuf = strtoupper($expcidade[count($expcidade) - 1]);
                session()->put('EMP' . getPropertyId() . 'pesquisa.estado', $getuf);

                if (is_numeric($expcidade[0])) {
                    // Se começar com número, usa só o código numérico
                    $getcidade = $expcidade[0];
                    $indbairro = 4;
                } else {
                    // Senão, junta as partes da cidade
                    $partesCidade = array_slice($expcidade, 0, -1); // tudo menos o último (UF)
                    $nomeCidadeSlug = implode('-', $partesCidade);  // ex: rio-de-janeiro
                    $nomeCidade = ucwords(str_replace('-', ' ', $nomeCidadeSlug)); // ex: Rio De Janeiro
                    $getcidade = $nomeCidade;
                    $indbairro = 3;
                }
            }
            /*if (isset($expcidade) and (is_numeric($expcidade[0])) and (count($expcidade) > 2) and strlen($expcidade[count($expcidade) - 1]) == 2):
                $getcidade = $expcidade[0];
                
                $getuf = strtoupper($expcidade[count($expcidade) - 1]);
                session()->put('EMP' . getPropertyId() . 'pesquisa.estado', strtoupper($expcidade[count($expcidade) - 1]));
                $indbairro = 4;
            endif;*/
        endif;

        if ($url[1] === 'favoritos') {
            $gettipo = null;
        }

        $_GET['b_cidade'] = $getcidade;

        $arrsuites = array('suites', 'suite', '-');
        $getsuites = str_replace($arrsuites, "", b_array('-suite', $url));

        $arrquartos = array('-quartos', '-quarto');
        $getquartos = str_replace($arrquartos, "", b_array('quarto', $url));

        $arrambientes = array('-ambientes', '-ambiente');
        $getambientes = str_replace($arrambientes, "", b_array('ambiente', $url));

        $arrgaragem = array('-garagem', '-garagens');
        $getgaragens = str_replace($arrgaragem, "", b_array('-garage', $url));
        if (!$getgaragens) $getgaragens = "";

        $arrbairros = array('-bairros', '-bairro');
        $getbairro = str_replace($arrbairros, "", b_array('bairro', $url));

        $arrbiomas = array('-biomas', '-bioma');
        $getbioma = str_replace($arrbiomas, "", b_array('bioma', $url));

        if (isset($URLbioma) AND strstr($URLbioma[0], ',') && b_array('bioma,', $url)) {
            $getbioma = $URLbioma[0];
        }

        $arrnegociacao = array('-negociacoes', '-negociacao');
        $getnegociacao = str_replace($arrnegociacao, "", b_array('negociacao', $url));

        if (isset($URLnegociacao) AND strstr($URLnegociacao[0], ',') && b_array('negociacao,', $url)) {
            $getnegociacao = $URLnegociacao[0];
        }

        $arrsubtiporural = array('-subtiporurais', '-subtiporural');
        $getsubtiporural = str_replace($arrsubtiporural, "", b_array('subtiporural', $url));

        if (isset($URLsubtiporural) AND strstr($URLsubtiporural[0], ',') && b_array('subtiporural,', $url)) {
            $getsubtiporural = $URLsubtiporural[0];
        }

        $arrsolo = array('-solos', '-solo');
        $getsolo = str_replace($arrsolo, "", b_array('solo', $url));

        if (isset($URLsolo) AND strstr($URLsolo[0], ',') && b_array('solo,', $url)) {
            $getsolo = $URLsolo[0];
        }

        $arrcultivo = array('-cultivos', '-cultivo');
        $getcultivo = str_replace($arrcultivo, "", b_array('cultivo', $url));

        if (isset($URLcultivo) AND strstr($URLcultivo[0], ',') && b_array('cultivo,', $url)) {
            $getcultivo = $URLcultivo[0];
        }


        if (isset($URLbairro) AND strstr($URLBairro[0], ',') && b_array('bairros,', $url)) {
            $getbairro = $URLBairro[0];
        }

        $arrendereco = array('-enderecos', '-endereco');
        $getendereco = str_replace($arrendereco, "", b_array('endereco', $url));

        if (isset($URLendereco) AND strstr($URLEndereco[0], ',') && b_array('endereco,', $url)) {
            $getendereco = $URLEndereco[0];
        }

        $arrfiltros = array('-filtros', '-filtro');
        $getfiltro = str_replace($arrfiltros, "", b_array('filtro', $url));

        if (isset($URLfiltro) AND strstr($URLfiltro[0], ',') && b_array('filtros,', $url)) {
            $getfiltro = $URLfiltro[0];
        }

        $edificiosadv = b_array('edificios,', $url);

        if ($edificiosadv):
            $edificiosadv = explode(",", $edificiosadv);
            array_shift($edificiosadv);
            if (count($edificiosadv) > 0 and is_numeric($edificiosadv[0])):
                $getedificios = $edificiosadv;
                $_GET['b_edificio'] = implode(",", $getedificios);
            endif;
        endif;


        $arredificios = array('-edificios', '-edificio');
        $_GET['b_edificio'] = str_replace($arredificios, "", b_array('edificio', $url));

        if (isset($URLedificio) AND strstr($URLedificio[0], ',') && b_array('edificios,', $url)) {
            $_GET['b_edificio'] = $URLedificio[0];
        }


        $ruasadv = b_array('ruas,', $url);

        if ($ruasadv):
            $ruasadv = explode(",", $ruasadv);
            array_shift($ruasadv);
            if (count($ruasadv) > 0):
                $getruas = $ruasadv;
                $_GET['b_rua'] = implode(",", $getruas);
            endif;
        endif;

    endif;

    // Negocio
    $getnegocio = ucfirst($url[1]);
    $_GET['b_cidade'] = $getcidade;
// Valores
    $arrvaloreE = array('-milhao-ou-mais', '-mil');
    $arrvaloreC = array('000000+', '000');
    $getvalores = str_replace($arrvaloreE, $arrvaloreC, b_array('r$', $url));
    $expvalores = explode("-", $getvalores);
    $allvalores = count($expvalores);

    if ($allvalores == 4):
        $getvalormin = $expvalores[1];
    elseif ($allvalores == 5):
        $getvalormin = $expvalores[2];
        $getvalormax = $expvalores[4];
    elseif ($allvalores == 3):
        $getvalormax = $expvalores[2];
    endif;

    if (!isset($getquartos)) $getquartos = "TODOS";

    $getvista = str_replace("vista-mar-", "", b_array('vista-mar-', $url));
    if (!isset($getvista)) $getvista = "0";

    $getdistancia = str_replace("distancia-", "", b_array('distancia-', $url));
    if (!isset($getdistancia)) $getdistancia = "0";

    if ((b_array('pessoa', $url) != '241-joao-pessoa-pb') and (isset($url[2]) and $url[2] != 'palavra-chave')) {
        $arrpessoas = array('-pessoas', '-pessoa');
        $getpessoas = str_replace($arrpessoas, "", b_array('pessoa', $url));
        if (!isset($getpessoas)) $getpessoas = "0";
    }

    $getposicao = str_replace("posicao-", "", b_array('posicao-', $url));

    $getareascp = b_array('m2', $url);
    $getareas = str_replace('m2', '', $getareascp);
    $expareas = explode("-", $getareas);
    $allareas = count($expareas);

    if ($allareas == 2):
        $getareamin = 1;
        $getareamax = $expareas[1];
    elseif ($allareas == 3):
        $getareamin = $expareas[0];
        $getareamax = 1000;
    elseif ($allareas >= 4):
        $getareamin = $expareas[1];
        $getareamax = $expareas[3];
    endif;

    if (strstr($getareascp, 'm2') !== false) {
        $area = explode('m2', $getareascp);
        $getareamin = !empty($area[1]) ? preg_replace('/\D/', '', $area[0]) : '';
        $getareamax = !empty($area[1]) ? preg_replace('/\D/', '', $area[1]) : preg_replace('/\D/', '', $area[0]);
    }


    $getandares = str_replace("andares-", "", b_array('andares-', $url));
    $expandares = explode("-", $getandares);
    $getandares = $expandares[0];
    if (!$getandares) $getandares = "0";

    $getfoto = count(b_array('com-foto', $url));
    $getmobilia = count(b_array('com-mobilia', $url)) + count(b_array('mobiliado', $url));
    $getsemimobilia = count(b_array('semi-mobilia', $url)) + count(b_array('semimobiliado', $url));
    $getfinanciavel = count(b_array('financiavel', $url));
    $getpermuta = count(b_array('permuta', $url));
    $getquitado = count(b_array('quitado', $url));
    $getportaria = count(b_array('portaria-24h', $url));
    $getchurrasqueira = count(b_array('churrasqueira', $url));
    $getpiscina = count(b_array('piscina', $url));
    $getplayground = count(b_array('playground', $url));
    $getquadra = count(b_array('quadra-esportiva', $url));
    $getelevador = count(b_array('elevador', $url));
    $getobranovo = count(b_array('novo-ou-pronto', $url));
    $getobraconst = count(b_array('em-construcao', $url));
    $getobraplanta = count(b_array('na-planta', $url));
    $getquitado = count(b_array('quitado', $url));
    $getexata = count(b_array('quantidades_exatas', $url));
    $getaplicar10 = count(b_array('aplicar-10', $url));

    $getuniversitario = (b_array('universitarios', $url) === 'universitarios') ? 1 : 0;

    if (str_contains($gettipo, ['ORDEM', 'ordem'])) {
        $gettipo = "TODOS";
    }

    if (str_contains($gettipo, ['ORDEM', 'ordem'])) {
        $getbairro = "TODOS";
    }

    if ($getvalormax == null) {
        $getvalormax = 3250000;
    }

    if ($getvalormax == 3250000) {
        $getvalormax = "";
    }
    


    $_GET['b_palavrachave'] = $getpalavrachave;
    $_GET['b_negocio'] = $getnegocio;
    $_GET['b_tipo'] = $gettipo;
    $_GET['b_valor_min'] = $getvalormin;
    $_GET['b_valor_max'] = $getvalormax;
    $_GET['b_estado'] = $getuf;
    $_GET['b_filtro'] = $getfiltro;
    $_GET['b_bairro'] = $getbairro;
    $_GET['b_endereco'] = $getendereco;
    $_GET['b_suites'] = $getsuites;
    $_GET['b_quartos'] = $getquartos;
    $_GET['b_ambientes'] = $getambientes;
    $_GET['b_vista'] = $getvista;
    $_GET['b_distancia'] = $getdistancia;
    $_GET['b_capacidade'] = $getpessoas;

    /* FILTRO DA BUSCA AVANÇADA */
    $_GET['b_posicao'] = $getposicao;
    $_GET['b_garagens'] = $getgaragens;
    $_GET['b_area_min'] = $getareamin;
    $_GET['b_area_max'] = $getareamax;
    $_GET['b_entreandares'] = $getandares;
    $_GET['apenasfotos'] = $getfoto;
    $_GET['mobiliado'] = $getmobilia;
    $_GET['semimobiliado'] = $getsemimobilia;
    $_GET['financiavel'] = $getfinanciavel;
    $_GET['permuta'] = $getpermuta;
    $_GET['quitado'] = $getquitado;
    $_GET['portaria_24horas'] = $getportaria;
    $_GET['churrasqueira'] = $getchurrasqueira;
    $_GET['piscina'] = $getpiscina;
    $_GET['playground'] = $getplayground;
    $_GET['quadra_esportiva'] = $getquadra;
    $_GET['elevador'] = $getelevador;
    $_GET['b_obra_novo'] = $getobranovo;
    $_GET['b_obra_construcao'] = $getobraconst;
    $_GET['b_obra_planta'] = $getobraplanta;
    $_GET['b_universitario'] = $getuniversitario;
    $_GET['b_area'] = $getareascp;
    $_GET['exata'] = $getexata;
    $_GET['aplicar10'] = $getaplicar10;

    /*RURAL*/
    if(isset($getbioma))$_GET['b_bioma'] = $getbioma;
    if(isset($getnegociacao))$_GET['b_negociacao'] = $getnegociacao;
    if(isset($getsubtiporural))$_GET['b_subtiporural']  = $getsubtiporural;
    if(isset($getsolo))$_GET['b_solo']  = $getsolo;
    if(isset($getcultivo))$_GET['b_cultivo']  = $getcultivo;

    $getordem = str_replace("ordem-", "", b_array('ordem-', $url));
    $getordem = str_replace("+", " ", $getordem);
    $getordem = str_replace("bairros.nome", "bairro", $getordem);
    if (!$getordem) $getordem = "imoveis.valor";
    $_GET['ordem'] = $getordem;

    if ($url[0] == "imoveis-mapa") $_GET['b_mapa'] = 'mapa'; else $_GET['b_mapa'] = null;


    $_GET['b_tipo'] = ($_GET['b_tipo'] && $_GET['b_tipo'] != 'TODOS') ? mb_strtolower($_GET['b_tipo']) : (isset(session('EMP' . getPropertyId())['pesquisa']['tipo']) ? session('EMP' . getPropertyId())['pesquisa']['tipo'] : 'TODOS');

}

function getDomain()
{
    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}
// Funcao para buscar palavras no URL
function b_array($palavra, $array)
{
    foreach ($array as $chave => $valor):
        if (strpos($valor, $palavra) !== false) return $valor;
    endforeach;
}

function converterValor($partes) {
    $frasesPermitidas = ['a', 'partir', 'de', 'r$', 'ate'];
    $label = '';
    $i = 0;

    while ($i < count($partes)) {
        $parte = strtolower($partes[$i]);

        if (in_array($parte, $frasesPermitidas)) {
            if ($parte == 'r$' && isset($partes[$i + 1])) {
                $valor = number_format((float)$partes[$i + 1], 0, '', '.');
                $label .= ' R$ ' . $valor;
                $i += 2;
                continue;
            } elseif ($parte == 'ate') {
                $label .= ' até';
                if (isset($partes[$i + 1]) && is_numeric($partes[$i + 1])) {
                    $valor = number_format((float)$partes[$i + 1], 0, '', '.');
                    $label .= ' R$ ' . $valor;
                    $i += 2;
                    continue;
                }
            } elseif ($parte == 'de') {
                $label .= ' de';
            } elseif ($parte == 'a') {
                $label .= 'A';
            } elseif ($parte == 'partir') {
                $label .= ' partir';
            }
        }

        $i++;
        if ($i < count($partes)) {
            $label .= ' ';
        }
    }

    // Deixa apenas a primeira letra da string em maiúsculo
    return ucfirst(trim($label));
}




// Busca as palavras-chave digitadas
function b_array2($campo, $array, $termo)
{

    $termo = rtrim($termo, 's');

    // retira acentuação das palavras do array que sera pesquisado
    $array = @array_map("RemoveAcentos", $array);

    // verifica se a palavra é extada
    $palavraExata = @array_search($termo, $array);

    if ($palavraExata !== false):
        $lista[$campo] = $palavraExata;
        return $lista;
    endif;

    if (count($array)):
        foreach ($array as $key => $palavra):
            $key = mb_strtolower($key);
            $palavra = rtrim($palavra, "s");
            if ($palavra == $termo):
                $lista[$campo][] = $key; //break;
            elseif (preg_match('/ ' . $termo . '/', $palavra) or preg_match('/^' . $termo . '/', $palavra)):
                $lista[$campo][] = $key; //break;
            endif;
        endforeach;
    endif;

    return $lista;
}

function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

function limpaStringUpper($str, $uppercase = false)
{
    $str = preg_replace('/[áàãâä]/ui', 'A', $str);
    $str = preg_replace('/[éèêë]/ui', 'E', $str);
    $str = preg_replace('/[íìîï]/ui', 'I', $str);
    $str = preg_replace('/[óòõôö]/ui', 'O', $str);
    $str = preg_replace('/[úùûü]/ui', 'U', $str);
    $str = preg_replace('/[ç]/ui', 'C', $str);

    return $str;
}

function paginate($items, $perPage = 15, $page = null, $options = [], $count = null)
{
    $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);

    if($count === null) {
        return new \Illuminate\Pagination\LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    return new \Illuminate\Pagination\LengthAwarePaginator($items, $count, $perPage, $page, $options);
}

function acentuacaoTipo($tipo)
{
    if (!is_array($tipo)) $tipo = strtoupper($tipo);
    $acentos = array("Armazéns", "Barracão", "Chácaras", "Depósitos", "Edifícios Comerciais", "Galpões", "Negócios", "Prédios Inteiros", "Salas em Edifícios", "Salas Térreas", "Salões Comerciais", "Sítios");
    $semacento = array("ARMAZENS", "BARRACAO", "CHACARAS", "DEPOSITOS", "EDIFICIOS COMERCIAIS", "GALPOES", "NEGOCIOS", "PREDIOS INTEIROS", "SALAS EM EDIFICIOS", "SALAS TERREAS", "SALOES COMERCIAIS", "SITIOS");
    $acentotipo = str_replace($semacento, $acentos, $tipo);
    return $acentotipo;
}

function letters()
{
    $url = explode('/', str_replace(\Illuminate\Support\Facades\URL::to('/') . "/", '', url()->current()));
    if (count($url) > 4) {
        $found_bairro = null;
        $found_endereco = null;
        $found_cidade = null;
        $found_edificio = null;
        $found_rua = null;
        $found_tipo = null;
        $termo_bairro = null;
        $termo_cidade = null;
        $termo_edificio = null;
        $termo_rua = null;
        $termo_tipo = null;
        $getbairro = null;
        $getendereco = null;
        $gettipo = null;
        $getedificio = null;
        $getref = null;
        $getgaragens = null;
        $getpalavrachave = null;
        $getsuites = null;
        $getquartos = null;
        $getcidade = null;
        $getdormitorios = null;
        $gettermo = null;
        $getrua = null;
        $getpalavrachave = htmlentities($url[3]);

        // DEFINE OS FILTROS DE PALAVRAS-CHAVE
        $arrPalavras = explode(" ", str_replace(',', ' ', $getpalavrachave));
        $listacompleta = array("venda", "locacao", "alugar", "vender", "comprar");

    // Palavras para ignorar na busca digitada pelo usuário
        $excecoes = array("imoveis", "em", "das", "com", "sendo", "de", "ou", "quarto", "quartos", "suite", "venda", "locacao", "alugar", "vender", "comprar", "suites", "rua", "r", "avenida", "av.", ",", "pr", "e");

    // Tratar palavras compostas
        $kw_comp_bairros = array("jd", "vl", "pq", "cj", "lot", "zona", "gleba");
        $kw_comp_edificios = array("cond", "maison", "spazio");
        $kw_comp_ruas = array("sao", "av", "rua");

        $kw_compostos = array_merge($kw_comp_bairros, $kw_comp_edificios, $kw_comp_ruas);
        $arrPalavras = array_map("trocaSinonimos", $arrPalavras);
        $caract_errado = array("Iii", "Ii", " i ", "Iv", "Vi");
        $caract_certo = array("III", "II", " I ", "IV", "VI");
        // TIPOS
        $kw_tipos = array("RESIDENCIAIS" => 'RESIDENCIAIS', "APARTAMENTOS" => 'APARTAMENTOS', "COBERTURA-DUPLEX-OU-TRIPLEX" => 'APARTAMENTO COBERTURA, DUPLEX OU TRIPLEX', "CASAS-OU-SOBRADOS" => 'CASAS OU SOBRADOS', "CASAS" => 'CASAS', "SOBRADOS" => 'SOBRADOS', 'CASAS-GEMINADAS' => 'CASAS GEMINADAS',
            "SOBRELOJAS" => 'SOBRELOJAS', "POUSADAS" => 'POUSADAS',
            "CASAS-OU-SOBRADOS-EM-CONDOMINIOS" => 'CASAS OU SOBRADOS EM CONDOMÍNIOS', "KITNET-OU-STUDIOS" => 'KITNET/STUDIOS', "FLAT" => 'FLAT', "LOFT" => 'LOFT', "CHALES" => 'CHALÉS', "POROES" => 'PORÕES',
            "COMERCIAIS-OU-INDUSTRIAIS" => 'COMERCIAIS OU INDUSTRIAIS', "CASAS-OU-SOBRADOS" => 'CASAS OU SOBRADOS', "SALAS" => 'SALAS', "SALAS-EM-EDIFICIOS" => 'SALAS EM EDIFÍCIOS', "SALAS-EM-SHOPPING" => 'SALAS EM SHOPPING', "SALAS-COMERCIAIS" => 'SALAS COMERCIAIS',
            "SALAS-TERREAS" => 'SALAS TÉRREAS', "LOJAS" => 'LOJAS', "SOBRELOJAS" => 'SOBRELOJAS', "EDIFICIOS-COMERCIAIS" => 'EDIFÍCIOS COMERCIAIS', "PREDIOS-INTEIROS" => 'PRÉDIOS INTEIROS', "SALOES" => 'SALÕES', "BARRACOES" => 'BARRACÕES',
            "DEPOSITOS" => 'DEPÓSITOS', "GALPOES" => 'GALPÕES', "ARMAZENS" => 'ARMAZÉNS', "GARAGENS" => 'GARAGENS', "BOX" => 'BOX', "NEGOCIOS" => 'NEGÓCIOS', "TERRENOS" => 'TERRENOS', "TERRENOS-RESIDENCIAIS" => 'TERRENOS RESIDENCIAIS',
            "TERRENOS-EM-CONDOMINIOS" => 'TERRENOS EM CONDOMÍNIOS', "TERRENOS-COMERCIAIS" => 'TERRENOS COMERCIAIS', "TERRENOS-INDUSTRIAIS" => 'TERRENOS INDUSTRIAIS', "RURAIS" => 'RURAIS', "GRANJAS" => 'GRANJAS', "CHACARAS" => 'CHÁCARAS',
            "CHACARAS-EM-AREA-LAZER" => 'CHÁCARAS EM ÁREA LAZER', "CHACARAS-EM-CONDOMINIO" => 'CHÁCARAS EM CONDOMÍNIO', "SITIOS" => 'SÍTIOS', "SITIOS" => 'SITIO', "FAZENDAS" => 'FAZENDAS', "HARAS" => 'HARAS');


        /* --------- DINAMICOS -------- */
    // CIDADES

        if (!session()->has('SUB100_cidadesPalavrasChave')):
            foreach (Properties::getCities() as $rowCidade) {
                session()->push('SUB100_cidadesPalavrasChave.' . $rowCidade['codigocidade'], removeAccents($rowCidade['nome']));
            }
        endif;

        if (count(session('SUB100_cidadesPalavrasChave'))):
            foreach (session('SUB100_cidadesPalavrasChave') as $codigoCidade => $nomeCidade):
                $kw_cidades[$codigoCidade] = $nomeCidade;
            endforeach;
        endif;

        // BAIRROS
        if (!session('SUB100_cidadesPalavrasChave')):
            foreach (Properties::getCities() as $rowBairro):
                session()->push('SUB100_cidadesPalavrasChave.' . $rowCidade['codigo'], htmlentities($rowBairro['nome']));
            endforeach;
        endif;

        if (count(session('SUB100_cidadesPalavrasChave'))):
            foreach (session('SUB100_cidadesPalavrasChave') as $codigoBairro => $nomeBairro):
                $kw_bairros[$codigoBairro] = str_replace($caract_errado, $caract_certo, $nomeBairro);
            endforeach;
        endif;

        // EDIFICIOS
        if (!session('SUB100_cidadesPalavrasChave')):

            foreach (Properties::getEdification() as $rowEdificio):
                session()->push('SUB100_cidadesPalavrasChave.' . $rowEdificio['codigo'], htmlentities($rowEdificio['nome']));
            endforeach;

        endif;

        if (count(session('SUB100_cidadesPalavrasChave'))):
            $edificioRemover = array("/\bcond\b/", "/\bcondominio\b/", "/\bed\b/", "/\bed.\b/", "/\bedificio\b/", "/\bres\b/", "/\bresidencial\b/");
            foreach (session('SUB100_cidadesPalavrasChave') as $codigoEdificio => $nomeEdificio):
                $kw_edificio[$codigoEdificio] = trim(array_first(preg_replace($edificioRemover, "", str_replace($caract_errado, $caract_certo, $nomeEdificio))));
            endforeach;
        endif;


        // RUAS
        if (!session()->has('SUB100_cidadesPalavrasChave')):

            foreach (Properties::getStreet() as $rowRua):
                session()->push('SUB100_cidadesPalavrasChave.' . $rowRua['codigo'], htmlentities($rowRua['nome']));
            endforeach;

        endif;

        if (count(session('SUB100_cidadesPalavrasChave'))):
            foreach (session('SUB100_cidadesPalavrasChave') as $codigoRua => $nomeRua):
                $kw_rua[$codigoRua] = $nomeRua;
            endforeach;
        endif;

        // Trata numeros digitados juntos com os termos. Ex: 4quartos, 2garagens
        foreach ($arrPalavras as $index => $gettermo):
    
            if (is_numeric($gettermo[0]) && !is_numeric($gettermo[1]) && $gettermo[1] != " " && $gettermo[1]):
                array_push($arrPalavras, $gettermo[0]);
                array_push($arrPalavras, substr($gettermo, 1));
            endif;
        endforeach;

        $exibircompleta = 0;
    // Faz o loop com as palavras digitadas na busca
        foreach ($arrPalavras as $index => $gettermo):

            // Caso não encontre mais nada e a pessoa digite comprar/alugar etc
            if (in_array($gettermo, $listacompleta)) $exibircompleta = 1;

            // Ignora espacos em branco ou vazias
            if (!$gettermo) continue;

            $nomeComposto = array_search($gettermo, $kw_compostos);

            // Verifica se é um nome composto, se SIM ele contatena com a próxima palavra
            if (($nomeComposto !== false) and (!is_numeric($gettermo))):
                $primeira = $nomeComposto;
                $gettermo = $kw_compostos[$primeira] . " " . $arrPalavras[($index + 1)];

                $composto_bairros = array_search($kw_compostos[$primeira], $kw_comp_bairros);
                $composto_edificios = array_search($kw_compostos[$primeira], $kw_comp_edificios);
                $composto_ruas = array_search($kw_compostos[$primeira], $kw_comp_ruas);

            endif;

            // procura as excecoes e ignora se for encontrado
            if ((array_search($gettermo, $excecoes) === false) and (!is_numeric($gettermo))):

                if (!$found_tipo): $termo_tipo = b_array2('tipo', $kw_tipos, $gettermo); endif;
                if (!$found_cidade): $termo_cidade = b_array2('cidade', $kw_cidades, $gettermo); endif;

                // Se achar uma cidade e bairro com o mesmo nome, ele ignora o bairro (Ex.: bairro Maringa Velho)
                if ((!$found_bairro) and (!b_array2('cidade', $kw_cidades, $gettermo))): $termo_bairro = b_array2('bairro', $kw_bairros, $gettermo); endif;
                if (!$found_edificio): $termo_edificio = b_array2('edificio', $kw_edificio, $gettermo); endif;
                if (!$found_rua): $termo_rua = b_array2('rua', $kw_rua, $gettermo); endif;

                if (!$found_tipo): $found_tipo = $termo_tipo; endif;
                if (!$found_cidade): $found_cidade = $termo_cidade; endif;
                if (!$found_bairro): $found_bairro = $termo_bairro; endif;
                if (!$found_edificio): $found_edificio = $termo_edificio; endif;
                if (!$found_rua): $found_rua = $termo_rua; endif;

            // filtra se o termo é um numero para interpretá-lo como QUARTO ou SUITE
            elseif (is_numeric($gettermo)):

                $kw_campo = $arrPalavras[$index + 1];

                if (soundex(rtrim($kw_campo, 's')) == soundex("quarto")) $_GET['b_quartos'] = $getquartos = $getdormitorios = ltrim($gettermo, "0");
                if (soundex(rtrim($kw_campo, 's')) == soundex("suite")) $_GET['b_suites'] = $getsuites = ltrim($gettermo, "0");
                if (soundex(rtrim($kw_campo, 'm')) == soundex("garage") or soundex(rtrim($kw_campo, 's')) == soundex("vaga")
                    or soundex(rtrim($kw_campo, 'ns')) == soundex("garage")) $_GET['b_garagens'] = $getgaragens = ltrim($gettermo, "0");

            endif;

        endforeach;


        if (is_array($found_tipo['tipo'])) $found_tipo['tipo'] = implode(",", $found_tipo['tipo']);
        if (is_array($found_cidade['cidade'])) $found_cidade['cidade'] = implode(",", $found_cidade['cidade']);
        if (is_array($found_bairro['bairro'])) $found_bairro['bairro'] = implode(",", $found_bairro['bairro']);
        if (is_array($found_edificio['edificio'])) $found_edificio['edificio'] = implode(",", $found_edificio['edificio']);
        if (is_array($found_rua['rua'])) $found_rua['rua'] = implode(",", $found_rua['rua']);
        $gettipo = $_GET['b_tipo'];
        if ($found_tipo['tipo']) $gettipo = $_GET['b_tipo'] = strtoupper($found_tipo['tipo']);
        if ($found_cidade['cidade']) $_GET['b_cidade'] = $getcidade = $found_cidade['cidade'];
        if ($found_bairro['bairro']) $_GET['b_bairro'] = $getbairro = $found_bairro['bairro'];
        if ($found_edificio['edificio']) $_GET['b_edificio'] = $getedificio = $found_edificio['edificio'];
        if ($found_rua['rua']) $_GET['b_rua'] = $getrua = $found_rua['rua'];

        $resultado_palavras = ($gettipo or $getsuites or $getdormitorios or $getbairro or $getgaragens or $getedificio or $getrua) ? 1 : 0;

        return $interpretacao = 'tipo=' . $gettipo . ' | suites=' . $getsuites . ' | quartos=' . $getdormitorios . ' | cidade=' . $getcidade . ' | bairro=' . $getbairro . ' | garagem=' . $getgaragens . ' | edificio=' . $getedificio . ' | rua=' . $getrua;
    }
}

function strposa($haystack, $needles=array(), $offset=0) {
    $chr = array();
    foreach($needles as $needle) {
        $res = strpos($haystack, $needle, $offset);
        if ($res !== false) $chr[$needle] = $res;
    }
    if(empty($chr)) return false;
    return min($chr);
}

function color_luminance( $hex, $percent ) {

    // validate hex string

    $hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
    $new_hex = '#';

    if ( strlen( $hex ) < 6 ) {
        $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
    }

    // convert to decimal and change luminosity
    for ($i = 0; $i < 3; $i++) {
        $dec = hexdec( substr( $hex, $i*2, 2 ) );
        $dec = min( max( 0, $dec + $dec * $percent ), 255 );
        $new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
    }

    return $new_hex;
}


function hex2rgb($colour) {
    if ($colour[0] == '#') {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array(
            $colour[0] . $colour[1],
            $colour[2] . $colour[3],
            $colour[4] . $colour[5]
        );
    } elseif (strlen($colour) == 3) {
        list($r, $g, $b) = array(
            $colour[0] . $colour[0],
            $colour[1] . $colour[1],
            $colour[2] . $colour[2]
        );
    } else {
        return false;
    }

    return array(
        'red' => hexdec($r),
        'green' => hexdec($g),
        'blue' => hexdec($b)
    );
}

function rgba($hex){
    $rgb = hex2rgb($hex);
    if (!$rgb) return "0, 0, 0";
    return "{$rgb['red']}, {$rgb['green']}, {$rgb['blue']}";
}

function ColorRgb($hex, $percent) {
    $rgb = hex2rgb($hex);
    if (!$rgb) return false;

    foreach ($rgb as $key => $value) {
        if ($percent > 0) {
            // Clarear
            $rgb[$key] = round($value + (255 - $value) * ($percent / 100));
        } else {
            // Escurecer
            $rgb[$key] = round($value * (1 + ($percent / 100)));
        }
        // Garante que esteja entre 0 e 255
        $rgb[$key] = max(0, min(255, $rgb[$key]));
    }

    return "rgb({$rgb['red']}, {$rgb['green']}, {$rgb['blue']})";
}

function removeAccents($str)
{

    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", " ", "/");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "-", "-");

    $str = str_replace($array1, $array2, $str);
    $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', $str);
    return $str;
}

function trocaSinonimos($var)
{

    $var = str_replace(',', '', $var);

    $A[] = "uma";
    $B[] = "1";
    $A[] = "um";
    $B[] = "1";
    $A[] = "dois";
    $B[] = "2";
    $A[] = "tres";
    $B[] = "3";
    $A[] = "quatro";
    $B[] = "4";
    $A[] = "cinco";
    $B[] = "5";
    $A[] = "seis";
    $B[] = "6";
    $A[] = "sete";
    $B[] = "7";
    $A[] = "oito";
    $B[] = "8";
    $A[] = "nove";
    $B[] = "9";
    $A[] = "dez";
    $B[] = "10";
    $A[] = "barracao";
    $B[] = "barracoes";
    $A[] = "jardim";
    $B[] = "jd";
    $A[] = "vila";
    $B[] = "vl";
    $A[] = "parque";
    $B[] = "pq";
    $A[] = "conjunto";
    $B[] = "cj";
    $A[] = "loteamento";
    $B[] = "lot";
    $A[] = "residencial";
    $B[] = "res";
    $A[] = "condominio";
    $B[] = "cond";
    $A[] = "dormitorio";
    $B[] = "quartos";
    $A[] = "dormitorios";
    $B[] = "quartos";
    $A[] = "data";
    $B[] = "terrenos";
    $A[] = "salao";
    $B[] = "salas";

    $A[] = "kit net";
    $B[] = "kitnet";
    $A[] = "kitinet";
    $B[] = "kitnet";

    $A[] = "apto";
    $B[] = "apartamentos";
    $A[] = "aptos";
    $B[] = "apartamentos";
    $A[] = "ap";
    $B[] = "apartamentos";
    $A[] = "ape";
    $B[] = "apartamentos";
    $A[] = "apt";
    $B[] = "apartamentos";
    $A[] = "apta";
    $B[] = "apartamentos";

    foreach ($A as $sinonimo) $kw_sinonimos_preg[] = "/\b" . $sinonimo . "\b/";

    return preg_replace($kw_sinonimos_preg, $B, $var);

}

//Verifica se existe um módulo.
function getModule($name)
{
    if (hasModule($name)) {
        foreach (getModules() as $module) {
            if ($module['name'] === $name) {
                return $module;
            }
        }
    }
}

// Verifica se existe o negócio
function hasTrading($name)
{
    return in_array($name, getTradings());
}

//Retorna o ID do cliente
function getPanelId(): int
{
    return (int)config('clients.' . session('client') . '.panel.panel_id');
}

//Return id client in property
function getPropertyId()
{
    return config('clients.' . session('client') . '.property.property_id') <> '' ? config('clients.' . session('client') . '.property.property_id')  :
           config('clients.' . session('client') . '.property.property_partners') ;
}

//Retorna parceiro
function getPropertyPartner()
{
    return config('clients.' . session('client') . '.property.property_partners');
}

// Retorna Estado Property
function getPropertyState()
{
    return config('clients.' . session('client') . '.property.property_state');
}
 
//Retorna Cidade Property
function getPropertyCity()
{
    return config('clients.' . session('client') . '.property.property_city');
}

//Retorna todos os módulos
function getModules()
{
    $propertyId = getPropertyId(); // ✅ Usar property ID do cliente
   Cache::forget('site_modules_' . $propertyId);
   return Cache::remember('site_modules_' . $propertyId, 3600, function() {
        return config('clients.' . session('client') . '.layout.modules');
   });
}

function hasModules($name = null)
{
    $propertyId = getPropertyId();
    
    if ($name === null) {
        // Se não passou nome, retorna todos os módulos
        return Cache::remember('all_modules_' . $propertyId, 3600, function() {
            return config('clients.' . session('client') . '.layout.modules');
        });
    }
    
    // Se passou nome específico, verifica se existe
    return Cache::remember('hasModules_' . $name . '_' . $propertyId, 3600, function() use ($name) {
        $modules = config('clients.' . session('client') . '.layout.modules');
        if (!is_array($modules)) return false;

        foreach ($modules as $module) {
            if (isset($module['name']) && $module['name'] === $name) {
                return true;
            }
        }
        return false;
    });
}
//Retorna os negócios
/*function getTradings()
{
    return config('clients.' . session('client') . '.property.property_trading');
}*/

function getTradings($tipo = null)
{
    $trading = config('clients.' . session('client') . '.property.property_trading');
    if ($tipo === null) {
        return $trading;
    }
    // Compara ignorando maiúsculas/minúsculas
    return in_array(mb_strtolower($tipo), array_map('mb_strtolower', $trading));
}


//Retorna os negócios
function getDisplay()
{
    return config('clients.' . session('client') . '.property.property_display');
}

//Retorna os endereços
function getAddress()
{
    return config('clients.' . session('client') . '.address');
}

//Retorna o dominio 
function getSiteAddress()
{
    return config('clients.' . session('client') . '.general.site_address');
}

//Retorna o dominio 
function getPortalSUB100()
{
    return "https://sub100.com.br";
}

// Retona os bancos
function getBanking()
{
    return config('clients.' . session('client') . '.bank_financing');
}

// Ativar os bancos
function getBankingLinkSimulator()
{
    $banksLinksSimulator = [
        'bradesco' => 'https://banco.bradesco/html/classic/produtos-servicos/emprestimo-e-financiamento/encontre-seu-credito/simuladores-imoveis.shtm#box1-comprar',
        'bancodobrasil' => 'https://www42.bb.com.br/portalbb/imobiliario/creditoimobiliario/simular,802,2250,2250.bbx?pk_vid=13cacc3cb4ba57311598291386a176f0',
        'caixa' => 'http://www8.caixa.gov.br/siopiinternet-web/?method=inicializarCasoUso',
        'santander' => 'https://www.santander.com.br/portal/wps/script/templates/GCMRequest.do?page=5516',
        'sicoob' => 'https://www.sicoob.com.br/',
        'sicredi' => 'https://www.sicredi.com.br/home/',
        'itau' => 'https://www.itau.com.br/emprestimos-financiamentos/credito-imobiliario/simulador/'
    ];
    $simulator = [];
    foreach (getBanking() as $bank) {
        if (array_key_exists($bank, $banksLinksSimulator)) {
            $simulator[] = [$bank, $banksLinksSimulator[$bank]];
        }
    }
    return $simulator;
}

//Retorna o Email
function getEmails()
{
    return config('clients.' . session('client') . '.email');
}

// Retorna o nome do site
function getSiteName()
{
    return config('clients.' . session('client') . '.general.site_name');
}

function getSiteCreci()
{
    return config('clients.' . session('client') . '.general.site_creci');
}

function getColorTopFont()
{
    return config('clients.' . session('client') . '.colors.color_top_font');
}

function getColorFooterFont()
{
    return config('clients.' . session('client') . '.colors.color_footer_font');
}

function getImageBackground()
{
    $clientId = session('client');
    return Cache::remember('image_Backgrounds_' . $clientId, 3600, function() {
        if (config('clients.' . session('client') . '.images.image_background')) {
            //$dataImagem = Panel::clienteByImagem("background");
            $dataImagem = "2025";
            $ImageBackground = env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.image_background') . "?id=" . $dataImagem ;
            if (url_existsNew($ImageBackground)) {
                return $ImageBackground;
            }
        }
        return false;
    });
}

function getImageBackground2()
{
    $clientId = session('client');
    return Cache::remember('image_background2_' . $clientId, 3600, function() {
        if (config('clients.' . session('client') . '.images.image_background2') && url_exists(env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.image_background2'))) {
            //$dataImagem = Panel::clienteByImagem("background2");
            $dataImagem = "2025";
            return env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.image_background2')."?id=".$dataImagem ;
        }
    });
}

function getImageLogo()
{

        if (config('clients.' . session('client') . '.images.logo_main')) {
           // $dataImagem = Panel::clienteByImagem("logo");
           $dataImagem = "2025";
            return env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.logo_main')."?id=".$dataImagem ;
        }

}

function getEmailLogo()
{
    $clientId = session('client');

    return Cache::remember('image_logo_email_' . $clientId, 3600, function() {
        if (config('clients.' . session('client') . '.images.logo_email')) {
            //$dataImagem = Panel::clienteByImagem("logo");
            $dataImagem = "2025";
            return env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.logo_email')."?id=".$dataImagem ;
        }
        //$dataImagem = Panel::clienteByImagem("logo");
        $dataImagem = "2025";
        return env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.logo_main')."?id=".$dataImagem ;
    });
}

function getImageFooter()
{
    $clientId = session('client');
    return Cache::remember('image_footer_' . $clientId, 3600, function() {
        if (config('clients.' . session('client') . '.images.logo_footer')) {
            //$dataImagem = Panel::clienteByImagem("rodape");
            $dataImagem = "2025";
            return env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.logo_footer')."?id=".$dataImagem ;
        }
    });
}

function getSvgLogo($parameter = null, $position = null)
{
    $clientId = session('client');
    $cacheKey = 'svgLogo_' . $position . '_' . $clientId;
    
    $logoData = Cache::remember($cacheKey, 86400, function() use ($position) {
       // $dataImagem = Panel::clienteByImagem("logo");
        $dataImagem = "2025";
        $logoPath = $position ? 'logo_' . $position : 'logo_main';

        $logo = env('PANEL_UPLOADPATH_S3') . config('clients.' . session('client') . '.images.' . $logoPath) . "?id=" . $dataImagem ;
        $logoSvg = str_replace('.png', '.svg', $logo);
        $css = ($position == 'footer')? 'rodapeSvg' : 'logoSvg';

        if (url_existsNew($logoSvg)) {
            return [
                'src' => $logoSvg,
                'css' => $css,
            ];
        }

        return [
            'src' => $logo,
            'css' => 'logo'
        ];
    });
    
    if ($parameter === 'src') {
        return $logoData['src'];
    } elseif ($parameter === 'css') {
        return $logoData['css'];
    }
    
    return $logoData['src'];
}

function url_existsNew($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // ✅ Timeout de 5 segundos
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // ✅ Timeout de conexão 3s
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // ✅ Não seguir redirects
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ✅ Para HTTPS
    
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($result !== false && $code == 200);
}


//Retorna o cidade
function getCity()
{
    if ($city = getAddressConfig()) {
        return $city[0]['city'];
    }
    return '';
}

//Retorna o estado
function getState()
{
    if ($city = getAddressConfig()) {
        return $city[0]['state'];
    }

    return '';

}

// Retorna o email
function getEmail($type)
{
    // Chamada da função para coletar os e-mails.
    $emails = getEmails();
    $list = "";

    // Recebe os e-mails se o tipo existir
    if (isset($emails["email_$type"])) {
        $list = $emails["email_$type"];
    }

    return $list;
}
function normalizaCampo($campo) {
    if (is_array($campo)) return implode(', ', array_map('trim', $campo));
    return trim((string)$campo);
}
// Retorna o Telefone
function getPhones(string $type = null, bool $first = null)
{
    $phones = collect(config('clients.' . session('client') . '.phone'));
    if (!empty($type)) {
        return $phones->where('type', $type);
    }
    if ($first) {
        return $phones->first();
    }
    return $phones->all();
}

//Pegar o endereço no config
function getAddressConfig()
{
    $address = getAddress();
    if (isset($address)) {
        return $address;
    }

    return [];
}

function getGoogleMapsUrls($addressData = null)
{
    $clientId = session('client');
    $cacheKey = 'google_maps_urls_' . $clientId;
    
    return Cache::remember($cacheKey, 3600, function() use ($addressData) {
        // Se não passou dados, busca do config
        if (!$addressData) {
            $addressData = collect(getAddress())->first();
        }
        
        // Se não tem endereço, retorna URLs de fallback
        if (empty($addressData) || empty($addressData['address'])) {
            $siteName = getSiteName();
            $fallbackUrl = 'https://www.google.com/maps/search/' . urlencode($siteName);
            
            return [
                'primary' => $fallbackUrl,
                'secondary' => $fallbackUrl,
                'display_name' => $siteName,
                'has_address' => false
            ];
        }
        
        // Monta os dados do endereço
        $endereco = $addressData['address'];
        $numero = !empty($addressData['number']) ? ', ' . $addressData['number'] : '';
        $bairro = $addressData['neighborhood'] ?? '';
        $cidade = $addressData['city'] ?? '';
        $estado = $addressData['state'] ?? '';
        $cep = !empty($addressData['zip_code']) ? ' CEP ' . $addressData['zip_code'] : '';
        
        $nomeEmpresa = getSiteName();
        $displayName = !empty($addressData['title']) ? $addressData['title'] : $nomeEmpresa;
        
        // Monta endereço completo
        $enderecoCompleto = trim($endereco . $numero . ', ' . $bairro . $cep . ', ' . $cidade . ' - ' . $estado);
        
        // URLs otimizadas
        $primaryUrl = 'https://www.google.com/maps/search/' . urlencode($nomeEmpresa . ' ' . $cidade . ' ' . $estado);
        $secondaryUrl = 'https://www.google.com/maps/search/' . urlencode($enderecoCompleto);
        
        return [
            'primary' => $primaryUrl,
            'secondary' => $secondaryUrl,
            'display_name' => $displayName,
            'has_address' => true,
            'address_parts' => [
                'endereco' => $endereco,
                'numero' => $numero,
                'bairro' => $bairro,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep
            ]
        ];
    });
}

/**
 * Gera HTML do endereço formatado
 * @param array|null $addressData Dados do endereço (opcional)
 * @return string HTML formatado
 */
function getFormattedAddress($addressData = null)
{
    $clientId = session('client');
    $cacheKey = 'formatted_address_' . $clientId;
    
    return Cache::remember($cacheKey, 3600, function() use ($addressData) {
        $mapsData = getGoogleMapsUrls($addressData);
        
        if (!$mapsData['has_address']) {
            return '<p>Consulte nossos contatos para mais informações</p>';
        }
        
        $parts = $mapsData['address_parts'];
        $endereco = $parts['endereco'] . $parts['numero'];
        $bairro = $parts['bairro'] . ($parts['cep'] ? ' - ' . $parts['cep'] : '');
        $cidade = $parts['cidade'] . ' - ' . $parts['estado'];
        
        return "<p>{$endereco}<br>{$bairro}<br>{$cidade}</p>";
    });
}

//Retorna Cidade Property
function getSocialNetwork()
{
    return config('clients.' . session('client') . '.social_network');
}

//Formato Data
function dateFormat($date_conv, $format = 'normal')
{
    if ($format == 'normal')
        $date = substr($date_conv, 8, 2) . '/' . substr($date_conv, 5, 2) . '/' . substr($date_conv, 0, 4);
    else {
        $date = explode('/', $date_conv);
        $date = $date[2] . '-' . $date[1] . '-' . $date[0];
    }
    return $date;
}

//Verificar o url
function url_exists($url) {

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($code == 200); // verifica se recebe "status OK"
}

//Data Atualizacao
function DataAtualizacao($date){
    $newdate = str_replace(array(' ', "-", ":"), "",$date);
    return $newdate;
}

// Converter para UF
function allStates()
{
    return array('AC:Acre', 'AL:Alagoas', 'AM:Amazonas', 'BA:Bahia', 'CE:Ceará', 'DF:Distrito Federal', 'ES:Espírito Santo', 'GO:Goiás', 'MA:Maranhão',
        'MG:Minas Gerais', 'MS:Mato Grosso do Sul', 'MT:Mato Grosso', 'PA:Pará', 'PB:Paraíba', 'PR:Paraná', 'PE:Pernambuco', 'PI:Piauí', 'RJ:Rio de Janeiro',
        'RN:Rio Grande do Norte', 'RO:Rondônia', 'RR:Roraima', 'RS:Rio Grande do Sul', 'SC:Santa Catarina', 'SP:São Paulo', 'SE:Sergipe', 'TO:Tocantins');
}

//Mudar data
function mudaData($data, $formato)
{
    if ($data):
        return str_replace("-", "/", date($formato, strtotime($data)));
    else:
        return "";
    endif;
}

function is_assoc(array $arr): bool {
    return array_keys($arr) !== range(0, count($arr) - 1);
}
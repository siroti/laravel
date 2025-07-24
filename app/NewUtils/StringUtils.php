<?php

namespace App\NewUtils;


class StringUtils
{

    public static function getDomain()
    {
        $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }

    public static function normalizes_name($nome, string $utf8 = null): string
    {
        if (empty($nome)) {
            return '';
        }

        if ($utf8) {
            $nome = htmlentities($nome, ENT_NOQUOTES, 'UTF-8');
        }

        /**
         * Constantes definidas para melhor legibilidade do código. O prefixo NN_ indica que
         * seu uso está relacionado ao método público e estático normalizarNome().
         */
        $NN_PONTO = '\.';
        $NN_PONTO_ESPACO = '. ';
        $NN_ESPACO = ' ';
        $NN_REGEX_MULTIPLOS_ESPACOS = '\s+';
        $NN_REGEX_NUMERO_ROMANO =
            '^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(AC|AL|AM|BA|CE|DF|ES|GO|MA|MG|MS|MT|PA|PB|PE|PI|PR|RJ|RN|RO|RR|RS|SC|SE|SP|TO|IX|IV|V?I{0,3})$';

        $nome = mb_strtolower($nome);
        /*
         * A primeira tarefa da normalização é lidar com partes do nome que
         * porventura estejam abreviadas,considerando-se para tanto a existência de
         * pontos finais (p. ex. JOÃO A. DA SILVA, onde "A." é uma parte abreviada).
         * Dado que mais à frente dividiremos o nome em partes tomando em
         * consideração o caracter de espaço (" "), precisamos garantir que haja um
         * espaço após o ponto. Fazemos isso substituindo todas as ocorrências do
         * ponto por uma sequência de ponto e espaço.
         */
        $nome = mb_ereg_replace($NN_PONTO, $NN_PONTO_ESPACO, $nome);

        /*
         * O procedimento anterior, ou mesmo a digitação errônea, podem ter
         * introduzido espaços múltiplos entre as partes do nome, o que é totalmente
         * indesejado. Para corrigir essa questão, utilizamos uma substituição
         * baseada em expressão regular, a qual trocará todas as ocorrências de
         * espaços múltiplos por espaços simples.
         */
        $nome = mb_ereg_replace($NN_REGEX_MULTIPLOS_ESPACOS, $NN_ESPACO,
            $nome);

        /*
         * Isso feito, podemos fazer a capitalização "bruta", deixando cada parte do
         * nome com a primeira letra maiúscula e as demais minúsculas. Assim,
         * JOÃO DA SILVA => João Da Silva.
         */
        $nome = mb_convert_case($nome, MB_CASE_TITLE, "UTF-8");
        //$nome = $this->mb_convert_case_utf8_variation($nome);
        //$nome = $this->capitalize($nome);

        /*
         * Nesse ponto, dividimos o nome em partes, para trabalhar com cada uma
         * delas separadamente.
         */
        $partesNome = mb_split($NN_ESPACO, $nome);

        /*
         * A seguir, são definidas as exceções à regra de capitalização. Como
         * sabemos, alguns conectivos e preposições da língua portuguesa e de outras
         * línguas jamais são utilizadas com a primeira letra maiúscula.
         * Essa lista de exceções baseia-se na minha experiência pessoal, e pode ser
         * adaptada, expandida ou mesmo reduzida conforme as necessidades de cada
         * caso.
         */
        $excecoes = array(
            'com', 'de', 'di', 'do', 'da', 'dos', 'das', 'dello', 'della',
            'dalla', 'dal', 'del', 'e', 'ou', 'em', 'na', 'no', 'nas', 'nos', 'van', 'von',
            'y'
        );

        for ($i = 0; $i < count($partesNome); ++$i) {

            /*
             * Verificamos cada parte do nome contra a lista de exceções. Caso haja
             * correspondência, a parte do nome em questão é convertida para letras
             * minúsculas.
             */
            foreach ($excecoes as $excecao)
                if (mb_strtolower($partesNome[$i]) == mb_strtolower($excecao))
                    $partesNome[$i] = $excecao;

            /*
             * Uma situação rara em nomes de pessoas, mas bastante comum em nomes de
             * logradouros, é a presença de numerais romanos, os quais, como é sabido,
             * são utilizados em letras MAIÚSCULAS.
             * No site
             * http://htmlcoderhelper.com/how-do-you-match-only-valid-roman-numerals-with-a-regular-expression/,
             * encontrei uma expressão regular para a identificação dos ditos
             * numerais. Com isso, basta testar se há uma correspondência e, em caso
             * positivo, passar a parte do nome para MAIÚSCULAS. Assim, o que antes
             * era "Av. Papa João Xxiii" passa para "Av. Papa João XXIII".
             */
          /*  if (mb_ereg_match($NN_REGEX_NUMERO_ROMANO,
                mb_strtoupper($partesNome[$i])))
                $partesNome[$i] = mb_strtoupper($partesNome[$i]);*/
                $NN_REGEX_NUMERO_ROMANO = '/^M{0,4}(CM|CD|D?C{0,3})?(XC|XL|L?X{0,3})?(IX|IV|V?I{0,3})?$/i';
        if (preg_match($NN_REGEX_NUMERO_ROMANO, $partesNome[$i])) {
            $partesNome[$i] = strtoupper($partesNome[$i]);
        }

        }

        /*
         * Finalmente, basta juntar novamente todas as partes do nome, colocando um
         * espaço entre elas.
         */
        return implode($NN_ESPACO, $partesNome);
    }

    public static function removeAccents(string $str): string
    {
        $map = [
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'é' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ç' => 'c',
            'Á' => 'A',
            'À' => 'A',
            'Ã' => 'A',
            'Â' => 'A',
            'É' => 'E',
            'Ê' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
            'Ç' => 'C'
        ];

        return strtr($str, $map);
    }

    public static function url(string $text): string
    {
        $text = self::removeAccents($text);
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
        $text = trim($text, '-');
        $text = mb_strtolower($text);
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) return '';

        return $text;
    }

    public static function getEstadoNome($sigla)
    {
        $estados = self::listaEstados();
        return $estados[strtoupper($sigla)] ?? null;
    }
    public static function listaTiposImoveis()
    {
        return [
            'Apartamento',
            'Casa',
            'Sobrado',
            'Sobreloja',
            'Pousada',
            'Casa/Sobrado em condomínio',
            'Kitnet/Studio',
            'Flat',
            'Loft',
            'Chalé',
            'Porão',
            'Sala',
            'Sala em edifício',
            'Sala em shopping',
            'Sala térrea',
            'Loja',
            'Prédio inteiro',
            'Salão',
            'Barracão',
            'Depósito',
            'Galpão',
            'Armazém',
            'Garagem',
            'Box',
            'Negócio',
            'Terreno residencial',
            'Terreno em condomínio',
            'Terreno comercial',
            'Terreno industrial',
            'Chácara',
            'Chácara em área de lazer',
            'Chácara em condomínio',
            'Sítio',
            'Fazenda',
            'Haras',
        ];
    }

    public static function listaEstados()
    {
        return [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ];
    }

}
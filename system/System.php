<?php
/**
 * Kernel of engine
 * 
 * PHP version 5
 * 
 * @category System
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @version  GIT: $Id$
 * @link     http://michelw.in/
 */

/**
 * The kernel of Kaffeine engine
 * 
 * @name     System
 * @category System
 * @package  Kaffeine
 * @author   Michel Wilhelm <michelwilhelm@gmail.com>
 * @license  GPL http://michelw.in
 * @link     http://michelw.in/
 */

class System
{

    /**
    * Vetor que contém os arquivos CSS
    * que devem ser carregados no bootstrap
    *
    * @var array
    */
    private static $_css_array=Array();

    /**
    * Vetor que contém os arquivos Javascript
    * que devem ser carregados no bootstrap
    * @var array
    */
    private static $_js_array=Array();


    /**
     * constructor
     * 
     * @return Bool
     */
    function __construct()
    {
        
    }

    /**
     * Render a view
     *
     * @param String $_view The view to call
     *
     * @return Bool
     */
    public static function render($_view, $_args=Array()) 
    {

        $_view = str_replace(':', '/', $_view);

        Twig_Autoloader::register();
        $twig   = new Twig_Environment(
            new Twig_Loader_Filesystem(DIR_APP . 'views'), 
            Array(
                //'cache' => DIR_CACHE,
                'cache' => false,
                'autoescape' => false
            )
        );

        $template = $twig->loadTemplate($_view . '.html.twig');
        echo $template->render($_args);

    }
    
    /**
     * Debug a variable
     *
     * @param ObjectArray $_var  Put a Object or Array to debug
     * @param Bool        $_html force htmlformat or not
     *
     * @name pr
     *
     * @return Bool
     */
    public static function pr($_var = Array(), $_html = false)
    {
        if ($_html === true) {              
            $_txt = print_r($_var, true);
            $_txt = str_replace(
                '[', 
                '<span style="font-weight:bold; color:green">[</span>', 
                $_txt
            );
            $_txt = str_replace(
                ']', 
                '<span style="font-weight:bold; color:green">]</span>', 
                $_txt
            );
            $_txt = str_replace(
                '=>', 
                '<span style="font-weight:bold; color:blue">=></span>', 
                $_txt
            );
            $_txt = str_replace(
                'Array', 
                '<span style="font-weight:bold; color://000">Array</span>', 
                $_txt
            );
            $_txt = str_replace(
                '(', 
                '<span style="font-weight:bold; color://000">(</span>', 
                $_txt
            );
            $_txt = str_replace(
                ')', 
                '<span style="font-weight:bold; color://000">)</span>', 
                $_txt
            );
        } else {
            $_txt = print_r($_var, true);
        }
        print '<pre style="margin: 10px 0; padding: 2px;border:1px solid //eee;';
        print 'background://fff;color://000;font-size: 12px';
        print 'font-family: Arial, Sans">';
        print $_txt;
        print '</pre>';

        return true;
    }

    /**
     * Returns the revision id of the present GIT project
     *
     * @name git_last_commit
     *
     * @return String
     */
    public static function gitLastCommit() 
    {
        $_str = '';
        $_tmp = explode(
            ' ', 
            shell_exec("git log -1 --pretty=format:'%h - %s (%ci)' --abbrev-commit")
        );
        if (count($_tmp)>0) {
            $_str = $_tmp[0];
        }

        return $_str;
    }

    /**
     * Works with the url_rewrite mod
     *
     * @param Integer $_index The url index
     *
     * @name rewrite
     *
     * @return Misc
     */
    public static function rewrite ($_index='none') 
    {
        $_rewrite      = explode(
            '/', 
            str_ireplace(
                REWRITE_EXT, 
                '', 
                self::removeLastChar(
                    str_replace(
                        str_replace(
                            __PATH__, 
                            '/', 
                            $_SERVER['SCRIPT_NAME']
                        ), 
                        '', 
                        str_replace(
                            __PATH__, 
                            '/', 
                            $_SERVER['REQUEST_URI']
                        )
                    ), 
                    '/'
                )
            )
        );
        $_request_uri = str_ireplace(
            REWRITE_EXT, 
            '', 
            str_replace(
                str_replace(
                    __PATH__, 
                    '/', 
                    $_SERVER['SCRIPT_NAME']
                ), 
                '', 
                str_replace(
                    __PATH__, 
                    '/', 
                    $_SERVER['REQUEST_URI']
                )
            )
        );
        $_rewrite['0'] = $_request_uri;
        
        $_return = null;
        
        if (is_int($_index)) {
            if (isset($_rewrite[ $_index ])) {
                $_return = self::removeLastChar($_rewrite[ $_index ], '/');
                if (strpos($_return, '?')>0) {
                    $_tmp = explode('?', $_return);
                    $_return = $_tmp[0];
                }
            }
            return $_return;
        } else {
            return $_rewrite;
        }
    }

    /**
     * Removes the last char of a string
     *
     * @param String $_str    The string
     * @param String $_ultimo Last char
     *
     * @name removeLastChar
     *
     * @return String
     */
    public static function removeLastChar ($_str=false, $_ultimo='')
    {
        $_tmp = '';
        if ($_str[strlen($_str)-1] === $_ultimo || $_ultimo === '') {

            // Percorre a string excluindo a última posição
            $i=0;
            $_len = strlen($_str);
            while ($i <= ($_len - 2)) {
                // Concatenando a string
                $_tmp .=  $_str[$i];
                
                $i++;
            }

            // Retornando a string formatada
            return $_tmp;
        } else {
            return $_str;
        }
    }

    public static function location ($_link=false, $_return = false, $_ext='none')
    {
        $_dns  = __DNS__;
        $_path = __PATH__;

        if ($_ext === 'none') {
            $_ext  = REWRITE_EXT;
        } elseif ($_ext === false) {
            $_ext = '';
        } else {
            $_ext  = $_ext;
        }

        if ($_link['0'] === '/' && $_link['1'] !== '/') {
            for ($i=1; $i<=strlen($_link); $i++) {
                $_tmp .= $_link[$i];
            }
            $_link = $_tmp;
        }

        if ($_SERVER['SERVER_PORT'] !== 80) {
            $_port = ":{$_SERVER['SERVER_PORT']}";
            $_port = '';
        } else {
            $_port = '';
        }

        $_url = str_replace('//','/',"{$_dns}{$_port}{$_path}{$_link}{$_ext}");
        $_url  = "http://{$_url}";

        if ($_return === false) {
            print $_url;
        } else {
            return $_url;
        }
    }

    public static function static_location ($_link=false, $_return = false) 
    {

        if ($_link['0'] === '/') {
            for ($i=1; $i<=strlen($_link); $i++) {
                $_tmp .= $_link[$i];
            }
            $_link = $_tmp;
        }

        $_url  = STATIC_URL . str_replace('//', '/', $_link);

        if ($_return === false) {
            print $_url;
        } else {
            return $_url;
        }
    }

    /**
     * Adiciona ilimitados estilos a lista de carregamento dinamico
     *
     * @static
     * @name addCss
     * @version 0.2
     * @param Array $_css_array
     */
    public static function addCss ($_pos=false, $_path=Array())
    {
        if ($_pos === false) {
            $_pos = count(self::$_css_array);
        }

        // Se for informados vários estilos ao mesmo tempo...
        if (is_array($_path)) {

            // Percorrendo array informado
            foreach ($_path as $_conteudo) {
                self::$_css_array[$_pos] = $_conteudo;
            }

        } else {
            self::$_css_array[$_pos] = $_path;
        }
        return true;
    }

    /**
     * Carrega todos os estilos CSS no cabeçalho da página (<head>)
     *
     * @name loadCss
     * @version 0.2
     * @return string
     * @static
     */
    public static function loadCss ($_args=false)
    {
        $_html = '';

        // Ordenando por índice
        ksort(self::$_css_array);

        // Carregando...
        $_return = '';
        foreach (self::$_css_array as $_key=>$_url) {
            
            if (!empty($_args['return']) && $_args['return'] === true) {
                $_return .= sprintf('<link rel="stylesheet" type="text/css" href="%s"/>'."\n", $_url);
            } else {
                printf('<link rel="stylesheet" type="text/css" href="%s"/>'."\n", $_url);
            }
        }
        
        if (!empty($_args['return']) && $_args['return'] === true) {
            return $_return;
        } else {
            return true;
        }
    }

    public static function loadJs ($_args=false,$_cache=false) {

        ksort(self::$_js_array);

        if ($_cache === true) {

            // Carregando...
            foreach (self::$_js_array as $_key=>$_url) {

                if (preg_match('/^[a-z]+:\/\/' . __DNS__ . '\//', $_url)) {
                    $_file = __ROOT__;
                    $_file .= preg_replace('/^[a-z]+:\/\/'.__DNS__.'\//', '', $_url);
                    $_js .= file_get_contents($_file);
                } elseif (preg_match('/^[a-z]+:/', $_url)) {
                    $_js .= file_get_contents($_url);
                } elseif (!preg_match('/^[a-z]+:/', $_url)) {
                    $_js .= file_get_contents(__ROOT__ . $_url);
                }

                $_js .= "\n\n";

            }

            $_bytes   = mb_strlen($_js);
            $_arquivo = sprintf(CACHE_PATH . "cache.%s.js", $_bytes);

            if (!file_exists($_arquivo)) {
                $_handle = fopen($_arquivo, 'w');
                fwrite($_handle, $_js);
                fclose($_handle);
            }

            // Carrega somente se o arquivo existir
            $_str = '<script type="text/javascript"';
            $_str .= ' rel="javascript" src="%s"></script>' . "\n";
            printf($_str, self::location(URL_CACHE_PATH . "cache.{$_bytes}.js", true ));
        } else {
            // Carregando...
            $_return = '';
            foreach (self::$_js_array as $_key=>$_url) {

                if (!preg_match('/^[a-z]+:/', $_url) && !preg_match('/\/\//', $_url)) {
                    $_url = self::location( $_url, true, false );
                }
                if (!empty($_args['return']) && $_args['return'] === true) {
                    // Carrega somente se o arquivo existir
                    $_return .= sprintf('<script rel="javascript" type="text/javascript" src="%s"></script>' . "\n", $_url);
                } else {
                    // Carrega somente se o arquivo existir
                    printf('<script rel="javascript" type="text/javascript" src="%s"></script>' . "\n", $_url);
                }
            }

            if (!empty($_args['return']) && $_args['return'] === true) {
                return $_return;
            } else {
                return true;
            }

        }

    }

    public static function addJs( $_pos=false, $_path = Array()){

        if (!isset($_pos)) {
            $_pos = count(self::$_js_array);
        }

        // Se for informados vários estilos ao mesmo tempo...
        if (is_array( $_path ) ) {
            
            // Percorrendo array informado
            foreach ($_path as $_conteudo) {
                self::$_js_array[$_pos] = $_conteudo;
            }

        } else {
            self::$_js_array[$_pos] = $_path;
        }
        return true;
    }

    public static function loadModel ($_name)
    {

        // File exists
        $_file = DIR_MODELS . $_name . 'Model.php';

        if (file_exists($_file) === true) {
            eval('$obj = new Model' . $_name . '();');
        }
        
    }


    public static function seoTitle($_str = false, $_clean=false) {

        if ($_str === false) {
            $_tmp = explode(__TITLE_SEP__, $_SESSION['SEO']['title']);
            $_tmp0 = '';
            for ($i=count($_tmp) - 1; $i>=0; $i--) {
                $_tmp0 .= trim($_tmp[$i]) . ' ';
                if ($i > 1) {
                    $_tmp0 .= __TITLE_SEP_INV__ . ' ';
                }
            }
            return self::removeLastChar(self::removeLastChar($_tmp0));
        } else {
            if ($_clean === false) {
                $_SESSION['SEO']['title'] .= " " . __TITLE_SEP__ . " {$_str}";
            } else {
                $_SESSION['SEO']['title'] = $_str;
            }
        }
    }

    public static function ogTitle($_str = false) {
        if ($_str === false) {
            return $_SESSION['og'][''];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og'][''];
        }
    }
    public static function ogType($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['type'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['type'];
        }
    }
    public static function ogImage($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['image'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['image'];
        }
    }
    public static function ogUrl($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['url'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['url'];
        }
    }

    public static function ogAudio($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['audio'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['audio'];
        }
    }
    public static function ogDescription($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['description'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['description'];
        }
    }
    public static function ogDeterminer($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['determiner'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['determiner'];
        }
    }
    public static function ogLocale($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['locale'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['locale'];
        }
    }
    public static function ogLocaleAlternate($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['alternate'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['localeAlternate'];
        }
    }
    public static function ogSiteName($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['siteName'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['siteName'];
        }
    }
    public static function ogVideo($_str = false) {
        if ($_str === false) {
            return $_SESSION['og']['siteVideo'];
        } else {
            $_SESSION['og']['description'] = $_str;
            return $_SESSION['og']['siteVideo'];
        }
    }

    public static function seoDescription($_str = false) {
        if ($_str === false) {
            return $_SESSION['SEO']['description'];
        } else {
            $_SESSION['SEO']['description'] = $_str;
            return $_SESSION['SEO']['description'];
        }
    }

    public static function seoTags($_str = false){
        if ($_str === false) {
            return $_SESSION['SEO']['tags'];
        } else {
            $_SESSION['SEO']['tags'] = $_str;
            return $_SESSION['SEO']['tags'];
        }
    }

    public static function passwd($_passwd=false) {
        return md5($_passwd . __PASS_SALT__);
    }

    public static function isLogged($_uid=false) {
        if ($_uid === false) {
            if (!empty($_SESSION['online']) && $_SESSION['online'] === true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function myId()
    {
        return new MongoId($_SESSION['uid']);
    }

    public static function loadDefaultCss()
    {

        $_css = self::loadCss(
            Array(
                'return' => true
            )
        );

        return $_css;
    }
    
    public static function loadDefaultJs()
    {

        $_js = self::loadJs(
            Array(
                'return' => true
            )
        );

        return $_js;
    }

    public static function redirect($_url = false) {
        ob_clean();
        header('Location: ' . $_url);
        return true;
    }

    /**
    * Retorna formatação de email sem espaços e tudo minísculo
    */
    public static function str_email($_email) {
        return strtolower( preg_replace("@[^A-Za-z0-9\@\.\-_]+@i", '', trim($_email)));
    }


      /**
       * Criação do link permanente para url_rewrite
       *
       * @access public
       * @name permlink
       * @param $str String
       * @version 0.2
       * @package Vision
       */
    public static function permlink($str)
    {
        $str = trim($str);
        // A
        $str = str_replace('Ã', 'A', $str);
        $str = str_replace('ã', 'a', $str);
        $str = str_replace('Á', 'A', $str);
        $str = str_replace('á', 'a', $str);
        $str = str_replace('À', 'A', $str);
        $str = str_replace('à', 'a', $str);
        $str = str_replace('Ä', 'A', $str);
        $str = str_replace('ä', 'a', $str);
        $str = str_replace('Ã', 'A', $str);
        $str = str_replace('Â', 'A', $str);
        $str = str_replace('â', 'a', $str);
        // E
        $str = str_replace('É', 'E', $str);
        $str = str_replace('é', 'e', $str);
        $str = str_replace('Ë', 'E', $str);
        $str = str_replace('ë', 'e', $str);
        $str = str_replace('ê', 'e', $str);
        $str = str_replace('Ê', 'E', $str);

        // I
        $str = str_replace('Í', 'I', $str);
        $str = str_replace('í', 'i', $str);
        $str = str_replace('é', 'i', $str);
        // O
        $str = str_replace('Õ', 'O', $str);
        $str = str_replace('õ', 'o', $str);
        $str = str_replace('Ó', 'O', $str);
        $str = str_replace('ó', 'o', $str);
        $str = str_replace('Ô', 'O', $str);
        $str = str_replace('ô', 'o', $str);
        $str = str_replace('Ò', 'O', $str);
        $str = str_replace('ò', 'o', $str);
        $str = str_replace('Ö', 'O', $str);
        $str = str_replace('ö', 'o', $str);
        // U
        $str = str_replace('Ú', 'U', $str);
        $str = str_replace('ú', 'u', $str);
        $str = str_replace('Ù', 'U', $str);
        $str = str_replace('ù', 'u', $str);
        $str = str_replace('Ü', 'U', $str);
        $str = str_replace('ü', 'u', $str);
        // Ç
        $str = str_replace('Ç', 'c', $str);
        $str = str_replace('ç', 'c', $str);
        $str = str_replace(' ', '-', $str);
        $str = str_replace('.', '', $str);
        $str = str_replace('--', '-', $str);

        //  Transformando em integer
        $tmp = strtolower(preg_replace("@[^A-Za-z0-9\@\.\-_]+@i", '', $str));
        
        // Retornando o valor final
        return $tmp;

    } // function permlink


    public static function dia_txt($_dia_semana, $_formato='0') {


        switch($_formato) {
        case 0:
          switch($_dia_semana) {
            case 0:  return 'Domingo'; break;
            case 1:  return 'Segunda'; break;
            case 2:  return 'Terça'; break;
            case 3:  return 'Quarta'; break;
            case 4:  return 'Quinta'; break;
            case 5:  return 'Sexta'; break;
            case 6:  return 'Sábado'; break;
          }
        break;
        case 1:
          switch($_dia_semana) {
            case 0:  return 'Dom'; break;
            case 1:  return 'Seg'; break;
            case 2:  return 'Ter'; break;
            case 3:  return 'Qua'; break;
            case 4:  return 'Qui'; break;
            case 5:  return 'Sex'; break;
            case 6:  return 'Sáb'; break;
          }
        break;
        case 2:
          switch($_dia_semana) {
            case 0:  return 'D'; break;
            case 1:  return 'S'; break;
            case 2:  return 'T'; break;
            case 3:  return 'Q'; break;
            case 4:  return 'Q'; break;
            case 5:  return 'S'; break;
            case 6:  return 'S'; break;
          }
          break;
        }
    }

    function mes_txt($_dia_mes, $_formato='0') {

      switch($_formato) {
        case 0:
          switch($_dia_mes) {
            case 1:   return 'Janeiro'; break;
            case 2:   return 'Fevereiro'; break;
            case 3:   return 'Março'; break;
            case 4:   return 'Abril'; break;
            case 5:   return 'Maio'; break;
            case 6:   return 'Junho'; break;
            case 7:   return 'Julho'; break;
            case 8:   return 'Agosto'; break;
            case 9:   return 'Setembro'; break;
            case 10:  return 'Outubro'; break;
            case 11:  return 'Novembro'; break;
            case 12:  return 'Dezembro'; break;
          }
        break;
        case 1:
          switch($_dia_mes) {
            case 1:   return 'Jan'; break;
            case 2:   return 'Fev'; break;
            case 3:   return 'Mar'; break;
            case 4:   return 'Abr'; break;
            case 5:   return 'Mai'; break;
            case 6:   return 'Jun'; break;
            case 7:   return 'Jul'; break;
            case 8:   return 'Ago'; break;
            case 9:   return 'Set'; break;
            case 10:  return 'Out'; break;
            case 11:  return 'Nov'; break;
            case 12:  return 'Dez'; break;
          }
        break;
        case 2:
          switch($_dia_mes) {
            case 1:   return 'J'; break;
            case 2:   return 'F'; break;
            case 3:   return 'M'; break;
            case 4:   return 'A'; break;
            case 5:   return 'M'; break;
            case 6:   return 'J'; break;
            case 7:   return 'J'; break;
            case 8:   return 'A'; break;
            case 9:   return 'S'; break;
            case 10:  return 'O'; break;
            case 11:  return 'N'; break;
            case 12:  return 'D'; break;
          }
        break;
      }
    }

    public static function facebooktime($_data, $_return =false, $_formato='timestamp') {

        if($_formato=='datetime') {
            $_data = (int) strtotime($_data);
        }

        //*** Se for menor que 60 segundos
        $_dif = time() - $_data;
        if($_dif<60) {
        if($_dif==0) {
          $_string = 'agora';
        } elseif($_dif==1) {
          $_string = '1 segundo';
        } else {
          $_string = sprintf('%s segundos', $_dif);
        }

        //*** 1 Minuto
        } elseif($_dif>59 && $_dif<120) {
        $_string = 'menos de 2 minutos';

        //*** Até 1 hora
        } elseif($_dif>119 && $_dif<3600) {
        $_string = sprintf('há %s minutos', floor($_dif/60));

        //*** 1 Hora
        } elseif($_dif>3599 && $_dif<7200) {
        $_string = 'há 1 hora';

        //*** Entre 2 horas e 23 horas e 59 minutos e 59 segundos
        } elseif($_dif>7199 && $_dif<86400) {
        $_string = sprintf('há %s horas', floor($_dif/60/60));

        //*** Ontem às 00:00
        } elseif($_dif>86399 && $_dif<172800) {
        $_string = sprintf('ontem às %s', date('H:s', $_data));

        //*** Entre 2 e 3 dias atrás
        } elseif($_dif>172799 && $_dif<259200) {
        $_string = sprintf('%s às %s', self::dia_txt(date('N', $_data)), date('H:s', $_data));

        //*** Há mais de 3 dias
        } else {

        //*** Se não for do mesmo ano exibe o ano também
        if(date('Y', $_data) < date('Y')) {
          $_string = sprintf('%s de %s em %s ás %s', date('d', $_data), self::mes_txt(date('n', $_data)), date('Y', $_data), date('H:s', $_data));
        } else {
          $_string = sprintf('%s de %s ás %s', date('d', $_data), self::mes_txt(date('n', $_data)), date('H:s', $_data));
        }

        }

        if($_return == true) { return $_string; } else { echo $_string; }
    }

    public static function error_log($_msg=false, $_file=false)
    {
        if ($_msg !== false) {

            $_msg = $_SERVER['REMOTE_ADDR'].
                "|".
                session_id().
                "|".
                time().
                "|".
                $_msg."\n";

            if ($_file !== false && file_exists($_file)) {
                return error_log($_msg, 3, $_file);
            } else {
                return error_log($_msg, 3, DEFAULT_LOG_FILE_ERROR);
            }
        } else {
            return false;
        }
    }

    public static function access_log($_msg=false, $_file=false)
    {
        if ($_msg !== false) {

            $_msg = $_SERVER['REMOTE_ADDR'].
                "|".
                session_id().
                "|".
                time().
                "|".
                $_msg.
                "|".
                $_SERVER['HTTP_REFERER'].
                "|".
                $_SERVER['HTTP_USER_AGENT']."\n";

            if ($_file !== false && file_exists($_file)) {
                $handle = fopen($_file, 'a');
                fwrite($handle, $_msg);
                fclose($handle);
                return true;
            } else {
                $handle = fopen(DEFAULT_LOG_FILE_ACCESS, 'a');
                fwrite($handle, $_msg);
                fclose($handle);
                return true;
            }
        } else {
            return false;
        }
    }

    public static function cdn_location($_link=false, $_return = false)
    {
        if ($_link['0'] === '/') {
            for ($i=1; $i<=strlen($_link); $i++) {
                $_tmp .= $_link[$i];
            }
            $_link = $_tmp;
        }

        $_url  = CDN_URL . str_replace('//', '/', $_link);

        if ($_return === false) {
            print $_url;
        } else {
            return $_url;
        }
    }

}

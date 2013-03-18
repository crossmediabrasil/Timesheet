<?php

class Layout extends System {
  
    public static function mainHeader()
    {
        $i=0;
        self::addJs($i++, self::static_location('js/jquery-1.9.1.min.js', true));
        self::addJs($i++, self::static_location('js/jquery-migrate-1.1.1.min.js', true));
        self::addJs($i++, self::static_location('js/bootstrap.min.js', true));
        
        self::addCss(100, self::static_location('css/style.css', true));

        self::render(
            'header', 
            Array(
                'seo_title' => self::seoTitle(),
                'seo_tags' => self::seoTags(),
                'seo_description' => self::seoDescription(),
                'url_favicon' => self::static_location(
                    'img/favicon.png', 
                    true
                ),
                'load_js' => self::loadDefaultJs(),
                'url_base'      => sprintf(
                    'http://%s%s', 
                    __DNS__, 
                    $_SERVER['REQUEST_URI']
                ),
                'js_dns'        => __DNS__,
                'js_url'        => self::location(false, true, false),
                'js_path'       => __PATH__,
                'js_static_url' => self::static_location(false, true),
                'load_css'      => self::loadDefaultCss(),
                'body_id'       => 'body'.rand(1, 4)
            )
        );

        // Determinando qual template chama (user online ou off)
        if (self::isLogged() === true) {
            $_u_tpl = 'online';

            Models::load('Users');
            $_user = ModelUsers::findOne(Array('_id'=>self::myId()));
        } else {
            $_u_tpl = 'offline';
            $_user = Array();
        }

        // Barra superior
        self::render(
            'top', 
            Array(
                'url' => self::location(false, true, false),
                'u_tpl' => $_u_tpl,
                'static_url' => self::static_location(false, true, false),
                'user' => $_user
            )
        );
    }


    public static function mainFooter()
    {
        self::render(
            'footer', 
            Array(
//               'load_css'     => self::loadDefaultCss(),
            		'year' => date('Y'),
                'load_js' => self::loadDefaultJs(),
            )
        );    

    }
}
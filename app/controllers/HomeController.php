<?php

class ControllerHome extends Controllers
{

    public function indexAction()
    {
        self::seoTitle('Home');

        self::addJs(130, self::static_location('js/jquery.isotope.min.js', true));
        

        Layout::mainHeader();

        // Carregando Model
        Models::load('Wallpapers');
        Controllers::load('Wallpapers');


        $_page = ((int) str_replace('.html', '', self::rewrite(3)) )+1;

        // Obtendo lista dos meus wallpapers
        $_wallpapersCursor = ModelWallpapers::Collection()->find(
            Array(
                'adult' => false
            )
        )
        ->sort(
            Array(
                'created_utc' => -1
            )
        )
        ->skip(($_page-1)*20)
        ->limit(20);

        $_width = 225;

        $_wallpapers = Array();
        foreach ($_wallpapersCursor as $_w) {

            $_r = ControllerWallpapers::inCache($_w['wid'], $_w['node'], $_width);

            if (is_array($_r)) {
                $_w['imageUrl'] = self::cdn_location($_r['cdnPath'], true);

            } else {
                $_w['imageUrl'] = self::location(
                    sprintf(
                        'wallpapers/showImage/%s/%s/?node=%s',
                        $_w['wid']->__toString(),
                        $_width,
                        $_w['node']
                    ),
                    true
                );
            }

            

            if (empty($_w['stats']['comments'])) {
                $_w['stats']['comments']=0;
                ModelWallpapers::save($_w);
            } else {
                $_w['comments']=$_w['stats']['comments'];
            }

            $_w['height_resample'] = number_format($_width / ($_w['width']/$_w['height']), 0);
            $_w['width_resample']  = $_width;
            $_wallpapers[]  = $_w;
        }

        self::render(
            'Home:index', 
            Array(
                'url'        => self::location(false, true, false),
                'wallpapers' => $_wallpapers,
                'width'      => $_width,
                'page'       => $_page,
                'next_page'  => $_page+1
            )
        );  

        Layout::mainFooter();

    }

    public static function myInfiniteScroll ()
    {
        // Carregando Model
        Models::load('Wallpapers');

        $_page = ((int) str_replace('.html', '', self::rewrite(3)) )+1;

        // Obtendo lista dos meus wallpapers
        $_wallpapersCursor = ModelWallpapers::Collection()->find(
            Array(
                'adult' => false
            )
        )
        ->sort(
            Array(
                'created_utc' => -1
            )
        )
        ->skip(($_page-2)*20)
        ->limit(20);

        $_width = 225;

        $_wallpapers = Array();
        foreach ($_wallpapersCursor as $_w) {

            $_w['imageUrl'] = self::location(
                sprintf(
                    'wallpapers/showImage/%s/%s/?node=%s',
                    $_w['wid']->__toString(),
                    $_width,
                    $_w['node']
                ),
                true
            );

            $_w['height_resample'] = number_format($_width / ($_w['width']/$_w['height']), 0);
            $_w['width_resample']  = $_width;

            if (empty($_w['stats']['comments'])) {
                $_w['stats']['comments']=0;
                ModelWallpapers::save($_w);
            } else {
                $_w['comments']=$_w['stats']['comments'];
            }

            $_wallpapers[]  = $_w;
        }

        self::render(
            'Home:index', 
            Array(
                'url'        => self::location(false, true, false),
                'wallpapers' => $_wallpapers,
                'width'      => $_width,
                'page'       => $_page,
                'next_page'  => $_page+1
            )
        );  
    }
}

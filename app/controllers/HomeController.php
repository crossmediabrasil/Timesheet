<?php

class ControllerHome extends Controllers
{

    public function indexAction()
    {
        self::seoTitle('Home');

        self::addJs(130, self::static_location('js/jquery.isotope.min.js', true));
        
        Layout::mainHeader();

        self::render(
            'Home:index', 
            Array(
                'url'        => self::location(false, true, false)
            )
        );  

        Layout::mainFooter();

    }
}

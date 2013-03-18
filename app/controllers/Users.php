<?php

class ControllerUsers extends Controllers
{

		public function indexAction()
		{
				
		}
		
		public static function signin()
		{
		    self::seoTitle('Usuários');
		    self::seoTitle('Entrar');
		    
		    Layout::mainHeader();
		    
		    self::render(
            'Users:signin',
            Array(
                'url'        => self::location(false, true, false)
            )
		    );
		    
		    Layout::mainFooter();
		}
}
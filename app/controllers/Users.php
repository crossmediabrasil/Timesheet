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
		
		public static function newUser()
		{
		    $_error = false;
		    if (!empty($_POST) && count($_POST) === 4) {
		        if (empty($_POST['name']) || empty($_POST['email']) ) {
		            $_error = true;
		        }
		        
		        if (
		            empty($_POST['password']) || 
		            $_POST['password'] == '' ||
                empty($_POST['re_password']) ||
                $_POST['re_password'] == ''
		        ) {
		            
		        }
		        
		        if ($_POST['password'] != $_POST['re_password']) {
		            $_error = true;
		        }
		        
		        if ($_error == true) {
		            self::redirect(self::location('users/signin', true));
		        } else {
		            
                // Preparing variables for insert
		            $_POST['password'] = self::passwd($_POST['password']);
		            $_POST['email'] = self::str_email($_POST['email']);
		            $_POST['name']  = trim($_POST['name']);
		            
		            $_data = $_POST;
		            $_data['created'] = new MongoDate(time());
		            $_data['updated'] = new MongoDate(time());
		            $_data['active']  = true;
		            $_data['roles'] = Array(
                    'is_admin' => false,
		                'is_customer' => true,
		                'is_manager'  => false,
		                'is_developer' => false,
		                'create_task' => true,
                    'create_project' => false,
		                'create_ticket'  => true
		            );
		            $_data['locale'] = 'UTC';
		            
		            // Verify if user exists
		            self::pr($_data);
		            
		        }
		    }
		    
		}
}
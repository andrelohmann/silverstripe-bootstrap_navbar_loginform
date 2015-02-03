<?php
/**
 * LoginForm for Bootstrap Navbar
 * 
 * put $BootstrapNavbarLoginForm inside your navbar
 * 
 * @package bootstrap_navbar_loginform
 */
class BootstrapNavbarLoginForm extends MemberLoginForm {
        
        protected static $AuthenticatorClass = "MemberAuthenticator";
        
        protected static $LoginFormClass = "MemberLoginForm";
        
        protected static $LoginButtonClass = "btn-info";
        
        protected static $LogoutButtonClass = "btn-danger";
    
        public static function set_AuthenticatorClass($class){
            self::$AuthenticatorClass = $class;
        }

        public static function get_AuthenticatorClass(){
            return self::$AuthenticatorClass;
        }
    
        public static function set_LoginFormClass($class){
            self::$LoginFormClass = $class;
        }

        public static function get_LoginFormClass(){
            return self::$LoginFormClass;
        }
    
        public static function set_LoginButtonClass($class){
            self::$LoginButtonClass = $class;
        }

        public static function get_LoginButtonClass(){
            return self::$LoginButtonClass;
        }
    
        public static function set_LogoutButtonClass($class){
            self::$LogoutButtonClass = $class;
        }

        public static function get_LogoutButtonClass(){
            return self::$LogoutButtonClass;
        }
	
	/**
	 * Constructor
	 *
	 * @param Controller $controller The parent controller, necessary to
	 *                               create the appropriate form action tag.
	 * @param string $name The method on the controller that will return this
	 *                     form object.
	 * @param FieldList|FormField $fields All of the fields in the form - a
	 *                                   {@link FieldList} of {@link FormField}
	 *                                   objects.
	 * @param FieldList|FormAction $actions All of the action buttons in the
	 *                                     form - a {@link FieldList} of
	 *                                     {@link FormAction} objects
	 * @param bool $checkCurrentUser If set to TRUE, it will be checked if a
	 *                               the user is currently logged in, and if
	 *                               so, only a logout button will be rendered
	 * @param string $authenticatorClassName Name of the authenticator class that this form uses.
	 */
	public function __construct($controller, $name, $fields = null, $actions = null, $checkCurrentUser = true) {
            
            // set the Authenticator class
            $this->authenticator_class = self::$AuthenticatorClass;

		if(isset($_REQUEST['BackURL'])) {
			$backURL = $_REQUEST['BackURL'];
		} else {
			$backURL = Session::get('BackURL');
		}

		if($checkCurrentUser && Member::currentUser() && Member::logged_in_session_exists()) {
			$fields = new FieldList(
				new HiddenField("AuthenticationMethod", null, $this->authenticator_class, $this)
			);
			$actions = new FieldList(
				$LogoutButton = new BootstrapLoadingFormAction("logout", _t('BootstrapNavbarLoginForm.BUTTONLOGOUT', 'BootstrapNavbarLoginForm.BUTTONLOGOUT'))
			);
                        $LogoutButton->addExtraClass(self::get_LogoutButtonClass());
		} else {
			if(!$fields) {
				$label=singleton('Member')->fieldLabel(Member::config()->unique_identifier_field);
				$fields = new FieldList(
					$AuthMethod = new HiddenField("AuthenticationMethod", null, $this->authenticator_class, $this),
					//Regardless of what the unique identifer field is (usually 'Email'), it will be held in the 'Email' value, below:
					$Email = new TextField("Email", $label, Session::get('SessionForms.MemberLoginForm.Email'), null, $this),
					$Password = new PasswordField("Password", _t('Member.PASSWORD', 'Password'))
				);
                                $Email->setPlaceholder($label);
                                $Password->setPlaceholder(_t('Member.PASSWORD', 'Password'));
			}
			if(!$actions) {
				$actions = new FieldList(
					$LoginButton = new BootstrapLoadingFormAction('dologin', _t('BootstrapNavbarLoginForm.BUTTONLOGIN', 'BootstrapNavbarLoginForm.BUTTONLOGIN'))
				);
                                $LoginButton->addExtraClass(self::get_LoginButtonClass());
			}
		}

		if(isset($backURL)) {
			$fields->push(new HiddenField('BackURL', 'BackURL', $backURL));
		}

		parent::__construct($controller, $name, $fields, $actions, $checkCurrentUser = false);
	}


	/**
	 * Login form handler method
	 *
	 * This method is called when the user clicks on "Log in"
	 *
	 * @param array $data Submitted data
	 */
	public function dologin($data) {
		if($this->performLogin($data)) {
			$this->logInUserAndRedirect($data);
		} else {
			if(array_key_exists('Email', $data)){
				Session::set('SessionForms.MemberLoginForm.Email', $data['Email']);
				Session::set('SessionForms.MemberLoginForm.Remember', isset($data['Remember']));
			}

			if(isset($_REQUEST['BackURL'])) $backURL = $_REQUEST['BackURL']; 
			else $backURL = null; 

			if($backURL) Session::set('BackURL', $backURL);
                        
                        // Show the right tab on failed login
                        $s = new Security();
    			$loginLink = Director::absoluteURL($s->Link("login"));
			if($backURL) $loginLink .= '?BackURL=' . urlencode($backURL);
			$this->controller->redirect($loginLink . '#' . $this->FormName() .'_tab');
		}
	}


	/**
	 * Log out form handler method
	 *
	 * This method is called when the user clicks on "logout" on the form
	 * created when the parameter <i>$checkCurrentUser</i> of the
	 * {@link __construct constructor} was set to TRUE and the user was
	 * currently logged in.
	 */
	public function logout() {
		$s = new Security();
		$s->logout();

		if(isset($_REQUEST['BackURL'])) $backURL = $_REQUEST['BackURL']; 
		else $backURL = null; 
                if($backURL) Session::set('BackURL', $backURL);
                // Show the right tab on failed login
    		$loginLink = Director::absoluteURL($s->Link("login"));
		if($backURL) $loginLink .= '?BackURL=' . urlencode($backURL);
		$this->controller->redirect($loginLink . '#' . $this->FormName() .'_tab');
	}

	/**
	 * Set a message to the session, for display next time this form is shown.
	 * 
	 * @param message the text of the message
	 * @param type Should be set to good, bad, or warning.
	 */
	public function sessionMessage($message, $type, $escapeHtml = true) {
		Session::set("FormInfo.".self::$LoginFormClass."_LoginForm.formError.message", $message);
		Session::set("FormInfo.".self::$LoginFormClass."_LoginForm.formError.type", $type);
	}
}
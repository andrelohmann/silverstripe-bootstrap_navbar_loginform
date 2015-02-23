<?php
/**
 * Modal LoginForm for Bootstrap Navbar
 * 
 * put $BootstrapNavbarModalLoginForm inside your navbar
 * and $BootstrapNavbarModalLoginForm.Modal outside of your navbar
 * 
 * @package bootstrap_navbar_loginform
 */
class BootstrapNavbarModalLoginForm extends BootstrapNavbarLoginForm {
    
    protected $ModalFormAction;
    protected $title;
    
    protected $size = 'modal-sm';
    
    protected $modal = null;
    
    public function hasErrors(){
        $errorInfo = Session::get("FormInfo.{$this->FormName()}");
        
        if(isset($errorInfo['errors']) && is_array($errorInfo['errors'])){
            return true;
        }
        
        if(isset($errorInfo['message']) && isset($errorInfo['type'])) {
            return true;
        }
        
        return false;
    }
    
    public function getTitle(){
        return $this->title;
    }
    
    public function getSize(){
        return $this->size;
    }
    
    public function getModal(){
        return $this->modal;
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
            $this->authenticator_class = parent::$AuthenticatorClass;

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
				$LogoutButton = BootstrapLoadingFormAction("logout")->setButtonContent(_t('BootstrapNavbarLoginForm.BUTTONLOGOUT', 'BootstrapNavbarLoginForm.BUTTONLOGOUT'))
			);
                        $LogoutButton->addExtraClass(parent::get_LogoutButtonClass());
		} else {
			if(!$fields) {
				$label=singleton('Member')->fieldLabel(Member::config()->unique_identifier_field);
				$fields = new FieldList(
					$AuthMethod = new HiddenField("AuthenticationMethod", null, $this->authenticator_class, $this),
					//Regardless of what the unique identifer field is (usually 'Email'), it will be held in the 'Email' value, below:
					$Email = new TextField("Email", $label, Session::get('SessionForms.MemberLoginForm.Email'), null, $this),
					$Password = new PasswordField("Password", _t('Member.PASSWORD', 'Password')),
                                        new LiteralField(
						'forgotPassword',
						'<p id="ForgotPassword"><a href="Security/lostpassword">'
						. _t('Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a></p>'
					)
				);
				if(Security::config()->autologin_enabled) {
					$fields->push(new CheckboxField(
						"Remember", 
						_t('Member.REMEMBERME', "Remember me next time?")
					));
				}
                                $Email->setPlaceholder($label);
                                $Password->setPlaceholder(_t('Member.PASSWORD', 'Password'));
			}
			if(!$actions) {
				$actions = new FieldList(
					$LoginButton = BootstrapLoadingFormAction::create('dologin')->setButtonContent(_t('BootstrapNavbarLoginForm.BUTTONLOGIN', 'BootstrapNavbarLoginForm.BUTTONLOGIN'))
				);
                                $LoginButton->addExtraClass(parent::get_LoginButtonClass());
			}
		}

		if(isset($backURL)) {
			$fields->push(new HiddenField('BackURL', 'BackURL', $backURL));
		}

		parent::__construct($controller, $name, $fields, $actions, $checkCurrentUser = false);
                
                if(!Member::currentUser()){
                    $this->title = _t('BootstrapNavbarModalLoginForm.MODALTITLE', 'BootstrapNavbarModalLoginForm.MODALTITLE');
        
                    $this->ModalFormAction = new BootstrapModalFormAction($Title = _t('BootstrapNavbarModalLoginForm.MODALBUTTON', 'BootstrapNavbarModalLoginForm.MODALBUTTON'));
                    $this->ModalFormAction->addExtraClass(parent::get_LoginButtonClass().' navbar-btn navbar-right');
                    $this->ModalFormAction->setTarget("Modal_".$this->FormName());

                    if($this->hasErrors()){
                        // set Modal open
                        $name = $this->FormName();
                        $js = <<<JS
$(function () {
    $('#Modal_{$name}').modal('show');
});
JS;
                        Requirements::customScript($js, 'BootstrapModalForm_hasErrorJs');
                    }
                    
                    $this->modal = $this->forTemplate();

                    $this->setTemplate('BootstrapNavbarModalLoginFormButton');
                }else{
                    $this->setTemplate('BootstrapNavbarLoginForm');
                }
	}
}
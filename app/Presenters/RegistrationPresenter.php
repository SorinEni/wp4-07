<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class RegistrationPresenter extends \Nette\Application\UI\Presenter
{
	private $database;

	public function __construct(\Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	protected function createComponentForm()
	{
		$form = new \Nette\Application\UI\Form();

		
		if ($this->user->isLoggedIn()) {
			echo "Zmena hesla :";
			$form->addPassword('pwd', 'New Password')->setRequired('Prosím vložte nové heslo.');

		} else {
		$form->addText('username', 'Username:')->setRequired('Please enter your username.');
		$form->addEmail('email', 'Email')->setRequired('Please enter email');
		$form->addPassword('pwd', 'Password')->setRequired('Please enter password');
		$form->addSubmit('register', 'Register');
		}
		
		$form->onSuccess[] = function() use ($form) {
			$passwords_hash = new Nette\Security\Passwords();	#hashovaci funkce
			$values = $form->getValues();
			if ($this->user->isLoggedIn()) {
					$this->database->table('users')->update([
						'password' => $passwords_hash->hash($values->pwd),
					]);
					$this->flashMessage('Úspěšne jsi změnil heslo.');
					$this->redirect('Homepage:default');
				} elseif ($this->database->table('users')->where('username', $values->username)->count()>0) {
					echo "Uživatel s tímto jménem už existuje";
				} else {
				
				 {
					$this->database->table('users')->insert([
						'email' => $values->email,
						'password' => $passwords_hash->hash($values->pwd), #hashovani hesla
						'username' => $values->username,
			]);}
				
			$this->flashMessage('You have been registered.');
			$this->redirect('Homepage:default'); 
		}
		};

		return $form;
	}
}
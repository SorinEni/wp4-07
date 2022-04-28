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
		$form->addText('username', 'Username:')->setRequired('Please enter your username.');
		$form->addEmail('email', 'Email')->setRequired('Please enter email');
		$form->addPassword('pwd', 'Password')->setRequired('Please enter password');
		$form->addSubmit('register', 'Register');
		
		
		$form->onSuccess[] = function() use ($form) {
			$passwords_hash = new Nette\Security\Passwords();	#hashovaci funkce
			$values = $form->getValues();
			if ($this->database->table('users')->where('username', $values->username)->count()>0) {
				$this->flashMessage("User already exist", "error");
			} else {
				$this->database->table('users')->insert([
					'email' => $values->email,
					'password' => $passwords_hash->hash($values->pwd), #hashovani hesla
					'username' => $values->username,
			]);
			$this->flashMessage('You have been registered.');
			$this->redirect('Homepage:default'); 
		}
		};

		return $form;
	}
}
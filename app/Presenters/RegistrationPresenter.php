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
		$form->addPassword('pwd2', 'Password (verify)')->setRequired('Please enter password for verification');
		$form->addSubmit('register', 'Register');
		
		
		$form->onSuccess[] = function() use ($form) {
			$passwords_hash = new Nette\Security\Passwords();
			$values = $form->getValues();
			$this->database->table('users')->insert([
				'email' => $values->email,
				'password' => $passwords_hash->hash($values->pwd),
				'username' => $values->username,
			]);
			$this->flashMessage('You have been registered.');
		};

		$form->onSuccess[] = function() {
			$this->redirect('Homepage:default');
		};

		return $form;
	}
}
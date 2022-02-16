<?php 

namespace App\Presenters;
use App\Model\PostFacade;

use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function renderShow(int $postId): void
{
	$post = $this->facade->getPostById
		->table('posts')
		->get($postId);
	if (!$post) {
		$this->error('Stránka nebyla nalezena');
	}

	$this->template->post = $post;
	$this->template->comments = $post->related('comments')->order('created_at');
}

protected function createComponentCommentForm(): Form
{
	$form = new Form; // means Nette\Application\UI\Form

	$form->addText('name', 'Jméno:')
		->setRequired();

	$form->addEmail('email', 'E-mail:');

	$form->addTextArea('content', 'Komentář:')
		->setRequired();

	$form->addSubmit('send', 'Publikovat komentář');

    $form->onSuccess[] = [$this, 'commentFormSucceeded'];

	return $form;
}

public function commentFormSucceeded(\stdClass $data): void
{
	$postId = $this->getParameter('postId');

	$this->facade->addComment($postId, $data);

	$this->flashMessage('Děkuji za komentář', 'success');
	$this->redirect('this');
}

}





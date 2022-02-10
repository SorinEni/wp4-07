<?php
namespace App\Presenters;
namespace App\Model\PostFacade;

use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

    protected function createComponentPostForm(): Form
{
	$form = new Form;
	$form->addText('title', 'Titulek:')
		->setRequired();
	$form->addTextArea('content', 'Obsah:')
		->setRequired();

	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = [$this, 'postFormSucceeded'];

	return $form;
}

public function renderEdit(int $postId): void
{
	$post = $this->PostFacade->getPostById
		->table('posts')
		->get($postId);

	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		->setDefaults($post->toArray());
}

public function postFormSucceeded(array $data): void
{
	$postId = $this->getPostById('postId');

	if ($postId) {
		$post = $this->editPost
			->table('posts')
			->get($postId);
		

	} else {
		$post = $this->insertPost;
	}

	$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
	$this->redirect('Post:show', $post->id);
}

}

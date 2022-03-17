<?php

namespace App\Presenters;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{

	private PostFacade $facade;

	public function __construct(PostFacade $facade)
	{
		$this->facade = $facade;
	}

	protected function createComponentPostForm(): Form
	{

		$form = new Form;
		$form->addText('title', 'Titulek:')
			->setRequired();
		$form->addTextArea('content', 'Obsah:')
			->setRequired();
		$form->addUpload('image', 'Soubor')
			//->setRequired()
			->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or Gif.');
		$form->addSubmit('send', 'Uložit a publikovat');
		$form->onSuccess[] = [$this, 'postFormSucceeded'];

		$statuses = [
            'OPEN' => 'OTEVŘENÝ',
            'CLOSED' => 'UZAVŘENÝ',
            'ARCHIVED' => 'ARCHIVOVANÝ'
        ];
        $form->addSelect('status', 'Stav:', $statuses)
            ->setDefaultValue('OPEN');
			
		return $form;
	}

	public function renderEdit(int $postId): void
	{
		$post = $this->facade
			->getPostById($postId);

		$this->getComponent('postForm')
			->setDefaults($post->toArray());
		$this->template->post = $post;
	}

	public function postFormSucceeded($form, $data): void
	{
		$postId = $this->getParameter('postId');

		
			if ($data->image->isOk()) {
				$data->image->move('upload/' . $data->image->getSanitizedName());
				$data['image'] = ('upload/' . $data->image->getSanitizedName());
			
		} else {
			unset($data->image);
			$this->flashMessage('Soubor nebyl přidán', 'failed');
		}


		if ($postId) {
			$post = $this->facade->editPost($postId, (array) $data);
		} else {
			$post = $this->facade->insertPost ((array) $data);
		}

		$this->flashMessage("Příspěvek byl úspěšně publikován.", 'success');
		$this->redirect('Post:show', $post->id);
	}

	public function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	public function handleDeleteImage(int $postId){
		$data['image'] = null;
		$this->facade->editPost($postId, $data);
		//$this->redirect('Edit:edit', $postId);
	}
}
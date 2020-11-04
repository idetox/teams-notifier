<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Bridge\Teams\Action\TeamsHttpPostAction;
use Symfony\Component\Notifier\Bridge\Teams\Action\TeamsOpenUriAction;
use Symfony\Component\Notifier\Bridge\Teams\TeamsOptions;
use Symfony\Component\Notifier\Bridge\Teams\Section\TeamsImageSectionElement;
use Symfony\Component\Notifier\Bridge\Teams\Section\TeamsSection;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class TeamsCommand extends Command
{
	protected static $defaultName = 'app:teams:notification';
	private $chatter;
	/**
	 * @var NotifierInterface
	 */
	private $notifier;

	public function __construct(ChatterInterface $chatter, NotifierInterface $notifier, string $name = null)
	{
		parent::__construct($name);
		$this->chatter = $chatter;
		$this->notifier = $notifier;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);

		$messageOptions = (new TeamsOptions())
			->title('Test title')
			->text('test text')
			->themeColor('00c6b6')
			->section((new TeamsSection())
				->title('Beautiful section')
				->image((new TeamsImageSectionElement())
					->title('mon image')
					->image('https://i.picsum.photos/id/100/200/300.jpg?hmac=MeTp97vw7VNDswRcCqUFkGNC8ILDvNfI4MRoHFyGcQ8')
				)
			)
			->section((new TeamsSection())
				->title('Beautiful section 2')
				->text('texte de secton groupée')
			)
			->action((new TeamsOpenUriAction())
				->name('GO TO Google')
				->target('default', 'https://www.google.com/')
			)
			->action((new TeamsHttpPostAction())
				->target('https://www.google.com/')
				->name('Google POST')
				->body('comment={{comment.value}}')
			);

		$message = (new ChatMessage('You got a Test.', $messageOptions));

		$this->chatter->send($message);

		$notification = new Notification('sending notification');
		$notification->content('content');

		$this->notifier->send($notification);

		$io->success('notification sent');

		return Command::SUCCESS;
	}
}
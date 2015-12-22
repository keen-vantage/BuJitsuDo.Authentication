<?php
namespace BuJitsuDo\Authentication\Service;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\View\StandaloneView;
use TYPO3\SwiftMailer\Mailer;
use TYPO3\SwiftMailer\Message;
use TYPO3\SwiftMailer\TransportFactory;
use TYPO3\SwiftMailer\TransportInterface;

/**
 * Class MailerService
 *
 * @package BuJitsuDo.Authentication
 * @Flow\Scope("singleton")
 */
class MailerService {

	/**
	 * @Flow\Inject(setting="transport", package="BuJitsuDo.Authentication")
	 * @var array
	 */
	protected $transportSettings;

	/**
	 * @param array $to
	 * @param string $subject
	 * @param string $templateFile
	 * @param array $variables
	 * @return boolean
	 * @throws \TYPO3\SwiftMailer\Exception
	 */
	public function sendEmail(array $to, $subject, $templateFile, array $variables = array()) {
		$view = $this->getStandaloneView();
		$view->setFormat('html');
		$view->setTemplatePathAndFilename(FLOW_PATH_ROOT . $templateFile);
		$view->assignMultiple($variables);
		$emailBody = $view->render();

		$message = $this->getMessage();
		$message->setFrom($this->transportSettings['from']['email'], $this->transportSettings['from']['name'])
			->setReplyTo($this->transportSettings['from']['email'], $this->transportSettings['from']['name'])
			->setTo($to['email'], $to['name'])
			->setSubject($subject)
			->setBody($emailBody, 'text/html');

		$transportFactory = $this->getTransportFactory();
		$transport = $transportFactory->create($this->transportSettings['type'], $this->transportSettings['options']);
		$mailer = $this->getMailer($transport);
		$mailer->send($message);
		return $message->isSent();
	}

	/**
	 * @return StandaloneView
	 */
	protected function getStandaloneView() {
		return new StandaloneView();
	}

	/**
	 * @return Message
	 */
	protected function getMessage() {
		return new Message();
	}

	/**
	 * @return TransportFactory
	 */
	protected function getTransportFactory() {
		return new TransportFactory();
	}

	/**
	 * @param mixed $transport
	 * @return Mailer
	 */
	protected function getMailer($transport) {
		return new Mailer($transport);
	}

}
<?php
namespace BuJitsuDo\Authentication\Controller;

use BuJitsuDo\Authentication\Service\ProfileService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

class LoginController extends ActionController {


	/**
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 * @Flow\Inject
	 */
	protected $authenticationManager;

	/**
	 * @Flow\Inject
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * @Flow\SkipCsrfProtection
	 * @return void|string
	 */
	public function authenticateAction() {
		try {
			$this->authenticationManager->authenticate();
			if ($this->authenticationManager->isAuthenticated()) {
				$profile = $this->profileService->getCurrentPartyProfile();
				$this->redirect('show', 'Frontend\Node', 'TYPO3.Neos', ['node' => $profile->getPath()]);
			} else {
				$this->addFlashMessage('Gebruikersnaam of wachtwoord is niet correct');
				$this->forwardToReferringRequest();
			}
		} catch (\Exception $e) {
			$this->addFlashMessage('Gebruikersnaam of wachtwoord is niet correct');
			$this->forwardToReferringRequest();
		}
	}

	/**
	 * @return void
	 */
	public function logoutAction() {
		try {
			$this->authenticationManager->logout();
			$this->redirectToUri('/');
			return;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}
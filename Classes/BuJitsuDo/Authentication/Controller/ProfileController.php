<?php
namespace BuJitsuDo\Authentication\Controller;

use BuJitsuDo\Authentication\Controller\Exception\UpdateNodeException;
use BuJitsuDo\Authentication\Domain\Dto\ImageDto;
use BuJitsuDo\Authentication\Domain\Dto\PersonDto;
use BuJitsuDo\Authentication\Domain\Model\Person;
use BuJitsuDo\Authentication\Service\MailerService;
use BuJitsuDo\Authentication\Service\ProfileService;
use Nieuwenhuizen\CR\Domain\Repository\NodeWriteRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\I18n\Locale;
use TYPO3\Flow\I18n\Translator;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Flow\Security\Cryptography\HashService;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class ProfileController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * @Flow\Inject
	 * @var NodeWriteRepository
	 */
	protected $nodeWriteRepository;

	/**
	 * @Flow\Inject
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject
	 * @var HashService
	 */
	protected $hashService;

	/**
	 * @Flow\Inject
	 * @var AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var MailerService
	 */
	protected $mailerService;

	/**
	 * @param PersonDto $person
	 * @param string $dateOfBirth
	 * @throws UpdateNodeException
	 * @throws \Exception
	 *
	 * @return string
	 */
	public function updateAction(PersonDto $person, $dateOfBirth = '') {
		$profile = $this->profileService->getCurrentPartyProfile();
		$referenceNode = $person->getReferenceNode();

		if ($profile->getIdentifier() !== $referenceNode->getIdentifier()) {
			throw new UpdateNodeException('You can only update your own node', 125172979);
		}

		foreach($person->getObjectVars() as $propertyName => $propertyValue) {
			if ($propertyName !== NULL && $propertyName !== 'referenceNode' && $propertyName !== 'image') {
				$properties[$propertyName] = $propertyValue;
			}
		}
		if ($dateOfBirth !== '') {
			$timestamp = strtotime($dateOfBirth);
			$dateOfBirth = new \DateTime();
			$dateOfBirth->setTimestamp($timestamp);
			$properties['dateOfBirth'] = $dateOfBirth;
		}

		if (isset($properties)) {
			$this->nodeWriteRepository->updateNode($referenceNode, $properties);
		}
		$locale = new Locale('nl');
		$this->response->setHeader('Notification', $this->translator->translateById('profile.update.success', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
		return '';
	}

	/**
	 * @param ImageDto $imageDto
	 * @param string $profileNodeIdentifier
	 * @throws UpdateNodeException
	 * @return void
	 */
	public function updateImageAction(ImageDto $imageDto, $profileNodeIdentifier) {
		/** @var Person $profile */
		$profile = $this->profileService->getCurrentPartyProfile();
		if ($profile->getIdentifier() !== $profileNodeIdentifier) {
			throw new UpdateNodeException('You can only update your own node', 1872398762);
		}
		if ($imageDto->getImage() instanceof Image) {
			$this->profileService->setImageToNode(
				$profile, $imageDto->getImage(),
				$profile->getDisplayName(),
				[$profile->getDisplayName(), 'Profile images'],
				'image',
				TRUE
			);
			$this->persistenceManager->persistAll();
			$this->redirect('show', 'Frontend\Node', 'TYPO3.Neos', ['node' => $profile]);
		}
	}

	/**
	 * Initialize updatePassword
	 * To display correct error message for the xHttpRequest
	 *
	 * @return void
	 */
	public function initializeUpdatePasswordAction() {
		if ($this->getFlattenedValidationErrorMessage()) {
			$locale = new Locale('nl');
			$this->response->setHeader('Notification', $this->translator->translateById('profile.update.password.response.failure', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
			$this->response->setHeader('NotificationType', 'alert');
		}
	}

	/**
	 * @param array $password
	 * @throws UpdateNodeException
	 * @Flow\Validate(argumentName="password", type="\TYPO3\Neos\Validation\Validator\PasswordValidator", options={ "allowEmpty"=0, "minimum"=6, "maximum"=255 })
	 * @return string
	 */
	public function updatePasswordAction(array $password) {
		$account = $this->securityContext->getAccount();
		$locale = new Locale('nl');
		if (!$account instanceof Account) {
			$this->response->setHeader('Notification', $this->translator->translateById('profile.update.password.response.failure', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
			throw new UpdateNodeException('No account (session) present', 127381164);
		}

		$newPassword = $this->hashService->hashPassword($password[0], 'default');
		$account->setCredentialsSource($newPassword);
		$this->accountRepository->update($account);
		$this->response->setHeader('Notification', $this->translator->translateById('profile.update.password.response.success', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
		$this->response->setHeader('NotificationType', 'success');
		return '';
	}

	/**
	 * @param string $emailAddress
	 * @param string $requirement
	 *
	 * @throws \Exception
	 *
	 * @return string
	 */
	public function resetPasswordAction($emailAddress, $requirement = '') {
		if ($requirement !== '') {
			throw new \Exception('Bot detection', 12393182738);
		}
		$locale = new Locale('nl');
		$account = $this->accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($emailAddress, 'DefaultProvider');
		if ($account instanceof Account) {
			try {
				/** @var Person $profile */
				$profile = $this->profileService->getProfileNodeOfAccount($account);
				$password = $this->randomPassword();
				$hashedPassword = $this->hashService->hashPassword($password, 'default');
				$this->mailerService->sendEmail(
					array(
						'email' => $emailAddress,
						'name' => $profile->getDisplayName()
					),
					'Nieuw wachtwoord',
					'Packages/Application/BuJitsuDo.Authentication/Resources/Private/Templates/Email/PasswordReset.html',
					array(
						'password' => $password,
						'profile' => $profile
					)
				);
				$account->setCredentialsSource($hashedPassword);
				$this->accountRepository->update($account);
				$this->persistenceManager->persistAll();
			} catch (\Exception $exception) {
				return  $exception->getMessage();
			}
		} else {
			$this->response->setHeader('Notification', $this->translator->translateById('profile.reset.password.response.failure', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
			$this->response->setHeader('NotificationType', 'alert');
			return '';
		}
		$this->response->setHeader('Notification', $this->translator->translateById('profile.reset.password.response.success', [], NULL, $locale, 'Main', 'BuJitsuDo.Authentication'));
		$this->response->setHeader('NotificationType', 'success');
		return '';
	}

	/**
	 * @return string
	 */
	protected function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}

}
<?php
namespace BuJitsuDo\Authentication\Controller;

use BuJitsuDo\Authentication\Domain\Dto\PersonDto;
use BuJitsuDo\Authentication\Service\ProfileService;
use Nieuwenhuizen\CR\Domain\Repository\NodeWriteRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Persistence\Doctrine\PersistenceManager;
use TYPO3\Flow\Property\TypeConverter\DateTimeConverter;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\AccountFactory;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\Neos\Domain\Model\User;
use TYPO3\Neos\Domain\Service\NodeSearchService;
use TYPO3\Party\Domain\Model\AbstractParty;
use TYPO3\Party\Domain\Model\PersonName;
use TYPO3\Party\Domain\Repository\PartyRepository;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface;
use TYPO3\TYPO3CR\Utility;

/**
 * Class RegistrationController
 *
 * @package BuJitsuDo.Authentication
 */
class RegistrationController extends ActionController {

	/**
	 * @Flow\Inject
	 * @var AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @Flow\Inject
	 * @var AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var NodeSearchService
	 */
	protected $nodeSearchService;

	/**
	 * @Flow\Inject
	 * @var ContextFactoryInterface
	 */
	protected $contextFactory;

	/**
	 * @Flow\Inject
	 * @var NodeWriteRepository
	 */
	protected $nodeWriteRepository;

	/**
	 * @Flow\Inject
	 * @var PartyRepository
	 */
	protected $partyRepository;

	/**
	 * @Flow\Inject
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * @var array
	 * @Flow\Inject(setting="captcha", package="BuJitsuDo.Authentication")
	 */
	protected $captchaSettings;

	/**
	 * @var array
	 */
	protected $defaultRoles = [
		'BuJitsuDo.Authentication:User'
	];

	/**
	 * @return void
	 */
	protected function initializeRegisterAction() {
		$propertyMappingConfiguration = $this->arguments->getArgument('dateOfBirth')->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->setTypeConverterOption(
			'TYPO3\Flow\Property\TypeConverter\DateTimeConverter',
			DateTimeConverter::CONFIGURATION_DATE_FORMAT,
			'd-m-Y'
		);
	}

	/**
	 * @param PersonDto $person
	 * @param NodeInterface $userStorageNode
	 * @param string $dateOfBirth
	 * @param array $password
	 * @param string $recaptcha_challenge_field
	 * @return void
	 *
	 * @throws \Exception
	 * @throws \TYPO3\Flow\Mvc\Exception\ForwardException
	 * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
	 * @throws \TYPO3\Flow\Security\Exception
	 *
	 * @Flow\Validate(argumentName="password", type="\TYPO3\Neos\Validation\Validator\PasswordValidator", options={ "allowEmpty"=0, "minimum"=6, "maximum"=255 })
	 * @Flow\Validate(argumentName="recaptcha_challenge_field", type="\BuJitsuDo\Authentication\Validation\Validator\CaptchaValidator")
	 * @Flow\Validate(argumentName="person.emailAddress", type="\BuJitsuDo\Authentication\Validation\Validator\AccountDoesNotExistsValidator")
	 */
	public function registerAction(PersonDto $person, NodeInterface $userStorageNode, $dateOfBirth = NULL, array $password, $recaptcha_challenge_field = '') {
		$account = $this->accountFactory->createAccountWithPassword($person->getEmailAddress(), array_shift($password), $this->defaultRoles);
		$party = $this->objectManager->get('\TYPO3\Neos\Domain\Model\User');
		if ($dateOfBirth !== NULL) {
			$date = new \DateTime();
			$date->setTimestamp(strtotime($dateOfBirth));
			$person->setDateOfBirth($date);
		}
		if (!$party instanceof AbstractParty) {
			throw new \Exception('The configured className did not result in an object that inherits from AbstractParty.', 1397043150);
		}
		/** @var $party User */
		$party->setName(new PersonName('', $person->getFirstName(), '', $person->getLastName()));
		$this->partyRepository->add($party);
		$account->setParty($party);
		$this->accountRepository->add($account);
		$this->persistenceManager->persistAll();
		$profile = $this->createProfileNode($account, $userStorageNode, $person);
		if ($profile instanceof NodeInterface) {
			$this->forward('show', 'Frontend\Node', 'TYPO3.Neos', ['node' => $profile]);
		} else {
			throw new \Exception('No profile node returned', 1239187);
		}
	}

	/**
	 * @param Account $account
	 * @param NodeInterface $userStorageNode
	 * @param PersonDto $person
	 * @return NodeInterface|string
	 */
	protected function createProfileNode(Account $account, NodeInterface $userStorageNode, PersonDto $person) {
		try {
			$profileNode = $this->findProfileNode($person->getEmailAddress());

			if ($profileNode === NULL) {
				$properties = [
					'title' => $person->getFirstName() . ' ' . $person->getLastName(),
					'firstName' => $person->getFirstName(),
					'lastName' => $person->getLastName(),
					'address' => $person->getAddress(),
					'zipCode' => $person->getZipCode(),
					'city' => $person->getCity(),
					'emailAddress' => $person->getEmailAddress(),
					'phone' => $person->getPhone(),
					'dateOfBirth' => $person->getDateOfBirth(),
					'jiuJitsu' => $person->getJiuJitsu(),
					'buJitsuDo' => $person->getBuJitsuDo(),
					'jiuJitsuDegree' => $person->getJiuJitsuDegree(),
					'buJitsuDoDegree' => $person->getBuJitsuDoDegree(),
					'gender' => $person->getGender()
				];
				if ($person->getFirstName() && $person->getLastName()) {
					$nodeName = $person->getFirstName() . ' ' . $person->getLastName();
				}
				$idealNodeName = Utility::renderValidNodeName(
					isset($nodeName) ? $nodeName : uniqid('node')
				);
				$idealNodeName = htmlspecialchars($idealNodeName, ENT_NOQUOTES, 'UTF-8');
				$profileNode = $this->nodeWriteRepository->createChildNode($userStorageNode, $idealNodeName, 'BuJitsuDo.Authentication:Person', $properties);
				if ($person->getImage() instanceof Image) {
					$profileNode = $this->profileService->setImageToNode($profileNode, $person->getImage(), $person->getFirstName(), 'Profile images');
				}
			}
			$account->getParty()->getPreferences()->set('profileNodeIdentifier', $profileNode->getIdentifier());
			$this->partyRepository->update($account->getParty());
			$this->persistenceManager->persistAll();
			$this->emitPersonCreated($profileNode);
			return $profileNode;
		} catch (\Exception $exception) {
			$this->systemLogger->log('Profile node could not be created because: ' . $exception->getMessage(), LOG_CRIT);
			return $exception->getMessage();
		}
	}

	/**
	 * @param string $property
	 * @return NodeInterface|null
	 */
	protected function findProfileNode($property) {
		$contentContext = $this->contextFactory->create(array());
		$result = $this->nodeSearchService->findByProperties(
			$property,
			array('BuJitsuDo.Authentication:Person'),
			$contentContext
		);
		if ($result === array()) {
			return NULL;
		}
		return array_shift($result);
	}

	/**
	 * @Flow\Signal
	 * @param NodeInterface $person
	 * @return void
	 */
	protected function emitPersonCreated(NodeInterface $person) {
	}

}
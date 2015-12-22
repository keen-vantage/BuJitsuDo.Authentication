<?php
namespace BuJitsuDo\Authentication\Eel;

use BuJitsuDo\Authentication\Service\ProfileService;
use BuJitsuDo\Authentication\TypoScriptObjects\RegistrationImplementation;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class ProfileHelper implements ProtectedContextAwareInterface {

	/**
	 * @Flow\Inject
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @return NodeInterface
	 */
	public function getCurrentUserNode() {
		try {
			return $this->profileService->getCurrentPartyProfile();
		} catch (\Exception $exception) {
			$this->systemLogger->log('Profile node could not be fetched: ' . $exception->getMessage(), LOG_CRIT);
		}
	}

	/**
	 * @param NodeInterface $profileNode
	 * @return string
	 */
	public function getDisplayName(NodeInterface $profileNode) {
		$return = '';
		if ($profileNode->getProperty('firstName')) {
			$return .= ucfirst($profileNode->getProperty('firstName'));
		}
		if ($profileNode->getProperty('lastName')) {
			$lastNameParts = explode(' ', $profileNode->getProperty('lastName'));
			$return .= ' ';
			$lastName = ucfirst(end($lastNameParts));
			array_pop($lastNameParts);
			$lastNameParts[] = $lastName;
			foreach ($lastNameParts as $namePart) {
				$return .= $namePart . ' ';
			}
		}
		return $return;
	}

	/**
	 * @param string $style
	 * @param $degree
	 * @return string
	 */
	public function getDegreeLabel($style = 'bujitsudo', $degree) {
		if ($style === 'bujitsudo') {
			return $this->profileService->getBuJitsuDoOptions()[$degree];
		}
		return $this->profileService->getJiuJitsuOptions()[$degree];
	}

	/**
	 * @param string $string
	 * @param string $delimiter
	 * @return array
	 */
	public function getExplode($string, $delimiter = ',') {
		return explode($delimiter, $string);
	}

	/**
	 * @param string $methodName
	 * @return boolean
	 */
	public function allowsCallOfMethod($methodName) {
		return TRUE;
	}
}
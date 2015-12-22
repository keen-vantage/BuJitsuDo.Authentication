<?php
namespace BuJitsuDo\Authentication\ViewHelpers;

use BuJitsuDo\Authentication\Service\ProfileService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Context;
use TYPO3\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class IsEditableByMeViewHelper extends AbstractConditionViewHelper {

	/**
	 * @Flow\Inject
	 * @var ProfileService
	 */
	protected $profileService;

	/**
	 * @Flow\Inject
	 * @var Context
	 */
	protected $securityContext;

	/**
	 * Allow access to content if it is my content or when i have the allowed role
	 *
	 * @param string $contentUserIdentifier
	 * @param array $allowedRoles
	 * @param array $disallowedRoles
	 * @return string
	 */
	public function render($contentUserIdentifier, $allowedRoles = [], $disallowedRoles = []) {
		try {
			$profile = $this->profileService->getCurrentPartyProfile();
		} catch (\Exception $exception) {
			return;
		}
		$myRoles = $this->securityContext->getRoles();
		$accessBasedOnRole = FALSE;

		foreach (array_unique($allowedRoles) as $role) {
			if (in_array($role, $myRoles)) {
				$accessBasedOnRole = TRUE;
			}
		}
		foreach(array_unique($disallowedRoles) as $role) {
			if (in_array($role, $myRoles)) {
				$accessBasedOnRole = FALSE;
			}
		}

		if ($profile->getIdentifier() === $contentUserIdentifier || $accessBasedOnRole === TRUE) {
			return $this->renderThenChild();
		}
		return $this->renderElseChild();
	}

}
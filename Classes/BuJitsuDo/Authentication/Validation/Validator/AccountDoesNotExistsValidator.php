<?php
namespace BuJitsuDo\Authentication\Validation\Validator;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Flow\Validation\Error;
use TYPO3\Flow\Validation\Exception\InvalidSubjectException;
use TYPO3\Flow\Validation\Validator\AbstractValidator;

/**
 * Validator for accounts
 */
class AccountDoesNotExistsValidator extends AbstractValidator {

	/**
	 * @Flow\Inject
	 * @var AccountRepository
	 */
	protected $accountRepository;

	/**
	 * Returns TRUE, if the specified user ($value) does not exist yet.
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @throws InvalidSubjectException
	 */
	protected function isValid($value) {
		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($value, 'DefaultProvider');
		if ($account instanceof Account) {
			$this->addError('There is already a user with the email address.', 1325156008);
		}
	}

}

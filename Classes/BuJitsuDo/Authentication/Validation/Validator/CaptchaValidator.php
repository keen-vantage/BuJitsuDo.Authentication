<?php
namespace BuJitsuDo\Authentication\Validation\Validator;

use ReCaptcha\Captcha;
use TYPO3\Flow\Validation\Validator\AbstractValidator;
use TYPO3\Flow\Annotations as Flow;

class CaptchaValidator extends AbstractValidator {

	/**
	 * @var array
	 * @Flow\Inject(setting="captcha", package="BuJitsuDo.Authentication")
	 */
	protected $captchaSettings;

	/**
	 * @param string $value
	 * @return void
	 */
	public function isValid($value) {
	    $captcha = new Captcha();
		$captcha->setPrivateKey($this->captchaSettings['secret']);
		if ($captcha->isValid() === FALSE) {
			$this->addError('Captcha is niet correct', 1231982309);
		}
	}

}
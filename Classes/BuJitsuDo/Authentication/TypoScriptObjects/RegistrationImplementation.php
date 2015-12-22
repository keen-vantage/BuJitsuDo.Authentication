<?php
namespace BuJitsuDo\Authentication\TypoScriptObjects;

use BuJitsuDo\Authentication\Service\ProfileService;
use ReCaptcha\Captcha;
use TYPO3\TypoScript\TypoScriptObjects\Helpers\FluidView;
use TYPO3\TypoScript\TypoScriptObjects\TemplateImplementation;
use TYPO3\Flow\Annotations as Flow;

class RegistrationImplementation extends TemplateImplementation {

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
	 * @param FluidView $view`
	 */
	protected function initializeView(FluidView $view) {
		$captcha = new Captcha();
		$captcha->setPublicKey($this->captchaSettings['publicKey']);
		$view->assignMultiple([
			'captcha' => $captcha->displayHTML('clean'),
			'jiuJitsuOptions' => $this->profileService->getJiuJitsuOptions(),
			'buJitsuDoOptions' => $this->profileService->getBuJitsuDoOptions()
		]);
	}

}
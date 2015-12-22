<?php
namespace BuJitsuDo\Authentication\Domain\Dto;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Media\Domain\Model\Image;

trait ImageTrait {

	/**
	 * @ORM\OneToOne(cascade={"persist"})
	 * @var \TYPO3\Media\Domain\Model\Image
	 */
	protected $image;

	/**
	 * Returns the Image
	 *
	 * @return \TYPO3\Media\Domain\Model\Image
	 */
	public function getImage () {
		return $this->image;
	}

	/**
	 * Sets the Image
	 *
	 * @param \TYPO3\Media\Domain\Model\Image $image
	 * @return void
	 */
	public function setImage (Image $image) {
		$this->image = $image;
	}

}
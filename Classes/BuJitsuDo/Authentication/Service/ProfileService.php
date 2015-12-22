<?php
namespace BuJitsuDo\Authentication\Service;

use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Security\Account;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\Media\Domain\Model\Tag;
use TYPO3\Media\Domain\Repository\ImageRepository;
use TYPO3\Media\Domain\Repository\TagRepository;
use TYPO3\Neos\Domain\Service\NodeSearchService;
use TYPO3\Party\Domain\Service\PartyService;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Flow\Security\Context as SecurityContext;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface;

class ProfileService {

	/**
	 * @Flow\Inject
	 * @var SecurityContext
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject
	 * @var ContextFactoryInterface
	 */
	protected $contextFactory;

	/**
	 * @Flow\Inject
	 * @var NodeSearchService
	 */
	protected $nodeSearchService;

	/**
	 * @Flow\Inject
	 * @var ImageRepository
	 */
	protected $imageRepository;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var TagRepository
	 */
	protected $tagRepository;

	/**
	 * @Flow\Inject
	 * @var PartyService
	 */
	protected $partyService;

	/**
	 * @var array
	 */
	protected $jiuJitsuOptions = [
		'N.v.t',
		'none' => '6e kyu - rokku-kyu - (Wit)',
		'kyu5' => '5e kyu - go-kyu - (geel)',
		'kyu4' => '4e kyu - yon-kyu - (oranje)',
		'kyu3' => '3e kyu - san-kyu - (groen)',
		'kyu2' => '2e kyu - ni-kyu - (blauw)',
		'kyu1' => '1e kyu - ichi-kyu - (bruin)',
		'dan1' => '1e dan - sho-dan - (zwart)',
		'dan2' => '2e dan - ni-dan - (zwart)',
		'dan3' => '3e dan - san-dan - (zwart)',
		'dan4' => '4e dan - san-dan - (zwart)'
	];

	/**
	 * @var array
	 */
	protected $buJitsuDoOptions = [
		'N.v.t',
		'none' => '6e kyu - rokku-kyu - (Wit)',
		'kyu5' => '5e kyu - go-kyu - (wit/geel)',
		'kyu4' => '4e kyu - yon-kyu - (geel/oranje)',
		'kyu3' => '3e kyu - san-kyu - (oranje/groen)',
		'kyu2' => '2e kyu - ni-kyu - (groen/blauw)',
		'kyu1' => '1e kyu - ichi-kyu - (blauw/bruin)',
		'dan1' => '1e dan - sho-dan - (rood/zwart)'
	];

	/**
	 * Retrieve current profileNode
	 *
	 * @throws \Exception
	 * @return NodeInterface
	 */
	public function getCurrentPartyProfile() {
		/** @var $user \TYPO3\Neos\Domain\Model\User */
		$user = $this->securityContext->getPartyByType('TYPO3\Neos\Domain\Model\User');
		if ($user instanceof \TYPO3\Neos\Domain\Model\User) {
			$profileNodeIdentifier = $user->getPreferences()->get('profileNodeIdentifier');
			$contentContext = $this->contextFactory->create(array());
			if ($profileNodeIdentifier !== NULL) {
				$profileNode = $contentContext->getNodeByIdentifier($profileNodeIdentifier);
				if (!isset($profileNode) || !$profileNode instanceof NodeInterface) {
					throw new \Exception('Profile not found');
				}
				return $profileNode;
			}
			throw new \Exception('Profile not found');
		} else {
			throw new \Exception('User not found');
		}
	}

	/**
	 * Returns the JiuJitsuOptions
	 *
	 * @return array
	 */
	public function getJiuJitsuOptions () {
		return $this->jiuJitsuOptions;
	}

	/**
	 * Returns the BuJitsuDoOptions
	 *
	 * @return array
	 */
	public function getBuJitsuDoOptions () {
		return $this->buJitsuDoOptions;
	}

	/**
	 * @param NodeInterface $node
	 * @param Image $newImage
	 * @param string $title
	 * @param string|array $tagLabel
	 * @param string $propertyName
	 * @param boolean $removePreviousProfileImage
	 * @return NodeInterface
	 */
	public function setImageToNode(NodeInterface $node, Image $newImage, $title, $tagLabel = NULL, $propertyName = 'image', $removePreviousProfileImage = FALSE) {
		$newImage->setTitle($title);
		if ($tagLabel !== NULL) {
			if (is_array($tagLabel) && !is_string($tagLabel)){
				if ($removePreviousProfileImage === TRUE) {
					$this->removePreviousProfilePictureBasedOnTags($title, $tagLabel);
				}
				foreach ($tagLabel as $key => $label) {
					$tag = $this->tagRepository->findOneByLabel($label);
					if (!$tag instanceof Tag) {
						$tag = new Tag($label);
						$this->tagRepository->add($tag);
					}
					$newImage->addTag($tag);
				}
			} elseif (is_string($tagLabel) && !is_array($tagLabel)) {
				$tag = $this->tagRepository->findOneByLabel($tagLabel);
				if (!$tag instanceof Tag) {
					$tag = new Tag($tagLabel);
					$this->tagRepository->add($tag);
				}
				$newImage->addTag($tag);
			}
		}

		/** @var Image $image */
		$image = $this->imageRepository->findByIdentifier($newImage->getIdentifier());
		if ($image !== NULL) {
			try {
				$this->imageRepository->update($image);
				$node->setProperty($propertyName, $image);
			} catch (\Exception $exception) {
				// Image repository might give back an image while not stored for some reason. If so, catch that error and store it anyway
				$image->setTitle($title);
				$this->imageRepository->add($image);
				$node->setProperty($propertyName, $image);
				$this->systemLogger->log('Image with identifier ' . $image->getIdentifier() . ' stored while preceding an error that is not stored yet fetched using ImageRepository', LOG_CRIT);
			}
		} else {
			$this->imageRepository->add($newImage);
			$node->setProperty($propertyName, $newImage);
		}
		return $node;
	}

	/**
	 * @param Account $account
	 * @throws \Exception
	 * @return NodeInterface
	 */
	public function getProfileNodeOfAccount(Account $account) {
		$party = $this->partyService->getAssignedPartyOfAccount($account);
		$profileNodeIdentifier = $party->getPreferences()->get('profileNodeIdentifier');
		if ($profileNodeIdentifier !== NULL) {
			$contentContext = $this->contextFactory->create(array());
			$profileNode = $contentContext->getNodeByIdentifier($profileNodeIdentifier);
			if (!isset($profileNode) || !$profileNode instanceof NodeInterface) {
				throw new \Exception('Profile not found');
			}
			return $profileNode;
		}
	}

	/**
	 * @param string $title
	 * @param array $tagLabels
	 * @return void
	 */
	protected function removePreviousProfilePictureBasedOnTags($title, array $tagLabels) {
		$images = $this->imageRepository->findBySearchTermOrTags($title, $tagLabels);
		foreach ($images as $image) {
			$this->imageRepository->remove($image);
		}
	}

}
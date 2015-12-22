<?php
namespace BuJitsuDo\Authentication\Domain\Dto;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Class PersonDto
 *
 * @package BuJitsuDo.Authentication
 */
class PersonDto {

	use ImageTrait;

	/**
	 * @var NodeInterface
	 */
	protected $referenceNode = NULL;

	/**
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $firstName;

	/**
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $lastName;

	/**
	 * @var string
	 */
	protected $address;

	/**
	 * @var string
	 */
	protected $zipCode;

	/**
	 * @var string
	 */
	protected $city;

	/**
	 * @var string
	 */
	protected $phone;

	/**
	 * @var \DateTime
	 */
	protected $dateOfBirth;

	/**
	 * @var string
	 * @Flow\Validate(type="EmailAddress")
	 */
	protected $emailAddress;

	/**
	 * @var boolean
	 */
	protected $jiuJitsu;

	/**
	 * @var boolean
	 */
	protected $buJitsuDo;

	/**
	 * @var string
	 */
	protected $jiuJitsuDegree;

	/**
	 * @var string
	 */
	protected $buJitsuDoDegree;

	/**
	 * @var string
	 */
	protected $gender;

	/**
	 * @param Image $image
	 */
	public function __construct(Image $image = NULL) {
		if ($image instanceof Image) {
			$this->setImage($image);
		}
	}

	/**
	 * Returns the FirstName
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Sets the FirstName
	 *
	 * @param string $firstName
	 * @return void
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * Returns the LastName
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Sets the LastName
	 *
	 * @param string $lastName
	 * @return void
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * Returns the Address
	 *
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * Sets the Address
	 *
	 * @param string $address
	 * @return void
	 */
	public function setAddress($address) {
		$this->address = $address;
	}

	/**
	 * Returns the ZipCode
	 *
	 * @return string
	 */
	public function getZipCode() {
		return $this->zipCode;
	}

	/**
	 * Sets the ZipCode
	 *
	 * @param string $zipCode
	 * @return void
	 */
	public function setZipCode($zipCode) {
		$this->zipCode = $zipCode;
	}

	/**
	 * Returns the City
	 *
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * Sets the City
	 *
	 * @param string $city
	 * @return void
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * Returns the Phone
	 *
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}

	/**
	 * Sets the Phone
	 *
	 * @param string $phone
	 * @return void
	 */
	public function setPhone($phone) {
		$this->phone = $phone;
	}

	/**
	 * Returns the DateOfBirth
	 *
	 * @return \DateTime
	 */
	public function getDateOfBirth() {
		return $this->dateOfBirth;
	}

	/**
	 * Sets the DateOfBirth
	 *
	 * @param \DateTime $dateOfBirth
	 * @return void
	 */
	public function setDateOfBirth($dateOfBirth) {
		$this->dateOfBirth = $dateOfBirth;
	}

	/**
	 * Returns the EmailAddress
	 *
	 * @return string
	 */
	public function getEmailAddress() {
		return $this->emailAddress;
	}

	/**
	 * Sets the EmailAddress
	 *
	 * @param string $emailAddress
	 * @return void
	 */
	public function setEmailAddress($emailAddress) {
		$this->emailAddress = $emailAddress;
	}

	/**
	 * Returns the JiuJitsu
	 *
	 * @return boolean
	 */
	public function getJiuJitsu() {
		return $this->jiuJitsu;
	}

	/**
	 * Sets the JiuJitsu
	 *
	 * @param boolean $jiuJitsu
	 * @return void
	 */
	public function setJiuJitsu($jiuJitsu) {
		$this->jiuJitsu = $jiuJitsu;
	}

	/**
	 * Returns the BuJitsuDo
	 *
	 * @return boolean
	 */
	public function getBuJitsuDo() {
		return $this->buJitsuDo;
	}

	/**
	 * Sets the BuJitsuDo
	 *
	 * @param boolean $buJitsuDo
	 * @return void
	 */
	public function setBuJitsuDo($buJitsuDo) {
		$this->buJitsuDo = $buJitsuDo;
	}

	/**
	 * Returns the JiuJitsuDegree
	 *
	 * @return string
	 */
	public function getJiuJitsuDegree() {
		return $this->jiuJitsuDegree;
	}

	/**
	 * Sets the JiuJitsuDegree
	 *
	 * @param string $jiuJitsuDegree
	 * @return void
	 */
	public function setJiuJitsuDegree($jiuJitsuDegree) {
		$this->jiuJitsuDegree = $jiuJitsuDegree;
	}

	/**
	 * Returns the BuJitsuDoDegree
	 *
	 * @return string
	 */
	public function getBuJitsuDoDegree() {
		return $this->buJitsuDoDegree;
	}

	/**
	 * Sets the BuJitsuDoDegree
	 *
	 * @param string $buJitsuDoDegree
	 * @return void
	 */
	public function setBuJitsuDoDegree($buJitsuDoDegree) {
		$this->buJitsuDoDegree = $buJitsuDoDegree;
	}

	/**
	 * Returns the Gender
	 *
	 * @return string
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets the Gender
	 *
	 * @param string $gender
	 * @return void
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * Returns the ReferenceNode
	 *
	 * @return NodeInterface
	 */
	public function getReferenceNode() {
		return $this->referenceNode;
	}

	/**
	 * Sets the ReferenceNode
	 *
	 * @param NodeInterface $referenceNode
	 * @return void
	 */
	public function setReferenceNode(NodeInterface $referenceNode) {
		$this->referenceNode = $referenceNode;
	}

	/**
	 * @return array
	 */
	public function getObjectVars() {
		return get_object_vars($this);
	}

}
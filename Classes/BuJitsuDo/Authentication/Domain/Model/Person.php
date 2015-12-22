<?php
namespace BuJitsuDo\Authentication\Domain\Model;

use TYPO3\TYPO3CR\Domain\Model\Node;

class Person extends Node {

	/**
	 * @return string
	 */
	public function getDisplayName() {
		$nameParts = [
			$this->getProperty('firstName'),
			$this->getProperty('lastName')
		];

		return implode(' ', $nameParts);
	}

	/**
	 * @param string $discipline
	 * @return string
	 */
	public function getPreviousDegree($discipline = 'buJitsuDoDegree') {
		$compare = [
			'none' => 'none',
			'kyu5' => 'none',
			'kyu4' => 'kyu5',
			'kyu3' => 'kyu4',
			'kyu2' => 'kyu3',
			'kyu1' => 'kyu2',
			'dan1' => 'kyu1',
			'dan2' => 'dan1',
			'dan3' => 'dan2',
			'dan4' => 'dan3'
		];
		return $compare[$this->getProperty($discipline)];
	}

	/**
	 * @param string $discipline
	 * @return string
	 */
	public function getNextDegree($discipline = 'buJitsuDoDegree') {
		$compare = [
			'none' => 'kyu5',
			'kyu5' => 'kyu4',
			'kyu4' => 'kyu3',
			'kyu3' => 'kyu2',
			'kyu2' => 'kyu1',
			'kyu1' => 'dan1',
			'dan1' => 'dan2',
			'dan2' => 'dan3',
			'dan3' => 'dan4'
		];
		return $compare[$this->getProperty($discipline)];
	}

	/**
	 * @return string
	 */
	public function getNextJiuJitsuDegree() {
		return $this->getNextDegree('jiuJitsuDegree');
	}

	/**
	 * @return string
	 */
	public function getPreviousJiuJitsuDegree() {
		return $this->getPreviousDegree('jiuJitsuDegree');
	}

}
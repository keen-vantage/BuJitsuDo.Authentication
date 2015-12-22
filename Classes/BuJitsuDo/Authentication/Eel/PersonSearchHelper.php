<?php
namespace BuJitsuDo\Authentication\Eel;

use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Search\Eel\SearchHelper;
use TYPO3\TYPO3CR\Search\Search\QueryBuilderInterface;
use TYPO3\Flow\Annotations as Flow;

class PersonSearchHelper extends SearchHelper implements ProtectedContextAwareInterface {

	/**
	 * @var SearchQueryBuilder
	 * @Flow\Inject
	 */
	protected $queryBuilder;

	/**
	 * @param NodeInterface $node
	 * @return QueryBuilderInterface
	 */
	public function query(NodeInterface $node) {
		$this->queryBuilder->nodeType('BuJitsuDo.Authentication:Person');
		return $this->queryBuilder->query($node);
	}

}
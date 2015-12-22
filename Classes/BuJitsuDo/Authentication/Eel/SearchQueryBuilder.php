<?php
namespace BuJitsuDo\Authentication\Eel;

use Flowpack\ElasticSearch\ContentRepositoryAdaptor\Eel\ElasticSearchQueryBuilder;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class SearchQueryBuilder extends ElasticSearchQueryBuilder {

	/**
	 * @var integer
	 */
	protected $month;

	/**
	 * @param NodeInterface $node
	 * @return \TYPO3\TYPO3CR\Search\Search\QueryBuilderInterface
	 */
	public function query (NodeInterface $node) {
		return parent::query ($node);
	}

}
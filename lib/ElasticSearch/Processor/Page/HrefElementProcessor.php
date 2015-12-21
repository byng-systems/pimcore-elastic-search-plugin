<?php

namespace ElasticSearch\Processor\Page;

use Pimcore\Model\Document\Tag\Multihref;
use ElasticSearch\Filter\FilterInterface;
use Object_Tag;

/**
 * Href Element Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class HrefElementProcessor
{
    /**
     * @var ObjectTagProcessor
     */
    private $objectTagProcessor;

    /**
     * @var FilterInterface
     */
    private $tagKeyFilter;


    /**
     * Constructor
     *
     * @param ObjectTagProcessor $objectTagProcessor
     * @param FilterInterface $tagKeyFilter
     */
    public function __construct(
        ObjectTagProcessor $objectTagProcessor,
        FilterInterface $tagKeyFilter
    ) {
        $this->objectTagProcessor = $objectTagProcessor;
        $this->tagKeyFilter = $tagKeyFilter;
    }

    /**
     * Process a multihref element
     *
     * @param array $body
     * @param string $elementKey
     * @param Multihref $element
     *
     * @return array
     */
    public function processElement(
        array &$body,
        $elementKey,
        Multihref $element
    ) {
        $tagKeys = [];
        $tagValues = [];

        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof Object_Tag) {
                $tagKeys[] = $this->tagKeyFilter->filter($childElement->getKey());
                $tagKeys[] = $this->objectTagProcessor->processTag($childElement);

                $tagValues[] = $childElement->getName();
            }
        }

        $body[$elementKey] = $tagKeys;
        $body[$elementKey . "-collated"] = implode(" ", $tagValues);

        return $tagKeys;
    }
}

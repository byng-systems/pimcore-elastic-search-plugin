<?php

namespace Byng\Pimcore\Elasticsearch\Processor\Element;

use Byng\Pimcore\Elasticsearch\Filter\FilterInterface;
use Pimcore\Model\Document\Tag;
use Pimcore\Model\Object\Tag as ObjectTag;

/**
 * Href Element Processor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class HrefElementProcessor
{
    /**
     * @var ObjectTagProcessor
     */
    protected $objectTagProcessor;

    /**
     * @var FilterInterface
     */
    protected $tagKeyFilter;


    /**
     * HrefElementProcessor constructor.
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
     * Process a multihref element.
     *
     * @param array $body
     * @param string $elementKey
     * @param Tag\Multihref $element
     * @return array
     */
    public function processElement(
        array &$body,
        $elementKey,
        Tag\Multihref $element
    ) {
        $tagKeys = [];
        $tagValues = [];

        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof ObjectTag) {
                $tagKeys[] = $this->tagKeyFilter->filter($childElement->getKey());
                $tagKeys[] = $this->objectTagProcessor->processTag($childElement);

                $tagValues[] = $childElement->getName();
            }
        }

        $body[$elementKey] = $tagKeys;
        $body[$elementKey . '-collated'] = implode(' ', $tagValues);

        return $tagKeys;
    }

}

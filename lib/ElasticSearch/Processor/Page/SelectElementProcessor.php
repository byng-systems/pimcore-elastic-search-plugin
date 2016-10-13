<?php

namespace ElasticSearch\Processor\Page;

use ElasticSearch\Filter\FilterInterface;
use ElasticSearch\Processor\ProcessorException;
use Pimcore\Model\Document\Tag;
use Pimcore\Model\Object\AbstractObject;

/**
 * SelectElementProcessor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class SelectElementProcessor
{
    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @var ElementProcessor
     */
    protected $fallbackProcessor;


    /**
     * SelectElementProcessor constructor.
     *
     * @param FilterInterface $filter
     * @param ElementProcessor $fallbackProcessor
     */
    public function __construct(FilterInterface $filter, ElementProcessor $fallbackProcessor)
    {
        $this->filter = $filter;
        $this->fallbackProcessor = $fallbackProcessor;
    }

    /**
     * Process a select element.
     *
     * @param array $body
     * @param string $key
     * @param Tag\Select $select
     * @return mixed
     * @throws ProcessorException
     */
    public function processElement(array &$body, $key, Tag\Select $select)
    {
        $elementData = $select->getData();

        if (
            is_numeric(($elementData = trim($elementData)))
            && ($object = AbstractObject::getById($elementData)) instanceof AbstractObject
        ) {
            $rawElementData = $object->getKey();

            $body[$key] = [
                $elementData,
                $this->filter->filter($rawElementData)
            ];

            return ($body[$key . '-collated'] = $rawElementData);
        }

        return $body[$key] = $this->fallbackProcessor->processElement($select);
    }

    /**
     * Process MultiSelect Element.
     *
     * @param array           $body
     * @param string          $key
     * @param Tag\Multiselect $select
     * @return mixed
     */
    public function processMultiSelectElement(array &$body, $key, Tag\Multiselect $select)
    {
        return ($body[$key] = $select->getData());
    }
}

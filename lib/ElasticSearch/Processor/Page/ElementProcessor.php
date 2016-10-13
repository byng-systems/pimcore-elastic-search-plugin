<?php

namespace ElasticSearch\Processor\Page;

use ElasticSearch\Processor\ProcessorException;
use NF\HtmlToText;
use Pimcore\Model\Document\Tag;

/**
 * ElementProcessor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class ElementProcessor
{
    /**
     * @var HtmlToText
     */
    protected $htmlToTextFilter;


    /**
     * ElementProcessor constructor.
     *
     * @param HtmlToText $htmlToTextFilter
     */
    public function __construct(HtmlToText $htmlToTextFilter)
    {
        $this->htmlToTextFilter = $htmlToTextFilter;
    }

    /**
     * Process a basic Pimcore element.
     *
     * @param Tag $tag
     * @return string
     * @throws ProcessorException
     */
    public function processElement(Tag $tag)
    {
        $elementData = $tag->getData();

        if (!is_string($elementData) || ($elementData = trim($elementData)) === '') {
            throw new ProcessorException(
                'This processor only accepts tags with immediate string data'
            );
        }

        // This needs to be handled much more elegantly than with the error suppression operator
        return @$this->htmlToTextFilter->convert($elementData);
    }
}

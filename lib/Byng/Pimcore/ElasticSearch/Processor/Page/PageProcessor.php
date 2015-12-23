<?php

namespace Byng\Pimcore\Elasticsearch\Processor\Page;

use Byng\Pimcore\Elasticsearch\Processor\ProcessorException;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Tag;

/**
 * Page Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class PageProcessor
{
    /**
     * @var ElementProcessor
     */
    private $elementProcessor;

    /**
     * @var DateElementProcessor
     */
    private $dateElementProcessor;

    /**
     * @var SelectElementProcessor
     */
    private $selectElementProcessor;


    /**
     * Constructor
     *
     * @param ElementProcessor $elementProcessor
     * @param DateElementProcessor $dateElementProcessor
     * @param SelectElementProcessor $selectElementProcessor
     */
    public function __construct(
        ElementProcessor $elementProcessor,
        DateElementProcessor $dateElementProcessor,
        SelectElementProcessor $selectElementProcessor
    ) {
        $this->elementProcessor = $elementProcessor;
        $this->dateElementProcessor = $dateElementProcessor;
        $this->selectElementProcessor = $selectElementProcessor;
    }

    /**
     * Process a page
     *
     * @param Page $document
     *
     * @return array
     */
    public function processPage(Page $document)
    {
        $body = [
            "controller" => $document->getController(),
            "action" => $document->getAction(),
            "created" => $document->getCreationDate(),
            "title" => $document->getTitle(),
            "description" => $document->getDescription()
        ];

        /* @var Tag $element */
        foreach ($document->getElements() as $key => $element) {
            if (!($element instanceof Tag)) {
                continue;
            }

            try {
                $this->processPageElement($body, $key, $element);
            } catch (ProcessorException $ex) {
                continue;
            }
        }

        return $body;
    }

    /**
     * Process page element
     *
     * @param array $body
     * @param string $elementKey
     * @param Tag $element
     */
    private function processPageElement(
        array &$body,
        $elementKey,
        Tag $element
    ) {
        switch (ltrim(get_class($element), "\\")) {
            case "Document_Tag_Select":
                /** @var Tag\Select $element */
                $this->selectElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case "Document_Tag_Multiselect":
                /** @var Tag\Multiselect $element */
                $this->selectElementProcessor->processMultiSelectElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case "Document_Tag_Date":
                /** @var Tag\Date $element */
                $body[$elementKey] = $this->dateElementProcessor->processElement($element);
                return;

            case "Document_Tag":
            default:
                $body[$elementKey] = $this->elementProcessor->processElement($element);
                return;
        }
    }
}

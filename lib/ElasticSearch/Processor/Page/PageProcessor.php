<?php

namespace ElasticSearch\Processor\Page;

use ElasticSearch\Processor\ProcessorException;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Tag;

/**
 * PageProcessor
 *
 * @author M.D.Ward <matthew.ward@byng.co>
 */
class PageProcessor
{
    /**
     * @var ElementProcessor
     */
    protected $elementProcessor;

    /**
     * @var DateElementProcessor
     */
    protected $dateElementProcessor;

    /**
     * @var SelectElementProcessor
     */
    protected $selectElementProcessor;

    /**
     * @var HrefElementProcessor
     */
    protected $hrefElementProcessor;


    /**
     * PageProcessor constructor.
     *
     * @param ElementProcessor $elementProcessor
     * @param DateElementProcessor $dateElementProcessor
     * @param SelectElementProcessor $selectElementProcessor
     * @param HrefElementProcessor $hrefElementProcessor
     */
    public function __construct(
        ElementProcessor $elementProcessor,
        DateElementProcessor $dateElementProcessor,
        SelectElementProcessor $selectElementProcessor,
        HrefElementProcessor $hrefElementProcessor
    ) {
        $this->elementProcessor = $elementProcessor;
        $this->dateElementProcessor = $dateElementProcessor;
        $this->selectElementProcessor = $selectElementProcessor;
        $this->hrefElementProcessor = $hrefElementProcessor;
    }

    /**
     * Process a Pimcore page.
     *
     * @param Page $document
     * @return array
     */
    public function processPage(Page $document)
    {
        $body = [
            'controller'    =>  $document->getController(),
            'action'        =>  $document->getAction(),
            'created'       =>  $document->getCreationDate(),
            'title'         =>  $document->getTitle(),
            'description'   =>  $document->getDescription()
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
     * Process a page element.
     *
     * @param array $body
     * @param string $elementKey
     * @param Tag $element
     */
    protected function processPageElement(array &$body, $elementKey, Tag $element)
    {
        switch (ltrim(get_class($element), '\\')) {
            case 'Document_Tag_Multihref':
            case Tag\Multihref::class:
                $this->hrefElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case 'Document_Tag_Select':
            case Tag\Select::class:
                $this->selectElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case 'Document_Tag_Multiselect':
            case Tag\Multiselect::class:
                $this->selectElementProcessor->processMultiSelectElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case 'Document_Tag_Date':
            case Tag\Date::class:
                $body[$elementKey] = $this->dateElementProcessor->processElement($element);
                return;

            case 'Document_Tag':
            case Tag::class:
            default:
                $body[$elementKey] = $this->elementProcessor->processElement($element);
                return;
        }
    }
}

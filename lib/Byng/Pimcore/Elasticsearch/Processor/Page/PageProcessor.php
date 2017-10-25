<?php

/**
 * This file is part of the "byng/pimcore-elasticsearch-plugin" project.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the LICENSE is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Byng\Pimcore\Elasticsearch\Processor\Page;

use Byng\Pimcore\Elasticsearch\Processor\Element\DateElementProcessor;
use Byng\Pimcore\Elasticsearch\Processor\Element\ElementProcessor;
use Byng\Pimcore\Elasticsearch\Processor\Element\SelectElementProcessor;
use Byng\Pimcore\Elasticsearch\Processor\Element\InputElementProcessor;
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
     * @var InputElementProcessor
     */
    private $inputElementProcessor;

    /**
     * Constructor
     *
     * @param ElementProcessor       $elementProcessor
     * @param DateElementProcessor   $dateElementProcessor
     * @param SelectElementProcessor $selectElementProcessor
     * @param InputElementProcessor  $inputElementProcessor
     */
    public function __construct(
        ElementProcessor $elementProcessor,
        DateElementProcessor $dateElementProcessor,
        SelectElementProcessor $selectElementProcessor,
        InputElementProcessor $inputElementProcessor
    ) {
        $this->elementProcessor = $elementProcessor;
        $this->dateElementProcessor = $dateElementProcessor;
        $this->selectElementProcessor = $selectElementProcessor;
        $this->inputElementProcessor = $inputElementProcessor;
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
            case "Pimcore\Model\Document\Tag\Select":
                /** @var Tag\Select $element */
                $this->selectElementProcessor->processElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case "Document_Tag_Multiselect":
            case "Pimcore\Model\Document\Tag\Multiselect":
                /** @var Tag\Multiselect $element */
                $this->selectElementProcessor->processMultiSelectElement(
                    $body,
                    $elementKey,
                    $element
                );
                return;

            case "Document_Tag_Date":
            case "Pimcore\Model\Document\Tag\Date":
                /** @var Tag\Date $element */
                $body[$elementKey] = $this->dateElementProcessor->processElement($element);
                return;

            case "Document_Tag_Input":
            case "Pimcore\Model\Document\Tag\Input":
                /** @var Tag\Input $element */
                $body[$elementKey] = $this->inputElementProcessor->processElement($element);
                return;

            default:
                $body[$elementKey] = $this->elementProcessor->processElement($element);
                return;
        }
    }
}

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

use Byng\Pimcore\Elasticsearch\Filter\FilterInterface;
use Pimcore\Model\Document\Tag\Multiselect;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Model\Object\AbstractObject;

/**
 * Select Element Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class SelectElementProcessor
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var ElementProcessor
     */
    private $fallbackProcessor;


    /**
     * Constructor
     *
     * @param FilterInterface $filter
     * @param ElementProcessor $fallbackProcessor
     */
    public function __construct(
        FilterInterface $filter,
        ElementProcessor $fallbackProcessor
    ) {
        $this->filter = $filter;
        $this->fallbackProcessor = $fallbackProcessor;
    }

    /**
     * Process element
     *
     * @param array $body
     * @param string $key
     * @param Select $select
     *
     * @return string
     */
    public function processElement(
        array &$body,
        $key,
        Select $select
    ) {
        $elementData = trim($select->getData());
        $object = AbstractObject::getById($elementData);

        if (is_numeric($elementData) && $object instanceof AbstractObject) {
            $rawElementData = $object->getKey();

            $body[$key] = [
                $elementData,
                $this->filter->filter($rawElementData)
            ];

            return ($body[$key . "-collated"] = $rawElementData);
        }

        return $body[$key] = $this->fallbackProcessor->processElement($select);
    }

    /**
     * Process MultiSelect Element
     *
     * @param array $body
     * @param string $key
     * @param Multiselect $select
     *
     * @return string|array
     */
    public function processMultiSelectElement(
        array &$body,
        $key,
        Multiselect $select
    ) {
        return ($body[$key] = $select->getData());
    }
}

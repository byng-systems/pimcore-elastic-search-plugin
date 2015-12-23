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

use Byng\Pimcore\Elasticsearch\Processor\ProcessorException;
use NF\HtmlToText;
use Pimcore\Model\Document\Tag;

/**
 * Element Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class ElementProcessor
{
    /**
     * @var HtmlToText
     */
    private $htmlToTextFilter;


    /**
     * Constructor
     *
     * @param HtmlToText $htmlToTextFilter
     */
    public function __construct(HtmlToText $htmlToTextFilter)
    {
        $this->htmlToTextFilter = $htmlToTextFilter;
    }

    /**
     * Process a generic element
     *
     * @param Tag $tag
     *
     * @return string
     *
     * @throws ProcessorException
     */
    public function processElement(Tag $tag)
    {
        $elementData = $tag->getData();

        if (!is_string($elementData) || ($elementData = trim($elementData)) === "") {
            throw new ProcessorException(
                "This processor only accepts tags with immediate string data"
            );
        }

        // This needs to be handled much more elegantly than with the error suppression operator
        return @$this->htmlToTextFilter->convert($elementData);
    }
}

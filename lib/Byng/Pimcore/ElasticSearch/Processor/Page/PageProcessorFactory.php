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
use Byng\Pimcore\Elasticsearch\Filter\TagKeyFilter;
use NF\HtmlToText;

/**
 * PageProcessor Factory
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class PageProcessorFactory
{
    /**
     * Build the PageProcessor
     *
     * @param FilterInterface|null $filter
     *
     * @return PageProcessor
     */
    public function build(FilterInterface $filter = null)
    {
        $elementProcessor = new ElementProcessor(new HtmlToText());

        return new PageProcessor(
            $elementProcessor,
            new DateElementProcessor(),
            new SelectElementProcessor(
                ($filter ?: new TagKeyFilter()),
                $elementProcessor
            )
        );
    }
}

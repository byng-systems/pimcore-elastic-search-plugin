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

namespace Byng\Pimcore\Elasticsearch\Processor\Element;

use Pimcore\Model\Document\Tag\Date;

/**
 * Date Element Processor
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class DateElementProcessor
{
    /**
     * Process a date tag
     *
     * @param Date $tag
     *
     * @return string
     */
    public function processElement(Date $tag)
    {
        return (int) $tag->getDataForResource();
    }
}

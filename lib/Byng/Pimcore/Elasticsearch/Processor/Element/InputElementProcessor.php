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

use Pimcore\Model\Document\Tag\Input;

/**
 * Input Element Processor
 *
 * @author Asim Liaquat <asimlqt22@gmail.com>
 */
final class InputElementProcessor
{
    /**
     * Process an input tag.
     *
     * If the value is a number then it will actually return an int or float
     * otherwise a string.
     *
     * @param Input $tag
     *
     * @return string|int|float
     */
    public function processElement(Input $tag)
    {
        $value = $tag->getData();
        return $this->getNumericType($value, $value);
    }

    private function getNumericType($value, $default = null)
    {
        $val = trim($value);
        if (is_numeric($val)) {
            if (preg_match("/^[0-9]+$/", abs($val))) {
                return (int) $val;
            } else {
                return (float) $val;
            }
        }

        return $default;
    }
}

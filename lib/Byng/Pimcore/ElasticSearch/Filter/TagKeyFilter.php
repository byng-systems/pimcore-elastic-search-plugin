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

namespace Byng\Pimcore\Elasticsearch\Filter;

/**
 * TagKeyFilter
 *
 * @author Elliot Wright <elliot@byng.co>
 * @author Matt Ward <matt@byng.co>
 */
final class TagKeyFilter implements FilterInterface
{
    /**
     * @var array
     */
    private $replaceable = [ " ", "-", "\"" ];

    /**
     * @var string
     */
    private $replacement = "_";


    /**
     * Constructor
     *
     * @param array|null $replaceable
     * @param string|null $replacement
     */
    public function __construct(array $replaceable = null, $replacement = null)
    {
        if ($replaceable !== null) {
            $this->setReplaceable($replaceable);
        }

        if ($replacement !== null) {
            $this->setReplacement($replacement);
        }
    }

    /**
     * Get replaceable
     *
     * @return array
     */
    public function getReplaceable()
    {
        return $this->replaceable;
    }

    /**
     * Get replacement
     *
     * @return string
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Set replaceable
     *
     * @param array $replaceable
     *
     * @return $this
     */
    public function setReplaceable(array $replaceable)
    {
        $this->replaceable = $replaceable;

        return $this;
    }

    /**
     * Set replacement
     *
     * @param string $replacement
     *
     * @return $this
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($input)
    {
        return str_replace(
            $this->replaceable,
            $this->replacement,
            strtolower((string) $input)
        );
    }
}

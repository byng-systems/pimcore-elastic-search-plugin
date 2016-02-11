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

namespace Byng\Pimcore\Elasticsearch\Event;

/**
 * EventManager Interface
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
interface EventManagerInterface
{
    /**
     * Attach all of this event manager's events.
     *
     * @return void
     */
    public function attachEvents();
}

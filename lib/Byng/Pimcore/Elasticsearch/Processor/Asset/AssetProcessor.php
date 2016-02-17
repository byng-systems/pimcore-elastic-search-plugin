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

namespace Byng\Pimcore\Elasticsearch\Processor\Asset;

use Pimcore\Model\Asset;
use Pimcore\Model\Property;

/**
 * Asset Processor
 *
 * @author Elliot Wright <elliot@elliotwright.co>
 */
final class AssetProcessor
{
    /**
     * Process an asset
     *
     * @param Asset $asset
     *
     * @return array
     */
    public function processAsset(Asset $asset)
    {
        $body = [];
        $body["creationDate"] = $asset->getCreationDate();
        $body["modificationDate"] = $asset->getModificationDate();
        $body["mimetype"] = $asset->getMimetype();

        if ($creationDate = $asset->getMetadata("creationDate")) {
            $body["creationDate"] = $creationDate;
        }

        if ($modificationDate = $asset->getMetadata("modificationDate")) {
            $body["modificationDate"] = $modificationDate;
        }

        $metadata = $asset->getMetadata();

        if (!empty($metadata)) {
            $body["metadata"] = [];

            foreach ($metadata as $item) {
                $body["metadata"][$item["name"]] = $item["data"];
            }
        }

        $properties = $asset->getProperties();

        if (!empty($properties)) {
            $body["properties"] = [];

            foreach ($properties as $property) {
                /** @var Property $property */
                $name = $property->getName();
                $value = $property->getData() ?: $name;

                $parsed = preg_replace("/(-|_|\\.|,|;|:)/", " ", $value);

                $body["properties"][$name] = $value;
                $body["properties_parsed"][$name] = $parsed;
            }
        }

        $type = $asset->getType();

        $body["type"] = $type;

        if ($type === "document") {
            $body["content"] = base64_encode(file_get_contents($asset->getFileSystemPath()));
        }

        return $body;
    }
}

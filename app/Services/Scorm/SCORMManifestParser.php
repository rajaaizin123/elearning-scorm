<?php

namespace App\Services\Scorm;

use RuntimeException;
use SimpleXMLElement;

class SCORMManifestParser
{
    public function parse(string $manifestPath): array
    {
        if (! is_file($manifestPath)) {
            throw new RuntimeException('imsmanifest.xml not found in SCORM package.');
        }

        $xml = simplexml_load_file($manifestPath);

        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException('Invalid SCORM manifest XML.');
        }

        $resources = $this->resources($xml);
        $organization = $this->first($xml, 'organizations/organization');
        $item = $organization ? $this->first($organization, 'item') : null;
        $identifier = $item ? (string) ($item['identifierref'] ?? '') : '';
        $resource = $resources[$identifier] ?? reset($resources);

        if (! $resource || empty($resource['href'])) {
            throw new RuntimeException('SCORM launch resource could not be resolved.');
        }

        return [
            'title' => $this->text($organization, 'title') ?: 'SCORM Package',
            'version' => $this->version($xml),
            'launch_path' => $resource['href'],
            'resources' => $resources,
            'identifier' => (string) ($xml['identifier'] ?? ''),
        ];
    }

    private function resources(SimpleXMLElement $xml): array
    {
        $items = [];

        foreach ($this->all($xml, 'resources/resource') as $resource) {
            $id = (string) $resource['identifier'];
            $adlcp = $resource->attributes('adlcp', true);
            $items[$id] = [
                'identifier' => $id,
                'type' => (string) $resource['type'],
                'scorm_type' => (string) ($adlcp['scormtype'] ?? $adlcp['scormType'] ?? ''),
                'href' => (string) $resource['href'],
            ];
        }

        return $items;
    }

    private function version(SimpleXMLElement $xml): string
    {
        $schemaVersion = strtolower($this->text($this->first($xml, 'metadata'), 'schemaversion') ?: '1.2');

        return str_contains($schemaVersion, '2004') || str_contains($schemaVersion, '1.3') ? '2004' : '1.2';
    }

    private function first(SimpleXMLElement $xml, string $path): ?SimpleXMLElement
    {
        $items = $this->all($xml, $path);

        return $items[0] ?? null;
    }

    /**
     * Select nodes by local-name so SCORM 1.2 and 2004 manifests work with or without XML namespaces.
     */
    private function all(SimpleXMLElement $xml, string $path): array
    {
        $segments = array_filter(explode('/', $path));
        $xpath = implode('/', array_map(fn (string $segment) => "*[local-name()='{$segment}']", $segments));

        return $xml->xpath($xpath) ?: [];
    }

    private function text(?SimpleXMLElement $xml, string $path): string
    {
        if (! $xml) {
            return '';
        }

        $node = $this->first($xml, $path);

        return $node ? trim((string) $node) : '';
    }
}

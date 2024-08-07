<?php

declare(strict_types=1);

namespace Loouss\ObsClient;

use Loouss\ObsClient\Http\Client;

class ObjectClient extends Client
{
    public function putObject(string $key, string $body, array $headers = [])
    {
        return $this->put(\urlencode($key), \compact('body', 'headers'));
    }

    public function headObject(string $key, string $versionId = null, array $headers = [])
    {
        return $this->head(\urlencode($key), [
            'query' => \compact('versionId'),
            'headers' => $headers,
        ]);
    }

    public function deleteObject(string $key, string $versionId = null)
    {
        return $this->delete(\urlencode($key), [
            'query' => \compact('versionId'),
        ]);
    }

    public function getObject(string $key, string $versionId = null)
    {
        return $this->get(\urlencode($key), [
            'query' => \compact('versionId'),
        ]);
    }

    public function getObjectAcl(string $key, string $versionId = null)
    {
        return $this->get(\urlencode($key), [
            'query' => [
                'acl' => '',
            ]
        ]);
    }

}

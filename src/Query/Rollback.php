<?php

namespace MongoDB\Laravel\Query;

class Rollback
{
    public function __construct(private string $collection, private string $operation, private array $ids)
    {
        $oplogs = $this->read();

        $oplogs = array_merge_recursive($oplogs, $this->prepareNewOplog());

        $this->write($oplogs);
    }

    private function read(string $filename = 'rollback.json'): array
    {
        if ( file_exists(storage_path($filename)) && $content = file_get_contents(storage_path($filename)) )
        {
            return json_decode($content, true) ?? [];
        }
        else
        {
            return [];
        }
    }

    private function write(array $content, string $filename = 'rollback.json')
    {
        file_put_contents(storage_path($filename), json_encode($content, JSON_PRETTY_PRINT));
    }

    private function prepareNewOplog(): array
    {
        $result[$this->operation][] = [ $this->collection, $this->ids ];

        return $result;
    }
}

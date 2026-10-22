<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class SupabaseStorage
{
    private function base(): string
    {
        return rtrim(config('kb.supabase_url'), '/');
    }

    private function headers(): array
    {
        $key = config('kb.supabase_key');
        return ['apikey' => $key, 'Authorization' => 'Bearer ' . $key];
    }

    public function upload(UploadedFile $file, string $path): string
    {
        $bucket = config('kb.bucket');
        $res = Http::withHeaders($this->headers())
            ->withBody(file_get_contents($file->getRealPath()), 'application/pdf')
            ->post($this->base() . "/storage/v1/object/{$bucket}/{$path}");

        if (!$res->successful()) {
            throw new \RuntimeException('Upload gagal: ' . $res->body());
        }

        return $this->base() . "/storage/v1/object/public/{$bucket}/{$path}";
    }

    public function delete(string $path): void
    {
        $bucket = config('kb.bucket');
        Http::withHeaders($this->headers())
            ->delete($this->base() . "/storage/v1/object/{$bucket}/{$path}");
    }
}

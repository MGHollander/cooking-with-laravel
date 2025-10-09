<?php

namespace Tests\Unit;

use App\Support\FileHelper;
use PHPUnit\Framework\TestCase;

class FileHelperTest extends TestCase
{
    public function test_clean_url_removes_query_parameters(): void
    {
        $url = 'https://example.com/recipe?utm_source=newsletter&utm_medium=email';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_removes_fragments(): void
    {
        $url = 'https://example.com/recipe#instructions';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_removes_both_query_and_fragments(): void
    {
        $url = 'https://example.com/recipe?param=value&another=test#section';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_preserves_path(): void
    {
        $url = 'https://example.com/recipes/chocolate-cake?source=google#ingredients';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipes/chocolate-cake', $cleanUrl);
    }

    public function test_clean_url_preserves_subdomain(): void
    {
        $url = 'https://recipes.example.com/chocolate-cake?utm_source=facebook';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://recipes.example.com/chocolate-cake', $cleanUrl);
    }

    public function test_clean_url_preserves_non_standard_ports(): void
    {
        $url = 'https://example.com:8080/recipe?param=value';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com:8080/recipe', $cleanUrl);
    }

    public function test_clean_url_handles_already_clean_urls(): void
    {
        $url = 'https://example.com/recipe';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_handles_root_path(): void
    {
        $url = 'https://example.com/?param=value#section';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/', $cleanUrl);
    }

    public function test_clean_url_handles_no_path(): void
    {
        $url = 'https://example.com?param=value#section';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com', $cleanUrl);
    }

    public function test_clean_url_handles_http_scheme(): void
    {
        $url = 'http://example.com/recipe?param=value#section';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('http://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_handles_complex_query_parameters(): void
    {
        $url = 'https://example.com/recipe?utm_source=social&utm_medium=facebook&utm_campaign=summer2024&fbclid=IwAR123456789';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('https://example.com/recipe', $cleanUrl);
    }

    public function test_clean_url_returns_original_on_parse_failure(): void
    {
        $invalidUrl = 'not-a-valid-url';
        $cleanUrl = FileHelper::cleanUrl($invalidUrl);

        $this->assertEquals($invalidUrl, $cleanUrl);
    }

    public function test_clean_url_handles_empty_string(): void
    {
        $url = '';
        $cleanUrl = FileHelper::cleanUrl($url);

        $this->assertEquals('', $cleanUrl);
    }
}

<?php

namespace App\Services;


class RssFeedBuilder
{
    protected array $mapping = [
        'title'       => 'webTitle',
        'link'        => 'webUrl',
        'description' => ['fields.trailText', 'webTitle'],
        'pubDate'     => 'webPublicationDate',
        'guid'        => 'id',
        'thumbnail'   => 'fields.thumbnail',
    ];

    protected array $channel = [
        'title'       => 'The Guardian - Latest News',
        'link'        => 'https://www.theguardian.com',
        'description' => 'Latest news and features from theguardian.com',
        'language'    => 'en-gb',
        'generator'   => 'Laravel Guardian RSS Service',
        'ttl'         => 10,
    ];

    protected int $maxItems = 30;

    public function build(array $apiResponse, array $overrideChannel = [])
    {
        $articles = $apiResponse['response']['results'] ?? [];
        $articles = array_slice($articles, 0, $this->maxItems);

        $channel = array_merge($this->channel, $overrideChannel);
        $buildDate = date(DATE_RSS);

        $xml  = $this->buildXmlHeader();
        $xml .= $this->buildChannelOpen($channel, $buildDate);

        foreach ($articles as $article) {
            $xml .= $this->buildItem($article);
        }

        $xml .= $this->buildChannelClose();

        return $xml;
    }

    protected function buildXmlHeader(): string
    {

        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
            '<rss version="2.0"
                    xmlns:content="http://purl.org/rss/1.0/modules/content/"
                    xmlns:media="http://search.yahoo.com/mrss/"
                    xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
    }

    protected function buildChannelOpen(array $channel, string $buildDate): string
    {

        $lines = [
            '  <channel>',
            '    <title>' . htmlspecialchars($channel['title'], ENT_QUOTES, 'UTF-8') . '</title>',
            '    <link>' . htmlspecialchars($channel['link'], ENT_QUOTES, 'UTF-8') . '</link>',
            '    <atom:link href="' . htmlspecialchars($channel['self'], ENT_QUOTES, 'UTF-8') .
                '" rel="self" type="application/rss+xml" />',
            '    <description>' . htmlspecialchars($channel['description'], ENT_QUOTES, 'UTF-8') . '</description>',
            '    <language>' . $channel['language'] . '</language>',
            '    <lastBuildDate>' . $buildDate . '</lastBuildDate>',
            '    <generator>' . htmlspecialchars($channel['generator'], ENT_QUOTES, 'UTF-8') . '</generator>',
            '    <ttl>' . (int) $channel['ttl'] . '</ttl>',
        ];

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function buildItem(array $article): string
    {
        $title       = $this->getValue($article, $this->mapping['title']);
        $link        = $this->getValue($article, $this->mapping['link']);
        $description = $this->getValue($article, $this->mapping['description']);
        $pubDate     = $this->formatPubDate($this->getValue($article, $this->mapping['pubDate']));
        $guid        = $this->getValue($article, $this->mapping['guid']) ?: $link;

        $isPermalink = filter_var($guid, FILTER_VALIDATE_URL) ? 'true' : 'false';

        $lines = [
            '    <item>',
            '      <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>',
            '      <link>' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</link>',
            '      <description><![CDATA[' . strip_tags($description) . ']]></description>',
            '      <pubDate>' . $pubDate . '</pubDate>',
            '      <guid isPermaLink="' . $isPermalink . '">' . htmlspecialchars($guid, ENT_QUOTES, 'UTF-8') . '</guid>',
        ];

        $thumbnail = $this->getValue($article, $this->mapping['thumbnail'] ?? null);
        if ($thumbnail) {
            $lines[] = '      <enclosure url="' . htmlspecialchars($thumbnail, ENT_QUOTES, 'UTF-8') . '" type="image/jpeg"/>';
        }

        $lines[] = '    </item>';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function buildChannelClose(): string
    {
        return '  </channel>' . PHP_EOL . '</rss>';
    }

    protected function getValue(array $data, string|array|null $path): string
    {
        if (!$path) {
            return '';
        }

        if (is_array($path)) {
            foreach ($path as $p) {
                $value = $this->getValue($data, $p);
                if ($value !== '') {
                    return $value;
                }
            }
            return '';
        }

        $current = $data;
        foreach (explode('.', $path) as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return '';
            }
            $current = $current[$key];
        }

        return is_string($current) ? trim($current) : '';
    }

    protected function formatPubDate(string $date): string
    {
        $timestamp = strtotime($date);
        return $timestamp ? date(DATE_RSS, $timestamp) : date(DATE_RSS);
    }
}

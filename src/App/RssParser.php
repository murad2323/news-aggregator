<?php

namespace App;

use DateTime;

class RssParser
{
    private \mysqli $db;
    private const RSS_URL = 'https://ria.ru/export/rss2/archive/index.xml';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function run(): void
    {
        $xml = simplexml_load_file(self::RSS_URL);
        if ($xml === false) {
            throw new \RuntimeException('Не удалось загрузить RSS');
        }

        $newCount = 0;
        $skipCount = 0;

        foreach ($xml->channel->item as $item) {
            $guid = (string)$item->guid;
            if (empty($guid)) continue;

            $title = (string)$item->title;
            $link = (string)$item->link;
            $description = (string)($item->description ?? '');
            $pubDate = $this->parseDate((string)$item->pubDate) ?? date('Y-m-d H:i:s');

            // Вставка с автоматическим пропуском дубликатов
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO news (guid, title, link, description, pub_date) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $guid, $title, $link, $description, $pubDate);
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                $skipCount++;
                $stmt->close();
                continue;
            }
            $newsId = $this->db->insert_id;
            $stmt->close();

            // Категории вынесены в отдельный метод
            $this->attachCategories($newsId, $item->category);
            $newCount++;
        }

        echo "Готово! Добавлено новых: $newCount, пропущено дублей: $skipCount\n";
    }

    private function attachCategories(int $newsId, $categories): void
    {
        if (empty($categories)) return;
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        foreach ($categories as $catName) {
            $catName = trim((string)$catName);
            if ($catName === '') continue;

            // Вставить категорию, если нет
            $stmt = $this->db->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $catName);
            $stmt->execute();
            $stmt->close();

            // Получить её id
            $stmt = $this->db->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->bind_param("s", $catName);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $categoryId = $row['id'];
                $stmtLink = $this->db->prepare(
                    "INSERT IGNORE INTO news_category (news_id, category_id) VALUES (?, ?)"
                );
                $stmtLink->bind_param("ii", $newsId, $categoryId);
                $stmtLink->execute();
                $stmtLink->close();
            }
            $stmt->close();
        }
    }

    private function parseDate(string $dateStr): ?string
    {
        if (empty($dateStr)) return null;
        $dt = DateTime::createFromFormat('D, d M Y H:i:s O', $dateStr);
        if ($dt) {
            return $dt->format('Y-m-d H:i:s');
        }
        $ts = strtotime($dateStr);
        if ($ts) {
            return date('Y-m-d H:i:s', $ts);
        }
        return null;
    }
}
<?php

namespace App;

use mysqli;

class NewsRepository
{
    private mysqli $db;
    private Cache $cache;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->cache = Cache::getInstance();
    }

    /**
     * Получить новости с фильтрацией и кешированием
     */
    public function getNews(string $category, string $dateFrom, string $dateTo): array
    {
        $cacheKey = 'news_list_' . md5($category . $dateFrom . $dateTo);
        $news = $this->cache->get($cacheKey);
        if ($news !== false) {
            return $news;
        }

        $sql = "SELECT n.id, n.title, n.link, n.pub_date, 
                       GROUP_CONCAT(c.name SEPARATOR ', ') AS categories
                FROM news n
                LEFT JOIN news_category nc ON n.id = nc.news_id
                LEFT JOIN categories c ON nc.category_id = c.id
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($category)) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM news_category nc2 
                        JOIN categories c2 ON nc2.category_id = c2.id 
                        WHERE nc2.news_id = n.id AND c2.name = ?
                      )";
            $params[] = $category;
            $types .= 's';
        }

        if (!empty($dateFrom)) {
            $sql .= " AND n.pub_date >= ?";
            $params[] = $dateFrom;
            $types .= 's';
        }

        if (!empty($dateTo)) {
            $sql .= " AND n.pub_date <= ?";
            $params[] = $dateTo . ' 23:59:59';
            $types .= 's';
        }

        $sql .= " GROUP BY n.id ORDER BY n.pub_date DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $this->cache->set($cacheKey, $news, 300);
        return $news;
    }

    /**
     * Получить список категорий (с кешированием)
     */
    public function getCategories(): array
    {
        $cacheKey = 'categories_list';
        $categories = $this->cache->get($cacheKey);
        if ($categories !== false) {
            return $categories;
        }

        $result = $this->db->query("SELECT name FROM categories ORDER BY name");
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        $this->cache->set($cacheKey, $categories, 600);
        return $categories;
    }
}
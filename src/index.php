<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\NewsRepository;

$repository = new NewsRepository();

$category = $_GET['category'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$news_list = $repository->getNews($category, $date_from, $date_to);
$categories = $repository->getCategories();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новостной агрегатор</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        .filter-form { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .filter-form label { margin-right: 10px; }
        .filter-form input, .filter-form select { margin-right: 20px; padding: 5px; }
        .news-item { border-bottom: 1px solid #ddd; padding: 15px 0; }
        .news-item h3 { margin: 0 0 5px; }
        .news-item .meta { color: #666; font-size: 0.9em; }
        .news-item .categories { color: #888; font-style: italic; }
    </style>
</head>
<body>
<h1>Новости РИА</h1>

<form class="filter-form" method="get">
    <label>Категория:
        <select name="category">
            <option value="">Все</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['name']) ?>"
                        <?= $category === $cat['name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Дата с:
        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
    </label>
    <label>по:
        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
    </label>
    <button type="submit">Фильтровать</button>
    <a href="?">Сбросить</a>
</form>

<?php if (empty($news_list)): ?>
    <p>Новости не найдены.</p>
<?php else: ?>
    <?php foreach ($news_list as $news): ?>
        <div class="news-item">
            <h3><a href="<?= htmlspecialchars($news['link']) ?>" target="_blank">
                    <?= htmlspecialchars($news['title']) ?>
                </a></h3>
            <div class="meta">
                <?= date('d.m.Y H:i', strtotime($news['pub_date'])) ?>
            </div>
            <?php if (!empty($news['categories'])): ?>
                <div class="categories"><?= htmlspecialchars($news['categories']) ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
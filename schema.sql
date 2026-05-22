USE news_aggregator;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guid VARCHAR(512) NOT NULL UNIQUE,
    title VARCHAR(512) NOT NULL,
    link VARCHAR(1024) NOT NULL,
    description TEXT,
    pub_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pub_date (pub_date),
    INDEX idx_guid (guid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE news_category (
    news_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (news_id, category_id),
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

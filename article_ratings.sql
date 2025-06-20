-- نظام تقييم المقالات
CREATE TABLE IF NOT EXISTS article_ratings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_rating (article_id, user_id),
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- للحصول على متوسط التقييم وعدد المقيمين:
-- SELECT article_id, AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM article_ratings GROUP BY article_id;

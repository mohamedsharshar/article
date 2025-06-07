-- إنشاء جدول التصنيفات
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE
);

-- إضافة عمود category_id لجدول المقالات
ALTER TABLE articles ADD COLUMN category_id INT NULL;
ALTER TABLE articles ADD CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- مثال لإضافة تصنيفات افتراضية
INSERT INTO categories (name, slug) VALUES
('تقنية', 'tech'),
('تصميم', 'design'),
('ذكاء اصطناعي', 'ai'),
('تطوير', 'development'),
('تجربة المستخدم', 'ux')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- تحديث المقالات القديمة لتكون بدون تصنيف (category_id = NULL)
UPDATE articles SET category_id = NULL WHERE category_id IS NULL;

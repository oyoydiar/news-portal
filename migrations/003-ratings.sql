CREATE TABLE ratings (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  news_url VARCHAR(500) NOT NULL,
  rating_type VARCHAR(20) NOT NULL CHECK (rating_type IN ('thumbs_up', 'thumbs_down')),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_ratings_user_id 
    FOREIGN KEY (user_id) 
    REFERENCES users(id) 
    ON DELETE CASCADE,
  
  CONSTRAINT unique_user_news 
    UNIQUE (user_id, news_url)
);

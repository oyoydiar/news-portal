CREATE TABLE api_logs (
    id SERIAL PRIMARY KEY,
    endpoint VARCHAR(500) NOT NULL,
    method VARCHAR(10) NOT NULL,
    request_params TEXT,
    request_headers TEXT,
    response_status INTEGER,
    response_body TEXT,
    response_headers TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    user_id INTEGER,
    duration_ms INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_api_logs_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE SET NULL
);
BEGIN;

DROP TABLE IF EXISTS configuration_component CASCADE;
DROP TABLE IF EXISTS component_images CASCADE;
DROP TABLE IF EXISTS quotes CASCADE;
DROP TABLE IF EXISTS configurations CASCADE;
DROP TABLE IF EXISTS wheel_hubs CASCADE;
DROP TABLE IF EXISTS wheel_components CASCADE;
DROP TABLE IF EXISTS wheel_categories CASCADE;
DROP TABLE IF EXISTS personal_access_tokens CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS migrations CASCADE;

CREATE TABLE migrations (
    id BIGSERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP(0) WITHOUT TIME ZONE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin')),
    remember_token VARCHAR(100),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration BIGINT NOT NULL
);

CREATE INDEX cache_expiration_index ON cache(expiration);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration BIGINT NOT NULL
);

CREATE INDEX cache_locks_expiration_index ON cache_locks(expiration);

CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX jobs_queue_index ON jobs(queue);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON failed_jobs(connection, queue, failed_at);

CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(80) NOT NULL UNIQUE,
    abilities TEXT,
    last_used_at TIMESTAMP(0) WITHOUT TIME ZONE,
    expires_at TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens(tokenable_type, tokenable_id);

CREATE TABLE wheel_categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    base_price NUMERIC(10, 2) NOT NULL,
    hero_image_url VARCHAR(255),
    available_colors JSON,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE wheel_hubs (
    id BIGSERIAL PRIMARY KEY,
    wheel_category_id BIGINT NOT NULL REFERENCES wheel_categories(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    engine_type VARCHAR(255) NOT NULL,
    horsepower INTEGER NOT NULL,
    image_url VARCHAR(255),
    price NUMERIC(10, 2) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE wheel_components (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL,
    category VARCHAR(255) NOT NULL,
    exclusive_group VARCHAR(255),
    image_url VARCHAR(255),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE configurations (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    wheel_category_id BIGINT NOT NULL REFERENCES wheel_categories(id) ON DELETE CASCADE,
    wheel_hub_id BIGINT NOT NULL REFERENCES wheel_hubs(id) ON DELETE CASCADE,
    name VARCHAR(255),
    description TEXT,
    total_price NUMERIC(10, 2) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE quotes (
    id BIGSERIAL PRIMARY KEY,
    configuration_id BIGINT NOT NULL REFERENCES configurations(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    total_amount NUMERIC(10, 2) NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE configuration_component (
    id BIGSERIAL PRIMARY KEY,
    configuration_id BIGINT NOT NULL REFERENCES configurations(id) ON DELETE CASCADE,
    component_id BIGINT NOT NULL REFERENCES wheel_components(id) ON DELETE CASCADE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE component_images (
    id BIGSERIAL PRIMARY KEY,
    component_id BIGINT NOT NULL REFERENCES wheel_components(id) ON DELETE CASCADE,
    wheel_category_id BIGINT NOT NULL REFERENCES wheel_categories(id) ON DELETE CASCADE,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (component_id, wheel_category_id)
);

INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_05_18_174229_create_car_models_table', 1),
('2026_05_18_174236_create_motorizations_table', 1),
('2026_05_18_174236_create_optionals_table', 1),
('2026_05_18_174237_create_configurations_table', 1),
('2026_05_18_174238_create_quotes_table', 1),
('2026_05_18_174406_create_configuration_optional_table', 1),
('2026_05_19_000000_add_role_to_users_table', 1),
('2026_05_28_000000_create_personal_access_tokens_table', 1),
('2026_05_28_000001_add_exclusive_group_to_optionals_table', 1),
('2026_06_04_120000_add_image_url_to_optionals_table', 1),
('2026_06_04_120001_add_image_url_to_motorizations_table', 1),
('2026_06_04_120002_add_images_to_car_models_table', 1),
('2026_06_05_100000_create_optional_images_table', 1),
('2026_08_25_095929_add_last_name_to_users_table', 1),
('2026_08_25_140433_rename_vehicle_tables_to_wheel_tables', 1),
('2026_08_25_150000_rename_vehicle_columns_to_wheel_columns', 1);

INSERT INTO wheel_categories (id, name, description, base_price, hero_image_url, available_colors, created_at, updated_at) VALUES
(1, 'Strada', NULL, 0.00, '/wheel-configurator/cards/strada.jpg', NULL, NOW(), NOW()),
(2, 'Gravel', NULL, 0.00, '/wheel-configurator/cards/gravel.jpg', NULL, NOW(), NOW()),
(3, 'MTB', NULL, 0.00, '/wheel-configurator/cards/mtb.jpg', NULL, NOW(), NOW());

INSERT INTO wheel_hubs (id, wheel_category_id, name, engine_type, horsepower, image_url, price, created_at, updated_at) VALUES
(1, 1, 'Mozzo DT Swiss 350', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss350.jpg', 300.00, NOW(), NOW()),
(2, 1, 'Mozzo DT Swiss 240', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss240.jpg', 450.00, NOW(), NOW()),
(3, 1, 'Mozzo DT Swiss 180', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss180.jpg', 700.00, NOW(), NOW()),
(4, 1, 'Mozzo Extralight', 'mozzo', 0, '/wheel-configurator/components/mozzo-extralight.jpeg', 700.00, NOW(), NOW()),
(5, 1, 'Mozzo Damil', 'mozzo', 0, '/wheel-configurator/components/mozzo-damil.webp', 300.00, NOW(), NOW()),
(6, 1, 'Mozzo Erase', 'mozzo', 0, '/wheel-configurator/components/mozzo-erase.jpg', 330.00, NOW(), NOW()),
(7, 1, 'Mozzo Bitex', 'mozzo', 0, '/wheel-configurator/components/mozzo-bitex.png', 250.00, NOW(), NOW()),
(8, 1, 'Mozzo Industry Nine', 'mozzo', 0, '/wheel-configurator/components/mozzo-industrynine.webp', 700.00, NOW(), NOW()),
(9, 1, 'Mozzo Chris King', 'mozzo', 0, '/wheel-configurator/components/mozzo-chrisking.webp', 850.00, NOW(), NOW()),
(10, 1, 'Mozzo OGS', 'mozzo', 0, '/wheel-configurator/components/mozzo-ogs.webp', 350.00, NOW(), NOW()),
(11, 1, 'Mozzo Spank', 'mozzo', 0, '/wheel-configurator/components/mozzo-spank.webp', 370.00, NOW(), NOW()),
(12, 2, 'Mozzo DT Swiss 350', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss350.jpg', 300.00, NOW(), NOW()),
(13, 2, 'Mozzo DT Swiss 240', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss240.jpg', 450.00, NOW(), NOW()),
(14, 2, 'Mozzo DT Swiss 180', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss180.jpg', 700.00, NOW(), NOW()),
(15, 2, 'Mozzo Extralight', 'mozzo', 0, '/wheel-configurator/components/mozzo-extralight.jpeg', 700.00, NOW(), NOW()),
(16, 2, 'Mozzo Damil', 'mozzo', 0, '/wheel-configurator/components/mozzo-damil.webp', 300.00, NOW(), NOW()),
(17, 2, 'Mozzo Erase', 'mozzo', 0, '/wheel-configurator/components/mozzo-erase.jpg', 330.00, NOW(), NOW()),
(18, 2, 'Mozzo Bitex', 'mozzo', 0, '/wheel-configurator/components/mozzo-bitex.png', 250.00, NOW(), NOW()),
(19, 2, 'Mozzo Industry Nine', 'mozzo', 0, '/wheel-configurator/components/mozzo-industrynine.webp', 700.00, NOW(), NOW()),
(20, 2, 'Mozzo Chris King', 'mozzo', 0, '/wheel-configurator/components/mozzo-chrisking.webp', 850.00, NOW(), NOW()),
(21, 2, 'Mozzo OGS', 'mozzo', 0, '/wheel-configurator/components/mozzo-ogs.webp', 350.00, NOW(), NOW()),
(22, 2, 'Mozzo Spank', 'mozzo', 0, '/wheel-configurator/components/mozzo-spank.webp', 370.00, NOW(), NOW()),
(23, 3, 'Mozzo DT Swiss 350', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss350.jpg', 300.00, NOW(), NOW()),
(24, 3, 'Mozzo DT Swiss 240', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss240.jpg', 450.00, NOW(), NOW()),
(25, 3, 'Mozzo DT Swiss 180', 'mozzo', 0, '/wheel-configurator/components/mozzo-dtswiss180.jpg', 700.00, NOW(), NOW()),
(26, 3, 'Mozzo Extralight', 'mozzo', 0, '/wheel-configurator/components/mozzo-extralight.jpeg', 700.00, NOW(), NOW()),
(27, 3, 'Mozzo Damil', 'mozzo', 0, '/wheel-configurator/components/mozzo-damil.webp', 300.00, NOW(), NOW()),
(28, 3, 'Mozzo Erase', 'mozzo', 0, '/wheel-configurator/components/mozzo-erase.jpg', 330.00, NOW(), NOW()),
(29, 3, 'Mozzo Bitex', 'mozzo', 0, '/wheel-configurator/components/mozzo-bitex.png', 250.00, NOW(), NOW()),
(30, 3, 'Mozzo Industry Nine', 'mozzo', 0, '/wheel-configurator/components/mozzo-industrynine.webp', 700.00, NOW(), NOW()),
(31, 3, 'Mozzo Chris King', 'mozzo', 0, '/wheel-configurator/components/mozzo-chrisking.webp', 850.00, NOW(), NOW()),
(32, 3, 'Mozzo OGS', 'mozzo', 0, '/wheel-configurator/components/mozzo-ogs.webp', 350.00, NOW(), NOW()),
(33, 3, 'Mozzo Spank', 'mozzo', 0, '/wheel-configurator/components/mozzo-spank.webp', 370.00, NOW(), NOW());

INSERT INTO wheel_components (id, name, description, price, category, exclusive_group, image_url, created_at, updated_at) VALUES
(1, 'Profilo 20 mm', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()),
(2, 'Profilo 30 mm', NULL, 760.00, 'profilo', 'profile', NULL, NOW(), NOW()),
(3, 'Profilo 45 mm', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()),
(4, 'Profilo 50 mm', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()),
(5, 'Profilo 60 mm', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()),
(6, 'Sapim CX-Ray', NULL, 3.00, 'raggi', 'spoke', NULL, NOW(), NOW()),
(7, 'Sapim Laser', NULL, 1.60, 'raggi', 'spoke', NULL, NOW(), NOW()),
(8, 'Sapim Sprint', NULL, 1.60, 'raggi', 'spoke', NULL, NOW(), NOW()),
(9, 'Sapim Leader', NULL, 1.00, 'raggi', 'spoke', NULL, NOW(), NOW()),
(10, 'Raggi Carbon', NULL, 10.00, 'raggi', 'spoke', '/wheel-configurator/components/raggio-carbon.webp', NOW(), NOW()),
(11, 'Raggi Berd', NULL, 10.00, 'raggi', 'spoke', '/wheel-configurator/components/raggio-berd.png', NOW(), NOW());

SELECT setval(pg_get_serial_sequence('migrations', 'id'), COALESCE(MAX(id), 1), true) FROM migrations;
SELECT setval(pg_get_serial_sequence('wheel_categories', 'id'), COALESCE(MAX(id), 1), true) FROM wheel_categories;
SELECT setval(pg_get_serial_sequence('wheel_hubs', 'id'), COALESCE(MAX(id), 1), true) FROM wheel_hubs;
SELECT setval(pg_get_serial_sequence('wheel_components', 'id'), COALESCE(MAX(id), 1), true) FROM wheel_components;
SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE(MAX(id), 1), true) FROM users;
SELECT setval(pg_get_serial_sequence('configurations', 'id'), COALESCE(MAX(id), 1), true) FROM configurations;
SELECT setval(pg_get_serial_sequence('quotes', 'id'), COALESCE(MAX(id), 1), true) FROM quotes;
SELECT setval(pg_get_serial_sequence('configuration_component', 'id'), COALESCE(MAX(id), 1), true) FROM configuration_component;
SELECT setval(pg_get_serial_sequence('component_images', 'id'), COALESCE(MAX(id), 1), true) FROM component_images;
SELECT setval(pg_get_serial_sequence('personal_access_tokens', 'id'), COALESCE(MAX(id), 1), true) FROM personal_access_tokens;
SELECT setval(pg_get_serial_sequence('jobs', 'id'), COALESCE(MAX(id), 1), true) FROM jobs;
SELECT setval(pg_get_serial_sequence('failed_jobs', 'id'), COALESCE(MAX(id), 1), true) FROM failed_jobs;

COMMIT;

BEGIN;

UPDATE wheel_components
SET name = 'Profilo 45/50mm wave',
    price = 800.00,
    description = NULL,
    image_url = NULL,
    updated_at = NOW()
WHERE name = 'Profilo 50 mm';

UPDATE wheel_components
SET price = 760.00,
    description = NULL,
    image_url = NULL,
    updated_at = NOW()
WHERE name = 'Profilo 30 mm';

UPDATE wheel_components
SET price = 800.00,
    description = NULL,
    image_url = NULL,
    updated_at = NOW()
WHERE name IN ('Profilo 20 mm', 'Profilo 45 mm', 'Profilo 60 mm', 'Profilo 45/50mm wave');

INSERT INTO wheel_components (name, description, price, category, exclusive_group, image_url, created_at, updated_at)
SELECT 'Profilo 40 mm', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM wheel_components WHERE name = 'Profilo 40 mm'
);

INSERT INTO wheel_components (name, description, price, category, exclusive_group, image_url, created_at, updated_at)
SELECT 'Profilo 35/40mm wave', NULL, 800.00, 'profilo', 'profile', NULL, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM wheel_components WHERE name = 'Profilo 35/40mm wave'
);

UPDATE wheel_components
SET name = 'Profilo 35/40mm wave',
    price = 800.00,
    description = NULL,
    image_url = NULL,
    updated_at = NOW()
WHERE name = 'Profilo 35/40mm';

COMMIT;

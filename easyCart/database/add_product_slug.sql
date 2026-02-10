-- Add url_slug column to catalog_product_entity
ALTER TABLE catalog_product_entity
ADD COLUMN IF NOT EXISTS url_slug VARCHAR(255);

-- Create index for faster lookup
CREATE INDEX IF NOT EXISTS idx_product_slug ON catalog_product_entity(url_slug);

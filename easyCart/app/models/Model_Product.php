<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_Product extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_Product';
    }

    /**
     * Business logic for product data formatting
     */
    public function afterLoad()
    {
        $data = $this->getData();
        // Initialize collections
        $data['specs'] = $data['specs'] ?? [];
        $data['variants'] = $data['variants'] ?? [];
        $data['tags'] = $data['tags'] ?? [];

        // 1. Fetch from Resource (Precedence)
        if (isset($data['entity_id'])) {
            $rows = $this->getResource()->getAttributes($data['entity_id']);

            if (!empty($rows)) {
                $dbVariants = [];
                $dbSpecs = [];
                $dbTags = [];

                // Predefined variant types (mostly lowercase in seed data)
                $variantTypes = ['color', 'size', 'storage', 'strap', 'weight', 'format', 'set', 'pack', 'version', 'edition', 'theme', 'character', 'design', 'switch', 'capacity', 'material', 'balls', 'tiers', 'style', 'grip'];

                foreach ($rows as $row) {
                    $type = $row['attribute_type'];
                    $val = $row['attribute_value'];

                    if ($type === 'tag') {
                        $dbTags[] = $val;
                    } elseif (in_array(strtolower($type), $variantTypes)) {
                        $typeKey = strtolower($type);
                        if (!isset($dbVariants[$typeKey])) {
                            $dbVariants[$typeKey] = [];
                        }
                        $dbVariants[$typeKey][] = $val;
                    } else {
                        // Everything else is a spec
                        $dbSpecs[$type] = $val;
                    }
                }

                // If we found DB data, use it
                if (!empty($dbVariants) || !empty($dbSpecs) || !empty($dbTags)) {
                    $data['variants'] = $dbVariants;
                    $data['specs'] = $dbSpecs;
                    $data['tags'] = array_unique($dbTags); // Just in case

                    $this->setData($data);
                    return $this->finalizeData($data);
                }
            }
        }

        // 2. Load static data as fallback
        static $staticProducts = null;
        if ($staticProducts === null) {
            if (file_exists(__DIR__ . '/../../database/original_products.php')) {
                require __DIR__ . '/../../database/original_products.php';
                $staticProducts = $products;
            } else {
                $staticProducts = [];
            }
        }

        // Merge with static data if ID matches
        foreach ($staticProducts as $sp) {
            if ($sp['id'] == ($data['id'] ?? $data['entity_id'] ?? null)) {
                $data['variants'] = $sp['variants'] ?? [];
                $data['specs'] = $sp['specs'] ?? [];
                $data['tags'] = $sp['tags'] ?? [];
                break;
            }
        }

        return $this->finalizeData($data);
    }

    private function finalizeData($data)
    {

        // Map entity_id to id for compatibility
        if (isset($data['entity_id']) && !isset($data['id'])) {
            $data['id'] = (int) $data['entity_id'];
        }

        // Convert numeric fields
        if (isset($data['id']))
            $data['id'] = (int) $data['id'];
        if (isset($data['price']))
            $data['price'] = (float) $data['price'];
        if (isset($data['original_price']))
            $data['original_price'] = (float) $data['original_price'];
        else if (isset($data['price']))
            $data['original_price'] = (float) $data['price'];

        if (isset($data['discount_percent']))
            $data['discount_percent'] = (int) $data['discount_percent'];
        if (isset($data['rating']))
            $data['rating'] = (float) $data['rating'];
        if (isset($data['reviews_count']))
            $data['reviews_count'] = (int) $data['reviews_count'];
        if (isset($data['stock']))
            $data['stock'] = (int) $data['stock'];

        $this->setData($data);
        return $this;
    }

    /**
     * Compatibility methods to maintain current functionality
     */
    public function getById($id)
    {
        require_once 'Collection_Product.php';
        $collection = new Collection_Product();
        $collection->addFieldToFilter('entity_id', $id);
        $items = $collection->getItems();
        if (!empty($items)) {
            return $items[0]->getData();
        }
        return null;
    }

    public function getBySlug($slug)
    {
        require_once 'Collection_Product.php';
        $collection = new Collection_Product();
        $collection->addFieldToFilter('url_slug', $slug);
        $items = $collection->getItems();
        if (!empty($items)) {
            $this->setData($items[0]->getData());
            return $this->getData();
        }
        return null;
    }

    public function getAll()
    {
        require_once 'Collection_Product.php';
        return (new Collection_Product())->getData();
    }

    public function getByCategory($categoryId)
    {
        require_once 'Collection_Product.php';
        $field = is_numeric($categoryId) ? 'category_id' : 'category';
        return (new Collection_Product())
            ->addFieldToFilter($field, $categoryId)
            ->getData();
    }

    public function getByBrand($brandSlug)
    {
        require_once 'Collection_Product.php';
        $field = is_numeric($brandSlug) ? 'brand_id' : 'brand';
        return (new Collection_Product())
            ->addFieldToFilter($field, $brandSlug)
            ->getData();
    }

    public function getFeatured()
    {
        require_once 'Collection_Product.php';
        return (new Collection_Product())
            ->addFieldToFilter('is_featured', true)
            ->setPageSize(8)
            ->getData();
    }

    public function search($query)
    {
        require_once 'Collection_Product.php';
        $collection = new Collection_Product();
        // search in name or description
        // For simplicity in this collection, we use a custom filter or just add multiple
        // Let's assume search query is passed correctly to addFieldToFilter
        return (new Collection_Product())
            ->addFieldToFilter('pe.name', '%' . $query . '%', 'LIKE')
            ->getData();
    }

    public function getVariants($productId)
    {
        return [];
    }

    public function createProduct($data)
    {
        $this->setData($data)->save();
        return $this->getId();
    }

    public function updateProduct($id, $data)
    {
        $this->load($id)->setData($data)->save();
        return true;
    }

    /**
     * Get related images
     */
    public function getImages()
    {
        $id = $this->getId();
        if (!$id)
            return [];

        return $this->getResource()->getImages($id);
    }
}

---
description: Refactor Model Layer into Model, Resource, and Collection (Magento-style pattern)
---
1. **Create Base Framework Classes** in `app/core/`:
   - `Core_Model.php`: Base entity class. Handles data storage (`$data` array) and delegates saving/loading to a resource.
   - `Core_Resource.php`: Base resource class. Contains standard SQL for `load`, `save`, and `delete` using the table name and primary key defined in child classes.
   - `Core_Collection.php`: Base collection class. Provides methods like `addFieldToFilter`, `setOrder`, and `load` for multi-record operations.

2. **Categorize Existing Models**:
   - For each existing model (e.g., `Product`), create:
     - `app/models/Model_Product.php` (Entity: holds current item data and business logic).
     - `app/models/Resource_Product.php` (Resource: defines table `catalog_product_entity` and PK `entity_id`).
     - `app/models/Collection_Product.php` (Collection: handles complex listing queries with joins).

3. **Migrate Business Logic**:
   - Move SQL execution for single records to Resource classes.
   - Move SQL for lists/collections to Collection classes.
   - Keep "Business Rules" (price calculations, validations) in the Model Entity.

4. **Update Controller Layer**:
   - Refactor controllers to use the new pattern:
     - To fetch a list: `$collection = new Collection_Product(); $products = $collection->addAttributeToSelect('*')->load();`
     - To load a single item: `$product = new Model_Product(); $product->load($id);`
     - To save: `$product->setData($postData)->save();`

5. **Specific Refactorings**:
   - **Cart**: Ensure `Model_Cart` correctly uses its Resource/Collection to manage session vs DB persistence.
   - **Order**: Split the complex order placement logic into `Resource_Order` (saving the header) and `Resource_Order_Item` (saving line items).
   - **Product**: Ensure `Collection_Product` handles the current filtering logic (category, brand, price) using standard `addFieldToFilter` methods.

// turbo-all

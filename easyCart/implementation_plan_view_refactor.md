---
description: Refactor View Layer to use View Classes with toHtml
---
1. **Create Base View Class**:
   - Create `app/core/View.php` as an abstract class.
   - Define abstract `toHtml()` method.
   - Implement `renderTemplate($template, $data)` method to handle template rendering and output buffering.

2. **Move Templates**:
   - Rename/Move `app/views` directory to `app/templates`.
   - Create a new `app/views` directory to hold the View Classes.

3. **Create Specific View Classes**:
   - Create a View Class for each major page in `app/views/`, extending `View`.
   - Each class implements `toHtml()` method.
   - Example: `app/views/View_Home.php` (class `View_Home`), `app/views/View_Product_List.php` (class `View_Product_List`), etc.
   - `toHtml()` will typically call `renderTemplate('layouts/header')`, `renderTemplate('page/content')`, `renderTemplate('layouts/footer')`.

4. **Update Controllers**:
   - Modify each Controller to instantiate the appropriate `View_` class and call `toHtml()` instead of `$this->view()`.
   - Example: `$view = new View_Home($data); echo $view->toHtml();`

5. **Verify**:
   - Check all pages to ensure formatting and data remain correct.
   - Ensure "HTML only" principle is respected (templates contain minimal PHP logic, mainly for display).
// turbo-all

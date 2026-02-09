<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-2">
    <div class="mb-2rem">
        <a href="<?php echo BASE_URL; ?>/admin" class="text-secondary">&larr; Back to Dashboard</a>
        <h1 class="auth-title mt-1">Import / Export Products</h1>
    </div>

    <div class="grid-2 gap-2rem">
        <!-- IMPORT SECTION -->
        <div class="card p-2rem">
            <h2 class="section-title mb-1">Import Products (CSV)</h2>
            <p class="text-secondary mb-1-5">Upload a CSV file to bulk import or update products. Existing SKUs will be
                updated.</p>

            <form action="<?php echo BASE_URL; ?>/admin/processImport" method="POST" enctype="multipart/form-data">
                <div class="form-group mb-1-5">
                    <label for="csv_file" class="form-label">Select CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-input" accept=".csv" required>
                </div>

                <div class="alert alert-info mb-1-5">
                    <strong>Expected Columns:</strong><br>
                    <small>sku, name, brand, price, stock, description, category, image_url, discount_percent</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">Upload & Import</button>
            </form>
        </div>

        <!-- EXPORT SECTION -->
        <div class="card p-2rem">
            <h2 class="section-title mb-1">Export Products</h2>
            <p class="text-secondary mb-1-5">Download a CSV file containing all current products in the database.</p>

            <div class="stat-card mb-1-5" style="border: 1px dashed var(--border-color); box-shadow: none;">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <h3>CSV</h3>
                    <p>Format matches import structure</p>
                </div>
            </div>

            <a href="<?php echo BASE_URL; ?>/admin/exportProducts" class="btn btn-secondary w-100 text-center">Export to
                CSV</a>
        </div>
    </div>
</div>

<style>
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
    }

    .alert-info {
        background: rgba(var(--primary-rgb), 0.1);
        color: var(--primary);
        padding: 1rem;
        border-radius: 0.5rem;
        font-size: 0.9rem;
    }

    .form-input[type="file"] {
        padding: 0.75rem;
        border: 1px dashed var(--border-color);
        background: var(--bg-secondary);
    }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
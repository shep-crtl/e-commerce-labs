<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$productController = new ProductController();

$editProduct = null;
$isEdit = false;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $found = $productController->getProductById($editId);
        if ($found) {
            $editProduct = $found;
            $isEdit = true;
        }
    }
}

$categories = $productController->getAllCategories();
$brands = $productController->getAllBrands();
$products = $productController->getAllProducts();

include __DIR__ . '/../layout/header.php';

?>

<section class="admin-section">

    <h2>Product Management</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success">
            <?= htmlspecialchars($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>
        </p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error">
            <?= htmlspecialchars($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        </p>
    <?php endif; ?>

    <div class="form-container">
        <h3><?= $isEdit ? 'Edit Product' : 'Add New Product' ?></h3>

        <form
            id="product-form"
            action="<?= BASE_URL ?>/actions/<?= $isEdit ? 'update_product_action.php' : 'add_product_action.php' ?>"
            method="POST"
            enctype="multipart/form-data"
        >
            <?php if ($isEdit): ?>
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($editProduct['product_id']) ?>">
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProduct['product_image']) ?>">
                <input type="hidden" id="is_edit" value="1">
            <?php endif; ?>

            <div class="form-group">
                <label for="product_title">Product Title</label>
                <input
                    type="text"
                    name="product_title"
                    id="product_title"
                    value="<?= $isEdit ? htmlspecialchars($editProduct['product_title']) : '' ?>"
                    maxlength="200"
                    required
                >
                <span id="product_title-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_price">Price ($ / GHS)</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="product_price"
                    id="product_price"
                    value="<?= $isEdit ? htmlspecialchars($editProduct['product_price']) : '' ?>"
                    required
                >
                <span id="product_price-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_cat">Category</label>
                <select name="product_cat" id="product_cat" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= $cat['cat_id'] ?>"
                            <?= ($isEdit && $editProduct['product_cat'] == $cat['cat_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cat['cat_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="product_cat-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_brand">Brand</label>
                <select name="product_brand" id="product_brand" required>
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option
                            value="<?= $brand['brand_id'] ?>"
                            <?= ($isEdit && $editProduct['product_brand'] == $brand['brand_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="product_brand-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_desc">Description</label>
                <textarea
                    name="product_desc"
                    id="product_desc"
                    rows="4"
                    maxlength="500"
                    style="width: 100%; max-width: 500px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                ><?= $isEdit ? htmlspecialchars($editProduct['product_desc']) : '' ?></textarea>
                <span id="product_desc-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_keywords">Keywords (comma separated)</label>
                <input
                    type="text"
                    name="product_keywords"
                    id="product_keywords"
                    value="<?= $isEdit ? htmlspecialchars($editProduct['product_keywords']) : '' ?>"
                    maxlength="100"
                >
                <span id="product_keywords-error" class="field-error"></span>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image <?= $isEdit ? '(Leave empty to keep current image)' : '' ?></label>
                <input
                    type="file"
                    name="product_image"
                    id="product_image"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    <?= $isEdit ? '' : 'required' ?>
                >
                <span id="product_image-error" class="field-error"></span>

                <?php if ($isEdit && !empty($editProduct['product_image'])): ?>
                    <div style="margin-top: 10px;">
                        <p style="margin-bottom: 5px; font-weight: bold;">Current Image:</p>
                        <img
                            src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($editProduct['product_image']) ?>"
                            alt="<?= htmlspecialchars($editProduct['product_title']) ?>"
                            style="max-width: 120px; border: 1px solid #ccc; padding: 3px; border-radius: 4px;"
                        >
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit">
                <?= $isEdit ? 'Update Product' : 'Add Product' ?>
            </button>

            <?php if ($isEdit): ?>
                <a href="<?= BASE_URL ?>/views/admin/product.php" class="btn-cancel">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <hr style="margin: 30px 0;">

    <h3>Existing Products</h3>

    <?php if (empty($products)): ?>
        <p>No products found. Add one above.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['product_id']) ?></td>
                        <td>
                            <?php if (!empty($prod['product_image'])): ?>
                                <img
                                    src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($prod['product_image']) ?>"
                                    alt="<?= htmlspecialchars($prod['product_title']) ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                >
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($prod['product_title']) ?></td>
                        <td><?= htmlspecialchars($prod['cat_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($prod['brand_name'] ?? 'N/A') ?></td>
                        <td>$<?= number_format($prod['product_price'], 2) ?></td>
                        <td>
                            <a
                                href="<?= BASE_URL ?>/views/admin/product.php?edit_id=<?= $prod['product_id'] ?>"
                                class="btn-edit"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</section>

<script src="<?= BASE_URL ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

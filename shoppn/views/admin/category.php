<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$productController = new ProductController();

$editCategory = null;
$isEdit = false;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $found = $productController->getCategoryById($editId);
        if ($found) {
            $editCategory = $found;
            $isEdit = true;
        }
    }
}

$categories = $productController->getAllCategories();

include __DIR__ . '/../layout/header.php';

?>

<section class="admin-section">

    <h2>Category Management</h2>

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
        <h3><?= $isEdit ? 'Edit Category' : 'Add Category' ?></h3>

        <form
            action="<?= BASE_URL ?>/actions/<?= $isEdit ? 'update_category_action.php' : 'add_category_action.php' ?>"
            method="POST"
        >
            <?php if ($isEdit): ?>
                <input type="hidden" name="cat_id" value="<?= htmlspecialchars($editCategory['cat_id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="cat_name">Category Name</label>
                <input
                    type="text"
                    name="cat_name"
                    id="cat_name"
                    value="<?= $isEdit ? htmlspecialchars($editCategory['cat_name']) : '' ?>"
                    maxlength="100"
                    required
                >
                <span id="category-name-error" class="field-error"></span>
            </div>

            <button type="submit">
                <?= $isEdit ? 'Update Category' : 'Add Category' ?>
            </button>

            <?php if ($isEdit): ?>
                <a href="<?= BASE_URL ?>/views/admin/category.php" class="btn-cancel">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <hr style="margin: 30px 0;">

    <h3>Existing Categories</h3>

    <?php if (empty($categories)): ?>
        <p>No categories found. Add one above.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['cat_id']) ?></td>
                        <td><?= htmlspecialchars($cat['cat_name']) ?></td>
                        <td>
                            <a
                                href="<?= BASE_URL ?>/views/admin/category.php?edit_id=<?= $cat['cat_id'] ?>"
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

<?php include __DIR__ . '/../layout/footer.php'; ?>

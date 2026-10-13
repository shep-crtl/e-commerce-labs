<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$productController = new ProductController();

$editBrand = null;
$isEdit = false;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $found = $productController->getBrandById($editId);
        if ($found) {
            $editBrand = $found;
            $isEdit = true;
        }
    }
}

$brands = $productController->getAllBrands();

include __DIR__ . '/../layout/header.php';

?>

<section class="admin-section">

    <h2>Brand Management</h2>

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
        <h3><?= $isEdit ? 'Edit Brand' : 'Add Brand' ?></h3>

        <form
            action="<?= BASE_URL ?>/actions/<?= $isEdit ? 'update_brand_action.php' : 'add_brand_action.php' ?>"
            method="POST"
        >
            <?php if ($isEdit): ?>
                <input type="hidden" name="brand_id" value="<?= htmlspecialchars($editBrand['brand_id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="brand_name">Brand Name</label>
                <input
                    type="text"
                    name="brand_name"
                    id="brand_name"
                    value="<?= $isEdit ? htmlspecialchars($editBrand['brand_name']) : '' ?>"
                    maxlength="100"
                    required
                >
                <span id="brand-name-error" class="field-error"></span>
            </div>

            <button type="submit">
                <?= $isEdit ? 'Update Brand' : 'Add Brand' ?>
            </button>

            <?php if ($isEdit): ?>
                <a href="<?= BASE_URL ?>/views/admin/brand.php" class="btn-cancel">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <hr style="margin: 30px 0;">

    <h3>Existing Brands</h3>

    <?php if (empty($brands)): ?>
        <p>No brands found. Add one above.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Brand ID</th>
                    <th>Brand Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?= htmlspecialchars($brand['brand_id']) ?></td>
                        <td><?= htmlspecialchars($brand['brand_name']) ?></td>
                        <td>
                            <a
                                href="<?= BASE_URL ?>/views/admin/brand.php?edit_id=<?= $brand['brand_id'] ?>"
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

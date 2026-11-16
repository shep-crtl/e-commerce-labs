<?php

/*
 * views/payment_failed.php (Task 13)
 *
 * Displays friendly error message when payment verification fails or is cancelled.
 */

require_once __DIR__ . '/../core/core.php';

$reason = $_GET['reason'] ?? '';
$errorMessage = $_SESSION['error'] ?? 'Your payment could not be completed or was cancelled.';

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="payment-failed-section" style="flex: 1; padding: 24px 28px; min-width: 0;">

        <h2>Payment Unsuccessful</h2>

        <div style="background: #fff; border: 1px solid #f5c2c7; border-radius: 6px; padding: 30px; max-width: 600px; margin: 20px auto; text-align: center;">

            <div style="font-size: 3rem; color: #dc3545; margin-bottom: 15px;">
                &#9888;
            </div>

            <h3 style="color: #842029; margin-top: 0;">Transaction Could Not Be Completed</h3>

            <p style="color: #555; line-height: 1.6; margin: 15px 0;">
                <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p style="color: #777; font-size: 0.9rem;">
                Your cart has been preserved. If Paystack confirmed a charge but no invoice appeared, contact the site administrator with the transaction reference before trying again.
            </p>

            <div style="margin-top: 25px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?= BASE_URL ?>/views/payment.php" class="btn-cart-update" style="text-decoration: none; padding: 10px 20px;">
                    Try Payment Again
                </a>

                <a href="<?= BASE_URL ?>/views/cart.php" class="btn-cart-continue" style="text-decoration: none; padding: 10px 20px;">
                    Return to Cart
                </a>
            </div>

        </div>

    </section>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>

<?php
function display_error() {
    if (isset($_SESSION['error'])) { ?>
        <p class="text-warning">
            <?php echo $_SESSION['error_description']; ?>
        </p>
        <?php unset($_SESSION['error']);
    }
}
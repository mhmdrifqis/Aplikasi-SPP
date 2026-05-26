<?php
/**
 * Footer Halaman Global
 * Aplikasi SPP
 * 
 * Menutup pembungkus layout halaman dan memuat pustaka Javascript (Bootstrap, Main JS).
 */
$base_url = "/Aplikasi SPP";
?>
<?php if (isset($_SESSION['login']) && !strpos($_SERVER['PHP_SELF'], 'login.php')): ?>
            <!-- Menutup div pembungkus halaman -->
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Kustom JavaScript aplikasi -->
<script src="<?= $base_url ?>/assets/js/main.js"></script>
</body>
</html>
<?php
// Mengirimkan buffer output ke browser
ob_end_flush();
?>

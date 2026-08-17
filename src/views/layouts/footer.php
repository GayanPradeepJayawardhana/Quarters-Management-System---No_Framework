<?php
/**
 * Footer layout for all pages
 */
require_once __DIR__ . '/../../../config/config.php';
?>
    <!-- Footer -->
    <footer class="bg-[#5c060d] text-white/60 text-center py-4 mt-8 border-t border-amber-400/20">
        <p class="text-sm">&copy; <?php echo date('Y'); ?> Department of Railways - Quarter Management System. All rights reserved.</p>
    </footer>

    <!-- Custom JavaScript -->
    <script src="<?php echo assetUrl('js/main.js'); ?>"></script>
</body>
</html>
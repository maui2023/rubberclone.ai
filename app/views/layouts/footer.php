<?php
// app/views/layouts/footer.php
?>
            </div> <!-- /page-content-wrapper -->
        </main> <!-- /main-content -->
    </div> <!-- /admin-wrapper -->

    <!-- LeafletJS Map Library (for geopins plot) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- ChartJS Library (for dashboards metrics) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Admin Portal Script -->
    <script src="/assets/js/admin.js?v=<?php echo filemtime(__DIR__ . '/../../public/assets/js/admin.js'); ?>"></script>
</body>
</html>

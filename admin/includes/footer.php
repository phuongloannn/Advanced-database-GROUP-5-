        </main>
    </div>
</div>

<!-- Scripts -->
<script src="../../assets/js/jquery-3.6.0.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active sidebar item
    $(document).ready(function() {
        var currentLocation = window.location.pathname;
        $('.nav-link').each(function() {
            var link = $(this).attr('href');
            if (currentLocation.indexOf(link) !== -1) {
                $(this).addClass('active');
            }
        });
    });
</script>
</body>
</html> 
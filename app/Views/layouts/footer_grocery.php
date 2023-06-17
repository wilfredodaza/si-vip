<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
    <script>localStorage.setItem('url', '<?= base_url() ?>')</script>
    <script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/search.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/chart.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
    <script src="<?= base_url() ?>/grocery-crud/js/libraries/ckeditor/ckeditor.adapter-jquery.js"></script>
    <script src="<?= base_url() ?>/grocery-crud/js/libraries/jquery-ui.js"></script>
    <script src="<?= base_url() ?>/grocery-crud/js/build/grocery-crud-v2.8.1.0659b25.js"></script>
    <script src="<?= base_url() ?>/grocery-crud/js/build/load-grocery-crud.js"></script>

<script>
    $(document).ready(function(){
        $('.notification-active').click(function(){
            var URLactual = window.location;
            var id = $(this).data('id');
            $(this).hide();
            fetch(URLactual.origin + '/notification/view/' + id)
                .then(function (response) {
                    return response.json();
                })
                .then(function (myJson) {
                    var dates = myJson;
                   location.href =  URLactual.origin+'/notification/index?nota='+ id;
                });
        });
    });
</script>
</body>
</html>



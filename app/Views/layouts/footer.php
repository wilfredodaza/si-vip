<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
    <script>localStorage.setItem('url', '<?= base_url() ?>')</script>
    <script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/jquery.formatter.min.js" ></script>
    <script src="<?= base_url() ?>/assets/js/jquery.validate.js"></script>
    <script src="https://unpkg.com/materialize-stepper@3.1.0/dist/js/mstepper.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/shepherd.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/search.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/chart.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
    <script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
    <script src="<?= base_url() ?>/assets/js/additional-methods.js"></script>
    <script src="<?= base_url() ?>/assets/js/form-wizard.js"></script>
    <script src="<?= base_url() ?>/assets/js/dropzone.js"></script>
    <script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script id = "sbinit" src = "https://mischats.com/supportboard/js/main.js?lang=es&mode=3" > </script>

<script src="<?= base_url() ?>/assets/js/sweetalert.min.js"></script>
    
<script src="<?= base_url() ?>/assets/js/loading-bar.min.js"></script>
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


<?= view('layouts/header_angular') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
    <?= '<script>window.localStorage.setItem("id", "'.$id.'")</script>'?>
   <div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <app-root></app-root>
            </div>
        </div>
    </div>
</div>






    <script src="<?= base_url() ?>/assets/js/note-credit/runtime-es2015.c5fa8325f89fc516600b.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/runtime-es5.c5fa8325f89fc516600b.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/polyfills-es5.3e8196928d184a6e5319.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/polyfills-es2015.5b10b8fd823b6392f1fd.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/scripts.b2e39411c6c47faf5b13.js" defer></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/main-es2015.65d09c2a90cc123dcc21.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-credit/main-es5.65d09c2a90cc123dcc21.js" nomodule defer></script>






<?= view('layouts/footer_angular') ?>
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

  
    <script src="<?= base_url() ?>/assets/js/note-debit/runtime-es2015.c5fa8325f89fc516600b.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/runtime-es5.c5fa8325f89fc516600b.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/polyfills-es5.3e8196928d184a6e5319.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/polyfills-es2015.5b10b8fd823b6392f1fd.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/scripts.b2e39411c6c47faf5b13.js" defer></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/main-es2015.131dfc552526d48ab4de.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/note-debit/main-es5.131dfc552526d48ab4de.js" nomodule defer></script>



<?= view('layouts/footer_angular') ?>
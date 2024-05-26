<?php 
session_start();
require_once('classes/actions.class.php');
$actionClass = new Actions();
$page = $_GET['page'] ?? "home";
$page_title = ucwords(str_replace("_", " ", $page));
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once('inc/header.php'); ?>
<body>
<?php include_once('inc/navigationDeckblat.php'); ?>
    <div class="container-md py-3">
        <?php if(isset($_SESSION['flashdata']) && !empty($_SESSION['flashdata'])): ?>
            <div class="flashdata flashdata-<?= $_SESSION['flashdata']['type'] ?? 'default' ?> mb-3">
                <div class="d-flex w-100 align-items-center flex-wrap">
                    <div class="col-11"><?= $_SESSION['flashdata']['msg'] ?? '' ?></div>
                    <div class="col-1 text-center">
                        <a href="javascript:void(0)" onclick="this.closest('.flashdata').remove()" class="flashdata-close"><i class="far fa-times-circle"></i></a>
                    </div>
                </div>
            </div>
        <?php unset($_SESSION['flashdata']); ?>
        <?php endif; ?>
        <div class="main-wrapper">
            <?php 
            $page_path = "pages/{$page}.php";
            if (file_exists($page_path)) {
                include_once($page_path);
            } else {
                echo "<p>Page not found. Please check the URL and try again.</p>";
            }
            ?>
        </div>
    </div>
    <?php include_once('inc/footer.php'); ?>
</body>
</html>

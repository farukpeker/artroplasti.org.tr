<?php
/**
 * Template Name: Giriş Yap
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/hesabim'));
    exit;
}

get_header();
?>

<main id="main-content" class="site-main login-page">
    <div class="container">
        <div class="login-form-wrapper">
            <h1>Giriş Yap</h1>
            
            <?php
            // Show any error messages
            if (isset($_GET['login']) && $_GET['login'] == 'failed') {
                echo '<div class="error-message">Kullanıcı adı veya şifre hatalı!</div>';
            }
            
            if (isset($_GET['login']) && $_GET['login'] == 'empty') {
                echo '<div class="error-message">Lütfen kullanıcı adı ve şifrenizi giriniz!</div>';
            }
            ?>

            <form id="login-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                <div class="form-group">
                    <label for="user_login">Kullanıcı Adı veya E-posta</label>
                    <input type="text" name="log" id="user_login" class="input" required>
                </div>

                <div class="form-group">
                    <label for="user_pass">Şifre</label>
                    <input type="password" name="pwd" id="user_pass" class="input" required>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="rememberme" value="forever"> Beni Hatırla
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit">Giriş Yap</button>
                </div>

                <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/hesabim')); ?>">
                <?php wp_nonce_field('login', 'login_nonce'); ?>
            </form>

            <div class="login-links">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">Şifremi Unuttum</a>
                <a href="<?php echo esc_url(home_url('/uyelik')); ?>">Üye Ol</a>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();

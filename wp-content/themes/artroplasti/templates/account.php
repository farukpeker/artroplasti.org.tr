<?php
/**
 * Template Name: Hesabım
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/giris-yap'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

get_header();
?>

<main id="main-content" class="site-main account-page">
    <div class="container">
        <h1>Hesabım</h1>
        
        <div class="account-wrapper">
            <aside class="account-sidebar">
                <nav class="account-nav">
                    <ul>
                        <li><a href="#profile" class="active">Profil Bilgileri</a></li>
                        <li><a href="#membership">Üyelik Bilgileri</a></li>
                        <li><a href="#payments">Ödeme Geçmişi</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>">Çıkış Yap</a></li>
                    </ul>
                </nav>
            </aside>

            <div class="account-content">
                <!-- Profile Section -->
                <section id="profile" class="account-section active">
                    <h2>Profil Bilgileri</h2>
                    <div class="user-info">
                        <p><strong>Ad Soyad:</strong> <?php echo esc_html($current_user->display_name); ?></p>
                        <p><strong>E-posta:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                        <p><strong>Kullanıcı Adı:</strong> <?php echo esc_html($current_user->user_login); ?></p>
                        <p><strong>Kayıt Tarihi:</strong> <?php echo date('d.m.Y', strtotime($current_user->user_registered)); ?></p>
                    </div>
                    <a href="<?php echo esc_url(get_edit_profile_url($user_id)); ?>" class="btn-edit">Profili Düzenle</a>
                </section>

                <!-- Membership Section -->
                <section id="membership" class="account-section">
                    <h2>Üyelik Bilgileri</h2>
                    <?php
                    $membership_type = artroplasti_get_membership_type($user_id);
                    $membership_status = get_user_meta($user_id, 'membership_status', true);
                    ?>
                    <div class="membership-info">
                        <p><strong>Üyelik Tipi:</strong> 
                            <?php echo $membership_type === 'premium' ? 'Premium' : 'Standart'; ?>
                        </p>
                        <p><strong>Üyelik Durumu:</strong> 
                            <span class="status-badge status-<?php echo esc_attr($membership_status); ?>">
                                <?php 
                                switch($membership_status) {
                                    case 'active':
                                        echo 'Aktif';
                                        break;
                                    case 'inactive':
                                        echo 'Pasif';
                                        break;
                                    case 'pending':
                                        echo 'Beklemede';
                                        break;
                                    default:
                                        echo 'Belirsiz';
                                }
                                ?>
                            </span>
                        </p>
                    </div>
                </section>

                <!-- Payments Section -->
                <section id="payments" class="account-section">
                    <h2>Ödeme Geçmişi</h2>
                    <?php
                    $payments = artroplasti_get_user_payments($user_id);
                    
                    if ($payments) :
                    ?>
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Durum</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment) : ?>
                                <tr>
                                    <td><?php echo date('d.m.Y H:i', strtotime($payment->payment_date)); ?></td>
                                    <td><?php echo number_format($payment->amount, 2, ',', '.'); ?> TL</td>
                                    <td><?php echo esc_html($payment->payment_method); ?></td>
                                    <td>
                                        <span class="payment-status status-<?php echo esc_attr($payment->payment_status); ?>">
                                            <?php 
                                            switch($payment->payment_status) {
                                                case 'completed':
                                                    echo 'Tamamlandı';
                                                    break;
                                                case 'pending':
                                                    echo 'Beklemede';
                                                    break;
                                                case 'failed':
                                                    echo 'Başarısız';
                                                    break;
                                                default:
                                                    echo esc_html($payment->payment_status);
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html($payment->description); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p class="no-payments">Henüz ödeme kaydınız bulunmamaktadır.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>

<script>
jQuery(document).ready(function($) {
    // Tab navigation
    $('.account-nav a').on('click', function(e) {
        if ($(this).attr('href').startsWith('#')) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.account-nav a').removeClass('active');
            $(this).addClass('active');
            
            $('.account-section').removeClass('active');
            $(target).addClass('active');
        }
    });
});
</script>

<?php
get_footer();

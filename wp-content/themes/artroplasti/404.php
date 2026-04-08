<?php
/**
 * 404 Page Template
 * Sayfa bulunamadı hatası için özel tasarım
 */

// Etkinlik takvimi sayfası query string'leri ile çalışıyor - 404 gösterme
if ( strpos( $_SERVER['REQUEST_URI'], 'etkinlik-takvimi' ) !== false && 
     ( isset( $_GET['month'] ) || isset( $_GET['year'] ) ) ) {
    // Bu 404 değil, etkinlik takvimi sayfası
    include( get_template_directory() . '/template-calendar.php' );
    exit;
}

get_header();
?>

<!-- breadcrumb start -->
<div class="contact-main-wrapper">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="sb-contact-section">
               <nav aria-label="breadcrumb">
                  <h4><?php echo esc_html__('404 - Sayfa Bulunamadı', 'artroplasti'); ?></h4>
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('Anasayfa', 'artroplasti'); ?></a></li>
                     <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html__('404', 'artroplasti'); ?></li>
                  </ol>
               </nav>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- breadcrumb end -->

<!-- 404 section start -->
<div class="error-404-section">
   <div class="container">
      <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="error-404-wrapper" style="text-align: center; ">
               
               <!-- 404 Illustration -->
               <div class="error-404-image" style="margin-bottom: 40px;">
                  <svg width="300" height="200" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width: 100%; height: auto;">
                     <text x="150" y="120" font-family="Arial, sans-serif" font-size="100" font-weight="bold" fill="#e0e0e0" text-anchor="middle">404</text>
                     <circle cx="80" cy="80" r="15" fill="#ff6b6b" opacity="0.3">
                        <animate attributeName="r" values="15;20;15" dur="2s" repeatCount="indefinite"/>
                     </circle>
                     <circle cx="220" cy="80" r="12" fill="#4ecdc4" opacity="0.3">
                        <animate attributeName="r" values="12;17;12" dur="2.5s" repeatCount="indefinite"/>
                     </circle>
                     <circle cx="150" cy="160" r="10" fill="#95e1d3" opacity="0.3">
                        <animate attributeName="r" values="10;15;10" dur="3s" repeatCount="indefinite"/>
                     </circle>
                  </svg>
               </div>

               <!-- Error Title -->
               <h1 style="font-size: 36px; color: #333; margin-bottom: 20px; font-weight: 700;">
                  <?php echo esc_html__('Üzgünüz, Sayfa Bulunamadı!', 'artroplasti'); ?>
               </h1>

               <!-- Error Description -->
               <p style="font-size: 18px; color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                  <?php echo esc_html__('Aradığınız sayfa kaldırılmış, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.', 'artroplasti'); ?>
               </p>

               <!-- Action Buttons -->
               <div class="error-404-actions" style="margin-bottom: 50px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                  <a href="<?php echo esc_url(home_url('/')); ?>" class="button-btn">
                     <i class="fas fa-home" style="margin-right: 8px;"></i>
                     <?php echo esc_html__('Ana Sayfaya Dön', 'artroplasti'); ?>
                     <span><i class="fas fa-angle-right"></i></span>
                  </a>
                  
                  <button onclick="window.history.back()" class="button-btn" style="background: #6c757d; border-color: #6c757d;">
                     <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                     <?php echo esc_html__('Geri Dön', 'artroplasti'); ?>
                  </button>
               </div>

               <!-- Search Form -->
               <div class="error-404-search" style="max-width: 600px; margin: 0 auto;">
                  <h3 style="font-size: 20px; color: #333; margin-bottom: 20px; font-weight: 600;">
                     <?php echo esc_html__('Belki Aramak İstersiniz?', 'artroplasti'); ?>
                  </h3>
                  <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 10px; justify-content: center;">
                     <div style="flex: 1; max-width: 500px;">
                        <input 
                           type="search" 
                           class="form-control" 
                           placeholder="<?php echo esc_attr__('Arama yapın...', 'artroplasti'); ?>" 
                           value="<?php echo get_search_query(); ?>" 
                           name="s" 
                           style="height: 50px; padding: 12px 20px; border: 2px solid #ddd; border-radius: 25px; font-size: 16px; width: 100%;"
                        />
                     </div>
                     <button type="submit" class="button-btn" style="border-radius: 25px; padding: 12px 30px;">
                        <i class="fas fa-search"></i>
                     </button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- 404 section end -->

<style>
/* 404 Page Additional Styles */
.error-404-wrapper .button-btn {
   display: inline-flex;
   align-items: center;
   gap: 8px;
}

.error-404-wrapper .button-btn:hover {
   transform: translateY(-2px);
   box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.error-404-search input:focus {
   border-color: #007bff;
   outline: none;
   box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.quick-links-grid a:hover {
   background-color: #007bff;
   color: white !important;
   border-color: #007bff;
   transform: translateY(-2px);
}

@media (max-width: 768px) {
   .error-404-wrapper h1 {
      font-size: 28px !important;
   }
   
   .error-404-wrapper p {
      font-size: 16px !important;
   }
   
   .error-404-actions {
      flex-direction: column;
      align-items: stretch;
   }
   
   .error-404-actions a,
   .error-404-actions button {
      width: 100%;
      justify-content: center;
   }
   
   .error-404-search form {
      flex-direction: column;
   }
}
</style>

<?php get_footer(); ?>

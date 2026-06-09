<?php
/**
 * Creates or updates Privacy Policy and Terms & Conditions pages
 * in both Turkish and English, linking them via Polylang.
 *
 * Also sets the WordPress privacy-policy page option.
 *
 * Usage:
 *   php tools/create_legal_pages.php --dry-run
 *   php tools/create_legal_pages.php
 */

if (php_sapi_name() !== 'cli') {
    echo "CLI only.\n";
    exit(1);
}

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "ERROR: Polylang functions are not available.\n";
    exit(1);
}

$langs = pll_languages_list(['fields' => 'slug']);
if (!in_array('tr', $langs, true) || !in_array('en', $langs, true)) {
    echo "ERROR: Polylang must have both tr and en languages.\n";
    exit(1);
}

// ─── Page Definitions ─────────────────────────────────────────────────────────

$pages = [
    'privacy-policy' => [
        'tr' => [
            'title'  => 'Gizlilik Politikası',
            'slug'   => 'gizlilik-politikasi',
            'content' => <<<HTML
<h2>Gizlilik Politikası</h2>
<p>Son güncelleme: Haziran 2026</p>

<p>Türk Artroplasti Derneği ("Dernek", "biz", "bizim") olarak, web sitemizi ziyaret eden kullanıcıların gizliliğine saygı duyuyor ve kişisel verilerinizin korunmasını öncelik olarak benimsiyoruz. Bu Gizlilik Politikası, <strong>artroplasti.org.tr</strong> adresindeki web sitemizi kullandığınızda hangi verileri topladığımızı, bu verileri nasıl kullandığımızı ve haklarınızın neler olduğunu açıklamaktadır.</p>

<h3>1. Toplanan Veriler</h3>
<p>Web sitemizi ziyaret ettiğinizde veya formlarımızı doldurduğunuzda aşağıdaki bilgileri toplayabiliriz:</p>
<ul>
  <li>Ad, soyad ve unvan</li>
  <li>E-posta adresi ve telefon numarası</li>
  <li>Tıbbi uzmanlık alanı ve kurum bilgisi (üyelik formlarında)</li>
  <li>IP adresi, tarayıcı türü ve ziyaret süresi gibi teknik veriler</li>
</ul>

<h3>2. Verilerin Kullanım Amacı</h3>
<p>Toplanan veriler yalnızca aşağıdaki amaçlarla kullanılmaktadır:</p>
<ul>
  <li>Üyelik başvurularını değerlendirmek ve üyelerimizle iletişim kurmak</li>
  <li>Kongre, kurs ve etkinlik duyurularını iletmek</li>
  <li>E-bülten hizmetini sağlamak (abonelik onayı gerektirir)</li>
  <li>Web sitesi kullanımını analiz ederek hizmeti geliştirmek</li>
  <li>Yasal yükümlülükleri yerine getirmek</li>
</ul>

<h3>3. Verilerin Paylaşımı</h3>
<p>Kişisel verileriniz, açık onayınız olmaksızın üçüncü taraflarla ticari amaçla paylaşılmaz. Veriler yalnızca yasal zorunluluk, yetkili kurum talepleri veya web sitesi altyapı hizmeti gibi teknik gereksinimlerde sınırlı ölçüde kullanılabilir.</p>

<h3>4. Çerezler (Cookies)</h3>
<p>Web sitemiz, kullanıcı deneyimini iyileştirmek amacıyla çerezler kullanmaktadır. Tarayıcınızın ayarlarından çerezleri devre dışı bırakabilirsiniz; ancak bu durumda bazı sayfa işlevleri çalışmayabilir.</p>

<h3>5. Veri Güvenliği</h3>
<p>Kişisel verileriniz, yetkisiz erişim, değiştirilme veya ifşaya karşı uygun teknik ve idari önlemlerle korunmaktadır. Web sitemiz SSL/TLS sertifikası ile şifrelenmiş bağlantı üzerinden hizmet vermektedir.</p>

<h3>6. Haklarınız</h3>
<p>6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında aşağıdaki haklara sahipsiniz:</p>
<ul>
  <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
  <li>Verilerinize erişim talep etme</li>
  <li>Yanlış veya eksik verilerin düzeltilmesini isteme</li>
  <li>Verilerinizin silinmesini talep etme</li>
  <li>Veri işlemeye itiraz etme</li>
</ul>
<p>Bu haklarınızı kullanmak için <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a> adresine e-posta gönderebilirsiniz.</p>

<h3>7. İletişim</h3>
<p>Gizlilik politikamız hakkında sorularınız için:</p>
<p><strong>Türk Artroplasti Derneği</strong><br>
E-posta: <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a><br>
Web: <a href="https://artroplasti.org.tr">artroplasti.org.tr</a></p>

<h3>8. Değişiklikler</h3>
<p>Bu politika gerektiğinde güncellenebilir. Önemli değişiklikler web sitemizde duyurulacaktır. Politikanın güncel versiyonu her zaman bu sayfada yayımlanır.</p>
HTML,
        ],
        'en' => [
            'title'  => 'Privacy Policy',
            'slug'   => 'privacy-policy',
            'content' => <<<HTML
<h2>Privacy Policy</h2>
<p>Last updated: June 2026</p>

<p>The Turkish Arthroplasty Association ("Association", "we", "our") is committed to protecting the privacy of all visitors to our website. This Privacy Policy explains what data we collect when you use <strong>artroplasti.org.tr</strong>, how we use it, and what rights you have.</p>

<h3>1. Data We Collect</h3>
<p>When you visit our website or complete our forms, we may collect the following information:</p>
<ul>
  <li>Name, surname, and professional title</li>
  <li>Email address and phone number</li>
  <li>Medical specialty and institution (on membership forms)</li>
  <li>Technical data such as IP address, browser type, and session duration</li>
</ul>

<h3>2. How We Use Your Data</h3>
<p>Collected data is used solely for the following purposes:</p>
<ul>
  <li>Processing membership applications and communicating with members</li>
  <li>Sending announcements for congresses, courses, and events</li>
  <li>Delivering the e-newsletter service (requires subscription consent)</li>
  <li>Analysing website usage to improve our services</li>
  <li>Meeting legal obligations</li>
</ul>

<h3>3. Data Sharing</h3>
<p>Your personal data will not be shared with third parties for commercial purposes without your explicit consent. Data may be used to a limited extent only in cases of legal obligation, requests from authorised authorities, or technical requirements such as website infrastructure services.</p>

<h3>4. Cookies</h3>
<p>Our website uses cookies to enhance the user experience. You may disable cookies in your browser settings; however, some page functions may not work as expected if you do so.</p>

<h3>5. Data Security</h3>
<p>Your personal data is protected by appropriate technical and administrative measures against unauthorised access, alteration, or disclosure. Our website operates over SSL/TLS encrypted connections.</p>

<h3>6. Your Rights</h3>
<p>Under applicable data protection legislation, you have the right to:</p>
<ul>
  <li>Learn whether your personal data is being processed</li>
  <li>Request access to your data</li>
  <li>Request correction of inaccurate or incomplete data</li>
  <li>Request deletion of your data</li>
  <li>Object to data processing</li>
</ul>
<p>To exercise these rights, please send an email to <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a>.</p>

<h3>7. Contact</h3>
<p>For questions regarding our privacy policy:</p>
<p><strong>Turkish Arthroplasty Association</strong><br>
Email: <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a><br>
Web: <a href="https://artroplasti.org.tr">artroplasti.org.tr</a></p>

<h3>8. Changes</h3>
<p>This policy may be updated when necessary. Significant changes will be announced on our website. The current version of this policy is always published on this page.</p>
HTML,
        ],
    ],

    'terms-and-conditions' => [
        'tr' => [
            'title'  => 'Kullanım Koşulları',
            'slug'   => 'kullanim-kosullari',
            'content' => <<<HTML
<h2>Kullanım Koşulları</h2>
<p>Son güncelleme: Haziran 2026</p>

<p>Bu web sitesini kullanarak aşağıdaki kullanım koşullarını kabul etmiş sayılırsınız. Lütfen bu koşulları dikkatlice okuyunuz. Koşulları kabul etmiyorsanız siteyi kullanmayı bırakınız.</p>

<h3>1. Genel Hükümler</h3>
<p><strong>artroplasti.org.tr</strong>, Türk Artroplasti Derneği'nin ("Dernek") resmi web sitesidir. Site içeriği yalnızca bilgilendirme amaçlıdır ve tıbbi tavsiye niteliği taşımaz. Sağlık kararları için mutlaka uzman bir hekime başvurunuz.</p>

<h3>2. Fikri Mülkiyet Hakları</h3>
<p>Bu web sitesinde yer alan tüm içerik (metinler, görseller, logolar, tasarım, yazılım ve diğer materyaller) Türk Artroplasti Derneği'ne aittir veya Dernek tarafından lisanslanmıştır. Dernek'in yazılı izni olmaksızın hiçbir içerik kopyalanamaz, dağıtılamaz veya ticari amaçla kullanılamaz.</p>

<h3>3. Kullanıcı Sorumlulukları</h3>
<p>Siteyi kullanan kişiler aşağıdakileri kabul eder:</p>
<ul>
  <li>Siteyi yalnızca yasal amaçlarla kullanmak</li>
  <li>Sistemin güvenliğini tehdit edecek girişimlerde bulunmamak</li>
  <li>Yanıltıcı veya zararlı içerik paylaşmamak</li>
  <li>Başkalarının haklarını ihlal eden davranışlardan kaçınmak</li>
</ul>

<h3>4. Üyelik ve Kayıt</h3>
<p>Dernek üyeliği ve etkinlik kayıtları yalnızca gerçek ve doğru bilgilerin verilmesi koşuluyla geçerlidir. Yanlış bilgi nedeniyle doğabilecek hukuki ve mali sorumluluk tamamen kullanıcıya aittir.</p>

<h3>5. Harici Bağlantılar</h3>
<p>Web sitemiz, üçüncü taraf web sitelerine bağlantılar içerebilir. Bu bağlantılar yalnızca bilgilendirme amacıyla sunulmaktadır; Dernek, bağlantı verilen harici sitelerin içeriklerinden sorumlu değildir.</p>

<h3>6. Sorumluluk Reddi</h3>
<p>Sitede yer alan bilgiler, mümkün olduğunca güncel ve doğru tutulmaya çalışılmaktadır. Ancak Dernek, bilgilerin doğruluğu veya eksiksizliği konusunda herhangi bir garanti vermez. Sitede yer alan bilgilerin kullanımından doğabilecek zararlardan Dernek sorumlu tutulamaz.</p>

<h3>7. Tıbbi Sorumluluk Reddi</h3>
<p>Bu web sitesindeki içerikler profesyonel tıbbi tavsiye, tanı veya tedavi yerine geçmez. Her zaman nitelikli bir sağlık profesyonelinin görüşüne başvurunuz. Tıbbi bir acil durum yaşıyorsanız derhal 112'yi arayınız.</p>

<h3>8. Uygulanacak Hukuk</h3>
<p>Bu kullanım koşulları Türkiye Cumhuriyeti kanunlarına tabidir. Bu koşullardan doğabilecek uyuşmazlıklarda İstanbul mahkemeleri ve icra daireleri yetkilidir.</p>

<h3>9. Değişiklikler</h3>
<p>Dernek, bu kullanım koşullarını önceden bildirimde bulunmaksızın değiştirme hakkını saklı tutar. Güncel koşullar her zaman bu sayfada yayımlanır. Siteyi kullanmaya devam etmeniz, değiştirilmiş koşulları kabul ettiğiniz anlamına gelir.</p>

<h3>10. İletişim</h3>
<p>Kullanım koşulları hakkında sorularınız için:<br>
<strong>Türk Artroplasti Derneği</strong><br>
E-posta: <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a></p>
HTML,
        ],
        'en' => [
            'title'  => 'Terms and Conditions',
            'slug'   => 'terms-and-conditions',
            'content' => <<<HTML
<h2>Terms and Conditions</h2>
<p>Last updated: June 2026</p>

<p>By using this website, you agree to the following terms and conditions. Please read them carefully. If you do not accept these terms, please discontinue use of the site.</p>

<h3>1. General Provisions</h3>
<p><strong>artroplasti.org.tr</strong> is the official website of the Turkish Arthroplasty Association ("Association"). The content of this site is provided for informational purposes only and does not constitute medical advice. Please consult a qualified physician for health-related decisions.</p>

<h3>2. Intellectual Property Rights</h3>
<p>All content on this website (texts, images, logos, design, software, and other materials) belongs to the Turkish Arthroplasty Association or is licensed to the Association. No content may be copied, distributed, or used for commercial purposes without the written consent of the Association.</p>

<h3>3. User Responsibilities</h3>
<p>Users of the site agree to:</p>
<ul>
  <li>Use the site for lawful purposes only</li>
  <li>Refrain from any attempt to compromise the security of the system</li>
  <li>Not share misleading or harmful content</li>
  <li>Avoid conduct that infringes the rights of others</li>
</ul>

<h3>4. Membership and Registration</h3>
<p>Association membership and event registrations are valid only when accurate and truthful information is provided. Any legal or financial liability arising from the submission of false information lies solely with the user.</p>

<h3>5. External Links</h3>
<p>Our website may contain links to third-party websites. These links are provided for informational purposes only; the Association is not responsible for the content of externally linked sites.</p>

<h3>6. Disclaimer</h3>
<p>The information on this site is kept as current and accurate as possible. However, the Association makes no warranty regarding the accuracy or completeness of the information. The Association shall not be held liable for any damages arising from the use of the information on this site.</p>

<h3>7. Medical Disclaimer</h3>
<p>The content on this website does not replace professional medical advice, diagnosis, or treatment. Always seek the opinion of a qualified healthcare professional. If you are experiencing a medical emergency, call emergency services immediately.</p>

<h3>8. Governing Law</h3>
<p>These terms and conditions are governed by the laws of the Republic of Turkey. Istanbul courts and enforcement offices shall have jurisdiction over any disputes arising from these terms.</p>

<h3>9. Changes</h3>
<p>The Association reserves the right to amend these terms and conditions without prior notice. The current terms are always published on this page. Continued use of the site constitutes acceptance of the amended terms.</p>

<h3>10. Contact</h3>
<p>For questions regarding the terms and conditions:<br>
<strong>Turkish Arthroplasty Association</strong><br>
Email: <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a></p>
HTML,
        ],
    ],
];

// ─── Helper: upsert a page ────────────────────────────────────────────────────

function upsert_page(string $slug, string $title, string $content, string $lang, bool $dryRun): int
{
    $existing = get_posts([
        'post_type'      => 'page',
        'name'           => $slug,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'suppress_filters' => false,
        'lang'           => $lang,
    ]);

    if (!empty($existing)) {
        $post_id = $existing[0]->ID;
        echo "[{$lang}] Updating existing page '{$slug}' (ID {$post_id})\n";
        if (!$dryRun) {
            wp_update_post([
                'ID'           => $post_id,
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
            ]);
        }
        return $post_id;
    }

    echo "[{$lang}] Creating new page '{$slug}'\n";
    if ($dryRun) {
        return 0;
    }

    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ]);

    if (is_wp_error($post_id)) {
        echo "ERROR: " . $post_id->get_error_message() . "\n";
        return 0;
    }

    return $post_id;
}

// ─── Main loop ───────────────────────────────────────────────────────────────

foreach ($pages as $key => $langs_data) {
    echo "\n=== Processing: {$key} ===\n";

    $tr_id = upsert_page(
        $langs_data['tr']['slug'],
        $langs_data['tr']['title'],
        $langs_data['tr']['content'],
        'tr',
        $dryRun
    );

    $en_id = upsert_page(
        $langs_data['en']['slug'],
        $langs_data['en']['title'],
        $langs_data['en']['content'],
        'en',
        $dryRun
    );

    if (!$dryRun && $tr_id && $en_id) {
        pll_set_post_language($tr_id, 'tr');
        pll_set_post_language($en_id, 'en');
        pll_save_post_translations(['tr' => $tr_id, 'en' => $en_id]);
        echo "Polylang translation link set: TR={$tr_id} <-> EN={$en_id}\n";
    }
}

// ─── Set WordPress privacy policy page to the TR privacy page ────────────────
if (!$dryRun) {
    $privacy_tr = get_posts([
        'post_type'        => 'page',
        'name'             => $pages['privacy-policy']['tr']['slug'],
        'post_status'      => 'publish',
        'posts_per_page'   => 1,
        'suppress_filters' => false,
        'lang'             => 'tr',
    ]);
    if (!empty($privacy_tr)) {
        update_option('wp_page_for_privacy_policy', $privacy_tr[0]->ID);
        echo "\nWordPress privacy-policy option set to page ID " . $privacy_tr[0]->ID . "\n";
    }
}

echo "\nDone.\n";

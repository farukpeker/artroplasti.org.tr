<?php
/**
 * Creates English translations for the 4 events missing EN versions.
 *
 * Usage:
 *   php tools/create_event_translations.php --dry-run
 *   php tools/create_event_translations.php
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "ERROR: Polylang not available.\n"; exit(1);
}

$translations = [

    // ── 1876: İstanbul Monthly Meeting – June 2026 ───────────────────────────
    1876 => [
        'title'   => 'Istanbul Monthly Arthroplasty Meeting',
        'slug'    => 'istanbul-monthly-arthroplasty-meeting-june-2026',
        'excerpt' => 'The June 2026 Istanbul Monthly Arthroplasty Meeting focuses on hip-preserving surgical approaches, including FAI surgery and periacetabular osteotomy.',
        'content' => <<<HTML
<!-- wp:paragraph -->
<p><strong>Dear Colleagues,</strong><br>The Istanbul Monthly Arthroplasty Meeting will be held on Monday, 1 June at 18:30 at Istanbul University Istanbul Faculty of Medicine – Kemal Atay Conference Hall.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>June 2026 – Monthly Meeting Topic:</strong><br>"Current Hip-Preserving Treatment Approaches: How Well Can We Actually Protect the Hip?"</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The session will provide a comprehensive review of hip-preserving surgical techniques aimed at preventing progression to total hip arthroplasty, with a particular focus on femoroacetabular impingement (FAI) surgery and periacetabular osteotomy. Presentations will be delivered by experienced specialists in the field.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The session will be moderated by Prof. Dr. Mehmet Aşık, Head of the Department of Orthopaedics and Traumatology at Istanbul Faculty of Medicine, who has hosted our monthly meetings for two consecutive terms between 2024 and 2026.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Alongside our younger and more experienced faculty, Prof. Dr. Remzi Tözün will also contribute his deep knowledge and expertise to the programme. For colleagues with a particular interest in this area, the opportunity to learn from our faculty's extensive experience represents a valuable academic occasion. We look forward to welcoming you to this special meeting where scientific exchange will be at the forefront.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Moderator:</strong><br>Prof. Dr. Mehmet AŞIK<br><br><strong>Programme:</strong><br>18:30 – 19:00 Reception / Dinner<br>19:00 – 19:10 Natural history of FAI – Assoc. Prof. Dr. Taha Kızılkurt<br>19:10 – 19:20 Current treatment approaches in FAI – Prof. Dr. Gökhan Polat<br>19:20 – 19:30 Periacetabular osteotomies – Prof. Dr. Remzi Tözün<br>19:30 – 20:00 Case Discussion<br><br><strong>Note:</strong> If you have cases you would like to discuss or results you wish to present, please get in touch before the meeting for planning purposes.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Meeting Contact:</strong><br>Dr. Vahit Emre ÖZDEN<br>Tel: <a href="tel:5323361464">532 336 14 64</a><br>E-mail: <a href="mailto:vahitemre@gmail.com">vahitemre@gmail.com</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Prof. Dr. Cengiz ŞEN</strong><br>Istanbul University Istanbul Faculty of Medicine</p>
<!-- /wp:paragraph -->
HTML,
    ],

    // ── 1854: İstanbul Monthly Meeting – May 2026 ────────────────────────────
    1854 => [
        'title'   => 'Istanbul Monthly Arthroplasty Meeting',
        'slug'    => 'istanbul-monthly-arthroplasty-meeting-may-2026',
        'excerpt' => 'The May 2026 Istanbul Monthly Arthroplasty Meeting covers acetabular reconstruction in pelvic discontinuity, including cage techniques and current surgical strategies.',
        'content' => <<<HTML
<!-- wp:paragraph -->
<p>Dear Colleagues,</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The Istanbul Monthly Arthroplasty Meeting will be held on Monday, 4 May at 18:30 at Istanbul University Istanbul Faculty of Medicine – Kemal Atay Conference Hall.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>May meeting topic:<br><em>'Acetabular Reconstruction in Pelvic Discontinuity: Surgical Strategies and Current Approaches'</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Pelvic discontinuity remains one of the most complex and challenging scenarios in revision hip arthroplasty. The extent of bone loss, biomechanical instability, and the variability of patient factors require advanced surgical expertise and a multidisciplinary approach in operative planning.<br>This session will address the classification of pelvic bone loss, preoperative planning, different reconstruction options, and clinical outcomes, guided by the current literature.<br>With contributions from experienced speakers, the session is designed to be particularly valuable for colleagues engaged in revision surgery. We look forward to a productive meeting enriched by your participation and contributions, and warmly invite all colleagues — especially residents and junior specialists.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Moderator:<br>Prof. Dr. Hakan GÜRBÜZ</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Programme:<br>18:30 – 19:00 Reception / Dinner</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>19:00 – 19:10 Pelvic Discontinuity: Aetiology / Classification / Treatment Options<br>Prof. Dr. Yusuf ÖZTÜRKMEN<br>19:10 – 19:20 Treatment approach: cage constructs<br>Prof. Dr. İbrahim AZBOY<br>19:20 – 19:30 Novel approaches in management<br>Dr. Mehmet Süleyman ABUL<br>19:30 – 20:00 Case Discussion</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Note: If you have cases you would like to discuss or results you wish to present, please get in touch before the meeting for planning purposes.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Meeting Contact:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Prof. Dr. Vahit Emre ÖZDEN<br>Tel: 532 336 14 64 &nbsp; <a href="mailto:vahitemre@gmail.com">vahitemre@gmail.com</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Prof. Dr. Cengiz ŞEN<br>Istanbul University Istanbul Faculty of Medicine</p>
<!-- /wp:paragraph -->
HTML,
    ],

    // ── 1829: Ankara Monthly Meeting – April 2026 ────────────────────────────
    1829 => [
        'title'   => 'Ankara Monthly Arthroplasty Meeting',
        'slug'    => 'ankara-monthly-arthroplasty-meeting-april-2026',
        'excerpt' => 'The April 2026 Ankara Monthly Arthroplasty Meeting focuses on total hip arthroplasty in developmental dysplasia of the hip, covering acetabular reconstruction with and without graft and femoral shortening techniques.',
        'content' => <<<HTML
<!-- wp:paragraph -->
<p>The Ankara Monthly Arthroplasty Meeting will be held on Thursday, 16 April at 18:30 at Gazi University Hospital.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The April meeting topic will be <em>'Total Hip Arthroplasty in the Setting of Developmental Dysplasia of the Hip.'</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>This important subject in arthroplasty surgery will be presented by Prof. Dr. Ertuğrul Şener, Prof. Dr. Ömer Faruk Bilgen, Prof. Dr. Ömür Çağlar, and Prof. Dr. Berk Güçlü. The session will also include current journal articles and case discussions.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>All colleagues are invited to this meeting, which we believe will be especially beneficial for residents and junior specialists.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Prof. Dr. Kerem Başarır</strong> (on behalf of KADAD)<br><strong>Prof. Dr. Hakan Atalar</strong> (Gazi University Faculty of Medicine)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Programme:</strong></p>
<!-- /wp:paragraph -->

<!-- wp:table -->
<figure class="wp-block-table"><table class="has-fixed-layout"><tbody><tr><td>18:00 – 18:30</td><td>Reception / Dinner</td><td></td></tr><tr><td>18:30 – 18:40</td><td>'Approach to Total Hip Arthroplasty in Developmental Dysplasia of the Hip'</td><td>Prof. Dr. Ertuğrul Şener</td></tr><tr><td>18:40 – 18:50</td><td>'Cementless Acetabulum in DDH'</td><td>Prof. Dr. Ömer Faruk Bilgen</td></tr><tr><td>18:50 – 19:00</td><td>'Acetabulum with Bone Graft in DDH'</td><td>Prof. Dr. Ömür Çağlar</td></tr><tr><td>19:00 – 19:10</td><td>'THA with Femoral Shortening Osteotomy in DDH'</td><td>Prof. Dr. Berk Güçlü</td></tr><tr><td>19:10 – 19:20</td><td>Journal presentation and case discussions</td><td></td></tr><tr><td>19:20 – 19:40</td><td>Discussion</td><td></td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph -->
<p><em>Note: If you have cases you would like to discuss or results you wish to present, please get in touch before the meeting for planning purposes.</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Meeting Contact:</strong><br>Dr. Kerem BAŞARIR &nbsp; 0 536 219 90 45 &nbsp; <a href="mailto:basarirkerem@yahoo.com">basarirkerem@yahoo.com</a></p>
<!-- /wp:paragraph -->
HTML,
    ],

    // ── 1828: 27th Basic Arthroplasty Course ─────────────────────────────────
    1828 => [
        'title'   => '27th Basic Arthroplasty Course',
        'slug'    => '27th-basic-arthroplasty-course',
        'excerpt' => 'The 27th Basic Arthroplasty Course will be held in Ankara on 15–16 May 2026, offering theoretical lectures, hands-on practice on bone models, and interactive case discussions for early-career orthopaedic surgeons.',
        'content' => <<<HTML
<!-- wp:paragraph -->
<p>Dear Colleagues,</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>As the Hip and Knee Arthroplasty Association (KADAD), we will be hosting the 27th Basic Arthroplasty Course in Ankara on 15–16 May 2026. The course is designed for young colleagues who wish to develop their knowledge and skills in hip and knee arthroplasty. In addition to theoretical lectures, the programme includes hands-on practice on bone models and interactive round-table case discussions, with the aim of updating both knowledge and practical technique. Participants will have the opportunity to engage in practical and theoretical discussion with experienced arthroplasty educators.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The two-day programme has been structured to address current approaches and to encourage active contribution from all participants. We are honoured to be joining you at this meeting, where scientific exchange and professional interaction will be at the forefront.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em>We look forward to seeing you at the 27th Basic Arthroplasty Course.</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>President of the Hip and Knee Arthroplasty Association: <strong>Prof. Dr. İbrahim Tuncay</strong><br>Course Chair: <strong>Prof. Dr. Berk Güçlü</strong><br>For registration: <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a></p>
<!-- /wp:paragraph -->
HTML,
    ],
];

// ─── Create translations ──────────────────────────────────────────────────────

foreach ($translations as $tr_id => $data) {
    $tr_post = get_post($tr_id);
    if (!$tr_post) {
        echo "SKIP: TR post {$tr_id} not found.\n";
        continue;
    }

    $existing = pll_get_post_translations($tr_id);
    if (!empty($existing['en'])) {
        $en_id = $existing['en'];
        echo "UPDATE: EN already exists TR:{$tr_id} → EN:{$en_id} — updating content.\n";
        if (!$dryRun) {
            wp_update_post([
                'ID'           => $en_id,
                'post_title'   => $data['title'],
                'post_name'    => $data['slug'],
                'post_excerpt' => $data['excerpt'],
                'post_content' => $data['content'],
                'post_status'  => 'publish',
            ]);
        }
        continue;
    }

    echo "CREATE: EN for TR:{$tr_id} → '{$data['title']}'\n";
    if ($dryRun) continue;

    $en_id = wp_insert_post([
        'post_title'   => $data['title'],
        'post_name'    => $data['slug'],
        'post_excerpt' => $data['excerpt'],
        'post_content' => $data['content'],
        'post_status'  => 'publish',
        'post_type'    => $tr_post->post_type,
        'post_date'    => $tr_post->post_date,
    ]);

    if (is_wp_error($en_id)) {
        echo "ERROR: " . $en_id->get_error_message() . "\n";
        continue;
    }

    pll_set_post_language($tr_id, 'tr');
    pll_set_post_language($en_id, 'en');
    pll_save_post_translations(['tr' => $tr_id, 'en' => $en_id]);

    echo "  → Created EN:{$en_id}, linked TR:{$tr_id} ↔ EN:{$en_id}\n";
}

echo "\nDone.\n";

<?php
/**
 * Creates English translations for Turkish webinar posts.
 * All source posts are empty — content is written fresh in English.
 *
 * Usage:
 *   php tools/create_webinar_translations.php --dry-run
 *   php tools/create_webinar_translations.php
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }

$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__) . '/wp-load.php';

if (!function_exists('pll_set_post_language') || !function_exists('pll_save_post_translations')) {
    echo "ERROR: Polylang not available.\n"; exit(1);
}

// TR post ID => English translation data
$translations = [
    1814 => [
        'title'   => '2015 Educational Webinars',
        'slug'    => '2015-educational-webinars',
        'excerpt' => 'The 2015 Educational Webinar series of the Turkish Arthroplasty Association brought together specialists in hip and knee arthroplasty for online training sessions throughout the year.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association launched its educational webinar programme in 2015, providing orthopaedic surgeons, residents, and allied healthcare professionals with access to focused online lectures on hip and knee arthroplasty.</p>

<h3>About the 2015 Series</h3>
<p>The 2015 webinar series covered fundamental and advanced topics in arthroplasty practice, including surgical techniques, implant selection, complication management, and postoperative rehabilitation. Sessions were delivered by leading specialists from Turkey and abroad.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Total hip arthroplasty: surgical approaches and implant options</li>
  <li>Total knee arthroplasty: alignment principles and soft-tissue balancing</li>
  <li>Periprosthetic joint infection: prevention and management</li>
  <li>Revision arthroplasty: indications and planning</li>
</ul>

<p>Recordings and presentation materials from the 2015 series are available to Association members through the member portal.</p>
HTML,
    ],

    1813 => [
        'title'   => '2016 Educational Webinars',
        'slug'    => '2016-educational-webinars',
        'excerpt' => 'The 2016 Educational Webinar series of the Turkish Arthroplasty Association delivered online sessions on current topics in hip and knee arthroplasty surgery.',
        'content' => <<<HTML
<p>Building on the success of the inaugural series, the Turkish Arthroplasty Association continued its educational webinar programme in 2016 with an expanded schedule of online lectures and case-based discussions.</p>

<h3>About the 2016 Series</h3>
<p>The 2016 programme emphasised evidence-based practice and clinical decision-making in arthroplasty. Expert faculty shared practical insights on patient selection, surgical planning, and outcome optimisation.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Direct anterior approach in total hip arthroplasty</li>
  <li>Total knee arthroplasty in valgus deformity</li>
  <li>Constrained prostheses in complex primary knee arthroplasty</li>
  <li>Avascular necrosis of the hip: non-vascularised grafting and PRP applications</li>
</ul>

<p>Recordings and presentation materials from the 2016 series are available to Association members through the member portal.</p>
HTML,
    ],

    1812 => [
        'title'   => '2017 Educational Webinars',
        'slug'    => '2017-educational-webinars',
        'excerpt' => 'The 2017 Educational Webinar series of the Turkish Arthroplasty Association offered online training sessions on advances in hip and knee arthroplasty.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2017 webinar series focused on evolving techniques and emerging evidence in arthroplasty surgery. Sessions were held on a monthly basis and attracted participants from across Turkey.</p>

<h3>About the 2017 Series</h3>
<p>The 2017 programme featured interactive case presentations alongside didactic lectures, encouraging audience participation and peer learning. Topics reflected current challenges and controversies in clinical practice.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Bearing surface options in total hip arthroplasty</li>
  <li>Kinematic versus mechanical alignment in total knee arthroplasty</li>
  <li>Periprosthetic fractures: classification and surgical management</li>
  <li>Enhanced recovery after arthroplasty surgery</li>
</ul>

<p>Recordings and presentation materials from the 2017 series are available to Association members through the member portal.</p>
HTML,
    ],

    1811 => [
        'title'   => '2018 Educational Webinars',
        'slug'    => '2018-educational-webinars',
        'excerpt' => 'The 2018 Educational Webinar series of the Turkish Arthroplasty Association provided in-depth online sessions on current topics in arthroplasty surgery.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2018 webinar series continued to advance the professional development of arthroplasty specialists through high-quality online education. The programme attracted a growing audience of orthopaedic surgeons and residents.</p>

<h3>About the 2018 Series</h3>
<p>Sessions in 2018 explored both operative techniques and the broader care pathway for arthroplasty patients, including preoperative optimisation and long-term follow-up strategies.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Minimally invasive approaches in total hip arthroplasty</li>
  <li>Patellar resurfacing in total knee arthroplasty: to resurface or not?</li>
  <li>Management of the stiff total knee</li>
  <li>Unicompartmental knee arthroplasty: indications and outcomes</li>
</ul>

<p>Recordings and presentation materials from the 2018 series are available to Association members through the member portal.</p>
HTML,
    ],

    1810 => [
        'title'   => '2019 Educational Webinars',
        'slug'    => '2019-educational-webinars',
        'excerpt' => 'The 2019 Educational Webinar series of the Turkish Arthroplasty Association covered current advances and clinical challenges in hip and knee arthroplasty.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2019 webinar series offered a comprehensive online curriculum for orthopaedic professionals working in the field of arthroplasty. Expert speakers presented on topics ranging from primary arthroplasty to complex revisions.</p>

<h3>About the 2019 Series</h3>
<p>The 2019 programme introduced a new format combining short didactic lectures with moderated discussion panels, providing participants with an opportunity to engage directly with faculty and explore real-world clinical scenarios.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Dual-mobility cups in primary and revision hip arthroplasty</li>
  <li>Robotic-assisted total knee arthroplasty: current evidence</li>
  <li>Tibial component fixation: cemented versus cementless options</li>
  <li>Outpatient arthroplasty: patient selection and safety protocols</li>
</ul>

<p>Recordings and presentation materials from the 2019 series are available to Association members through the member portal.</p>
HTML,
    ],

    1809 => [
        'title'   => '2020 Educational Webinars',
        'slug'    => '2020-educational-webinars',
        'excerpt' => 'The 2020 Educational Webinar series of the Turkish Arthroplasty Association continued online education for arthroplasty professionals throughout a challenging year.',
        'content' => <<<HTML
<p>Despite the global challenges of 2020, the Turkish Arthroplasty Association maintained its commitment to professional education by delivering its full webinar programme online. The digital format enabled broader participation from across Turkey and internationally.</p>

<h3>About the 2020 Series</h3>
<p>The 2020 programme adapted swiftly to changing circumstances and expanded its online reach. Sessions covered both the clinical management of arthroplasty patients and the organisational adaptations required during the COVID-19 pandemic.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Arthroplasty service management during the COVID-19 pandemic</li>
  <li>Computer navigation in total knee arthroplasty</li>
  <li>Cementless fixation in total hip arthroplasty: long-term outcomes</li>
  <li>Managing dislocation after total hip arthroplasty</li>
</ul>

<p>Recordings and presentation materials from the 2020 series are available to Association members through the member portal.</p>
HTML,
    ],

    1808 => [
        'title'   => '2021 Educational Webinars',
        'slug'    => '2021-educational-webinars',
        'excerpt' => 'The 2021 Educational Webinar series of the Turkish Arthroplasty Association delivered high-quality online sessions on the latest developments in arthroplasty surgery.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2021 webinar series built on the expanded digital reach of the previous year, offering a refined and engaging online programme for arthroplasty professionals at all levels of experience.</p>

<h3>About the 2021 Series</h3>
<p>The 2021 programme featured an increased number of international faculty contributions and introduced live Q&amp;A sessions to foster interactive learning. Topics reflected the latest evidence and emerging innovations in arthroplasty care.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Partial knee replacement: medial, lateral, and patellofemoral options</li>
  <li>Total hip arthroplasty in the young patient</li>
  <li>Custom implants and patient-specific instrumentation</li>
  <li>Periprosthetic joint infection: diagnosis and two-stage revision</li>
</ul>

<p>Recordings and presentation materials from the 2021 series are available to Association members through the member portal.</p>
HTML,
    ],

    1807 => [
        'title'   => '2022 Educational Webinars',
        'slug'    => '2022-educational-webinars',
        'excerpt' => 'The 2022 Educational Webinar series of the Turkish Arthroplasty Association provided online training on advances in hip and knee arthroplasty for orthopaedic professionals.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2022 webinar series offered a rich programme of online education for orthopaedic surgeons and healthcare professionals specialising in arthroplasty. The series returned to a hybrid format, combining virtual participation with live events.</p>

<h3>About the 2022 Series</h3>
<p>Sessions in 2022 focused on translating research evidence into surgical practice, with faculty from leading arthroplasty centres sharing their clinical experience and perspectives on current controversies.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Stems in revision hip arthroplasty: classification and selection</li>
  <li>Tibial bone loss in revision knee arthroplasty</li>
  <li>Instability after total knee arthroplasty: causes and solutions</li>
  <li>Infection prevention in arthroplasty: current best practice</li>
</ul>

<p>Recordings and presentation materials from the 2022 series are available to Association members through the member portal.</p>
HTML,
    ],

    32 => [
        'title'   => '2024 Educational Webinars',
        'slug'    => '2024-educational-webinars',
        'excerpt' => 'The 2024 Educational Webinar series of the Turkish Arthroplasty Association presents the latest advances in hip and knee arthroplasty through online sessions for orthopaedic professionals.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2024 Educational Webinar series provides orthopaedic surgeons, residents, and allied healthcare professionals with access to up-to-date knowledge and expert discussion on all aspects of hip and knee arthroplasty.</p>

<h3>About the 2024 Series</h3>
<p>The 2024 programme features monthly webinars delivered by experienced faculty from leading arthroplasty centres in Turkey and internationally. Sessions combine focused lectures with interactive case discussions and live Q&amp;A.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Robotic and computer-assisted arthroplasty: current evidence and future directions</li>
  <li>Cementless total knee arthroplasty: indications and outcomes</li>
  <li>Revision total hip arthroplasty: acetabular reconstruction strategies</li>
  <li>Patient-reported outcomes in arthroplasty: measurement and interpretation</li>
  <li>Antibiotic prophylaxis and infection prevention: updated guidelines</li>
</ul>

<p>Recordings and presentation materials from completed sessions are available to Association members through the member portal. Upcoming webinar dates and registration details are announced via the Association newsletter.</p>
HTML,
    ],

    34 => [
        'title'   => '2025 Educational Webinars',
        'slug'    => '2025-educational-webinars',
        'excerpt' => 'The 2025 Educational Webinar series of the Turkish Arthroplasty Association brings together leading experts to discuss the latest advances in hip and knee arthroplasty.',
        'content' => <<<HTML
<p>The Turkish Arthroplasty Association's 2025 Educational Webinar series continues its mission to deliver high-quality online education for orthopaedic professionals across Turkey and beyond. The programme covers the full spectrum of arthroplasty practice, from primary surgery to complex revision cases.</p>

<h3>About the 2025 Series</h3>
<p>The 2025 series brings together nationally and internationally recognised faculty for monthly online sessions. Each webinar focuses on a clinically relevant topic, combining evidence-based content with practical insights from surgical practice. Live interaction with speakers is encouraged throughout.</p>

<h3>Topics Covered</h3>
<ul>
  <li>Artificial intelligence and machine learning in arthroplasty planning</li>
  <li>Extended trochanteric osteotomy in revision hip arthroplasty</li>
  <li>Managing the failed total knee arthroplasty: stepwise approach</li>
  <li>Emerging bearing surfaces and fixation technologies</li>
  <li>Shared decision-making and patient education in arthroplasty</li>
</ul>

<p>To participate in upcoming webinars, register via the Association newsletter or contact the secretariat at <a href="mailto:dernek@artroplasti.org.tr">dernek@artroplasti.org.tr</a>. Recordings of completed sessions are available to members through the member portal.</p>
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

    // Check if English translation already exists
    $existing_translations = pll_get_post_translations($tr_id);
    if (!empty($existing_translations['en'])) {
        $en_id = $existing_translations['en'];
        echo "UPDATE: EN translation already exists for TR:{$tr_id} → EN:{$en_id} ({$data['title']})\n";
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

    echo "CREATE: EN translation for TR:{$tr_id} → '{$data['title']}'\n";
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

    echo "  → Created EN post ID:{$en_id}, linked TR:{$tr_id} ↔ EN:{$en_id}\n";
}

echo "\nDone.\n";

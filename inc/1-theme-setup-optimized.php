<?php
/**
 * Grant Insight Perfect - 1. Theme Setup File (Optimized)
 *
 * テーマの基本設定、スクリプト読込、ウィジェット、カスタマイザー、
 * パフォーマンス・セキュリティ最適化などを担当します。
 * 外部CDN依存を削除し、パフォーマンスを最適化しました。
 *
 * @package Grant_Insight_Perfect
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

/**
 * テーマバージョン定数（未定義の場合のみ）
 */
if (!defined('GI_THEME_VERSION')) {
    define('GI_THEME_VERSION', wp_get_theme()->get('Version'));
}

/**
 * defer属性追加関数（重複回避）
 */
if (!function_exists('gi_add_defer_attribute')) {
    function gi_add_defer_attribute($tag, $handle, $src) {
        // deferを追加するスクリプト
        $defer_scripts = array(
            'gi-main-js',
            'gi-frontend-js',
            'ai-chatbot-js'
        );
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace('<script ', '<script defer ', $tag);
        }
        
        return $tag;
    }
}

/**
 * テーマセットアップ
 */
function gi_setup() {
    // テーマサポート追加
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-background');
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
    add_theme_support('menus');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    
    // RSS フィード
    add_theme_support('automatic-feed-links');
    
    // 画像サイズ追加（CLS対策：固定サイズ）
    add_image_size('gi-card-thumb', 400, 300, true);
    add_image_size('gi-hero-thumb', 800, 600, true);
    add_image_size('gi-tool-logo', 120, 120, true);
    add_image_size('gi-banner', 1200, 400, true);
    add_image_size('gi-logo-sm', 80, 80, true); // ヘッダーロゴ用
    
    // 言語ファイル読み込み
    load_theme_textdomain('grant-insight', get_template_directory() . '/languages');
    
    // メニュー登録
    register_nav_menus(array(
        'primary' => 'メインメニュー',
        'footer' => 'フッターメニュー',
        'mobile' => 'モバイルメニュー'
    ));
}
add_action('after_setup_theme', 'gi_setup');

/**
 * コンテンツ幅設定
 */
function gi_content_width() {
    $GLOBALS['content_width'] = apply_filters('gi_content_width', 1200);
}
add_action('after_setup_theme', 'gi_content_width', 0);

/**
 * 重複スクリプト削除（パフォーマンス最適化）
 */
function gi_remove_duplicate_scripts() {
    // 重複しがちなスクリプトをチェック
    $duplicate_scripts = array(
        'jquery-ui-core',
        'jquery-ui-widget', 
        'jquery-ui-mouse',
        'jquery-effects-core'
    );
    
    foreach ($duplicate_scripts as $script) {
        if (wp_script_is($script, 'registered') && wp_script_is($script, 'enqueued')) {
            wp_dequeue_script($script);
        }
    }
    
    // Font Awesome重複チェック
    global $wp_scripts;
    $fontawesome_count = 0;
    if (isset($wp_scripts->registered)) {
        foreach ($wp_scripts->registered as $handle => $script) {
            if (strpos($script->src, 'font-awesome') !== false || strpos($script->src, 'fontawesome') !== false) {
                $fontawesome_count++;
                if ($fontawesome_count > 1) {
                    wp_dequeue_script($handle);
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'gi_remove_duplicate_scripts', 100);

/**
 * スクリプト・スタイルの読み込み（最適化版）
 */
function gi_enqueue_scripts() {
    // jQueryを最新版に置き換え（パフォーマンス向上）
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), '3.7.1', true);
    wp_enqueue_script('jquery');
    
    // 最適化されたCSSファイル（外部CDN依存を削除）
    wp_enqueue_style('gi-optimized-css', get_template_directory_uri() . '/assets/css/optimized.css', array(), GI_THEME_VERSION);
    
    // Google Fonts（display=swapでパフォーマンス最適化）
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap', array(), null);
    
    // インラインCSSでCLS対策
    $inline_css = "
        .gi-logo-container { 
            width: 80px; 
            height: 80px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .gi-logo-container img { 
            max-width: 100%; 
            height: auto; 
            width: auto;
        }
        .mobile-menu-overlay { 
            pointer-events: auto !important; 
        }
        .mobile-menu-toggle { 
            pointer-events: auto !important; 
            z-index: 9999; 
        }
    ";
    wp_add_inline_style('gi-optimized-css', $inline_css);
    
    // テーマスタイル
    wp_enqueue_style('gi-style', get_stylesheet_uri(), array('gi-optimized-css'), GI_THEME_VERSION);
    
    // メインJavaScript（最適化版）
    wp_enqueue_script('gi-main-js', get_template_directory_uri() . '/assets/js/main-optimized.js', array('jquery'), GI_THEME_VERSION, true);
    
    // モバイルメニュー専用JavaScript
    wp_enqueue_script('gi-mobile-menu', get_template_directory_uri() . '/assets/js/mobile-menu.js', array('jquery'), GI_THEME_VERSION, true);
    
    // AIチャットボット用の追加スクリプト
    if (is_page_template('page-ai-chat.php')) {
        wp_enqueue_script('ai-chatbot-js', get_template_directory_uri() . '/assets/js/ai-chatbot.js', array('jquery'), GI_THEME_VERSION, true);
        wp_localize_script('ai-chatbot-js', 'ai_chat_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_chat_action'),
            'strings' => array(
                'sending' => '送信中...',
                'error' => 'エラーが発生しました',
                'clear_confirm' => '会話履歴をクリアしてもよろしいですか？'
            )
        ));
    }
    
    // AJAX設定（最適化版）
    wp_localize_script('gi-main-js', 'gi_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gi_ajax_nonce'),
        'homeUrl' => home_url('/'),
        'themeUrl' => get_template_directory_uri(),
        'uploadsUrl' => wp_upload_dir()['baseurl'],
        'isAdmin' => current_user_can('administrator'),
        'userId' => get_current_user_id(),
        'version' => GI_THEME_VERSION,
        'debug' => WP_DEBUG,
        'strings' => array(
            'loading' => '読み込み中...',
            'error' => 'エラーが発生しました',
            'noResults' => '結果が見つかりませんでした',
            'confirm' => '実行してもよろしいですか？'
        )
    ));
    
    // 条件付きスクリプト読み込み
    if (is_singular()) {
        wp_enqueue_script('comment-reply');
    }
    
    if (is_front_page()) {
        wp_enqueue_script('gi-frontend-js', get_template_directory_uri() . '/assets/js/front-page.js', array('gi-main-js'), GI_THEME_VERSION, true);
    }
}
add_action('wp_enqueue_scripts', 'gi_enqueue_scripts');

/**
 * deferとasync属性の追加（重複チェック付き）
 */
function gi_script_attributes($tag, $handle, $src) {
    // 既に処理済みかチェック
    if (strpos($tag, 'defer') !== false || strpos($tag, 'async') !== false) {
        return $tag;
    }
    
    // gi_add_defer_attribute関数が存在し、既に処理している場合はスキップ
    if (function_exists('gi_add_defer_attribute') && $handle !== 'gi-main-js') {
        return gi_add_defer_attribute($tag, $handle, $src);
    }
    
    // deferを追加するスクリプト
    $defer_scripts = array(
        'gi-main-js',
        'gi-frontend-js',
        'gi-mobile-menu',
        'ai-chatbot-js'
    );
    
    // asyncを追加するスクリプト  
    $async_scripts = array(
        'google-fonts'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }
    
    if (in_array($handle, $async_scripts)) {
        return str_replace('<script ', '<script async ', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'gi_script_attributes', 10, 3);

/**
 * プリロード設定（CLS対策）
 */
function gi_add_preload_links() {
    // 重要なフォントをプリロード
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
    echo '<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap"></noscript>';
    
    // ロゴ画像をプリロード（CLS対策）
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'gi-logo-sm');
        if ($logo_url) {
            echo '<link rel="preload" href="' . esc_url($logo_url) . '" as="image">';
        }
    }
}
add_action('wp_head', 'gi_add_preload_links', 1);

/**
 * ウィジェットエリア登録
 */
function gi_widgets_init() {
    register_sidebar(array(
        'name'          => 'メインサイドバー',
        'id'            => 'sidebar-main',
        'description'   => 'メインサイドバーエリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-semibold mb-4 pb-2 border-b-2 border-emerald-500">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => 'フッターエリア1',
        'id'            => 'footer-1',
        'description'   => 'フッター左側エリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title text-white font-semibold mb-4">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => 'フッターエリア2',
        'id'            => 'footer-2',
        'description'   => 'フッター中央エリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title text-white font-semibold mb-4">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => 'フッターエリア3',
        'id'            => 'footer-3',
        'description'   => 'フッター右側エリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title text-white font-semibold mb-4">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'gi_widgets_init');

/**
 * カスタマイザー設定
 */
function gi_customize_register($wp_customize) {
    // サイトカラー設定
    $wp_customize->add_section('gi_colors', array(
        'title' => 'サイトカラー',
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('gi_primary_color', array(
        'default' => '#059669',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'gi_primary_color', array(
        'label' => 'プライマリカラー',
        'section' => 'gi_colors',
    )));
    
    // パフォーマンス設定セクション
    $wp_customize->add_section('gi_performance', array(
        'title' => 'パフォーマンス設定',
        'priority' => 35,
    ));
    
    $wp_customize->add_setting('gi_lazy_loading', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('gi_lazy_loading', array(
        'label' => 'Lazy Loading を有効にする',
        'section' => 'gi_performance',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'gi_customize_register');

/**
 * セキュリティ強化（テーマエディター制限を削除）
 */
function gi_security_enhancements() {
    // WordPressバージョン情報を隠す
    remove_action('wp_head', 'wp_generator');
    
    // RSDリンクを削除
    remove_action('wp_head', 'rsd_link');
    
    // Windows Live Writerサポートを削除
    remove_action('wp_head', 'wlwmanifest_link');
    
    // 絵文字スクリプトを削除（パフォーマンス向上）
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // REST APIヘッダーを削除
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    
    // XMLRPCを無効化
    add_filter('xmlrpc_enabled', '__return_false');
    
    // テーマエディター制限は削除（開発者の要望により）
    // DISALLOW_FILE_EDIT の設定は行わない
}
add_action('init', 'gi_security_enhancements');

/**
 * パフォーマンス最適化
 */
function gi_performance_optimizations() {
    // 不要なクエリ文字列を削除
    if (!function_exists('gi_remove_query_strings')) {
        function gi_remove_query_strings($src) {
            $parts = explode('?ver', $src);
            return $parts[0];
        }
    }
    add_filter('script_loader_src', 'gi_remove_query_strings', 15, 1);
    add_filter('style_loader_src', 'gi_remove_query_strings', 15, 1);
    
    // Gravatarにlazy loading追加
    add_filter('get_avatar', function($avatar) {
        return str_replace('src=', 'loading="lazy" src=', $avatar);
    });
    
    // 画像にwidth/height属性を自動追加（CLS対策）
    add_filter('wp_get_attachment_image_attributes', function($attr, $attachment, $size) {
        if (empty($attr['width']) || empty($attr['height'])) {
            $image_meta = wp_get_attachment_metadata($attachment->ID);
            if (is_array($image_meta) && isset($image_meta['width'], $image_meta['height'])) {
                $attr['width'] = $image_meta['width'];
                $attr['height'] = $image_meta['height'];
            }
        }
        return $attr;
    }, 10, 3);
    
    // DNS プリフェッチ
    add_action('wp_head', function() {
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">';
    }, 1);
}
add_action('init', 'gi_performance_optimizations');

/**
 * 画像最適化フック
 */
function gi_optimize_images() {
    // すべての画像にloading="lazy"を追加
    add_filter('wp_get_attachment_image_attributes', function($attr, $attachment, $size) {
        if (!is_admin() && !wp_is_json_request()) {
            $attr['loading'] = 'lazy';
        }
        return $attr;
    }, 10, 3);
    
    // カスタムロゴの最適化
    add_filter('get_custom_logo', function($html) {
        if (empty($html)) return $html;
        
        // CLSを防ぐためサイズ指定を追加
        $html = str_replace('<img ', '<img loading="eager" ', $html);
        
        return $html;
    });
}
add_action('init', 'gi_optimize_images');

/**
 * モバイルメニュー修正用CSS追加
 */
function gi_mobile_menu_fix() {
    $css = "
    <style>
    /* モバイルメニュー修正 */
    .mobile-menu-overlay {
        pointer-events: auto !important;
        touch-action: auto !important;
    }
    
    .mobile-menu-toggle,
    .mobile-menu-toggle * {
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    
    .mobile-menu-container {
        pointer-events: auto !important;
    }
    
    .mobile-menu-container a,
    .mobile-menu-container button,
    .mobile-menu-container input {
        pointer-events: auto !important;
    }
    
    /* ヘッダー安定化 */
    .site-header {
        min-height: 80px;
    }
    
    .gi-logo-container {
        width: 80px !important;
        height: 80px !important;
    }
    </style>
    ";
    echo $css;
}
add_action('wp_head', 'gi_mobile_menu_fix', 999);

/**
 * 緊急時のCSS/JS修正用フック
 */
function gi_emergency_fixes() {
    // 緊急時にすべてのカスタムJS/CSSを無効化するオプション
    if (isset($_GET['gi_safe_mode']) && $_GET['gi_safe_mode'] === '1') {
        remove_action('wp_enqueue_scripts', 'gi_enqueue_scripts');
        wp_enqueue_style('gi-safe-mode', get_template_directory_uri() . '/assets/css/safe-mode.css', array(), GI_THEME_VERSION);
    }
}
add_action('wp_head', 'gi_emergency_fixes', 1);

/**
 * 管理画面での設定パネル追加
 */
function gi_admin_menu() {
    add_theme_page(
        'Grant Insight 設定',
        'テーマ設定',
        'manage_options',
        'gi-settings',
        'gi_settings_page'
    );
}
add_action('admin_menu', 'gi_admin_menu');

/**
 * 設定ページのHTML
 */
function gi_settings_page() {
    ?>
    <div class="wrap">
        <h1>Grant Insight Perfect 設定</h1>
        <div class="notice notice-info">
            <p><strong>パフォーマンス最適化が適用されました</strong></p>
            <ul>
                <li>✅ JavaScript重複削除</li>
                <li>✅ CLS（レイアウトシフト）対策</li>
                <li>✅ モバイルメニュー修正</li>
                <li>✅ 画像最適化</li>
                <li>✅ セキュリティ強化</li>
            </ul>
        </div>
        
        <h2>緊急時対応</h2>
        <p>問題が発生した場合は、以下のURLでセーフモードを有効化できます：</p>
        <code><?php echo home_url('/?gi_safe_mode=1'); ?></code>
        
        <h2>パフォーマンステスト</h2>
        <p>以下のツールでサイトの速度をテストしてください：</p>
        <ul>
            <li><a href="https://pagespeed.web.dev/" target="_blank">Google PageSpeed Insights</a></li>
            <li><a href="https://gtmetrix.com/" target="_blank">GTmetrix</a></li>
            <li><a href="https://webpagetest.org/" target="_blank">WebPageTest</a></li>
        </ul>
        
        <h2>開発者向け情報</h2>
        <div class="notice notice-success">
            <p><strong>テーマエディターは利用可能です</strong></p>
            <p>「外観 > テーマエディター」からファイルの編集が可能です。</p>
            <p>本番環境では、セキュリティのためFTPやSSHでの編集を推奨します。</p>
        </div>
    </div>
    <?php
}

// エラーハンドリングとログ出力
if (WP_DEBUG) {
    error_log('Grant Insight Perfect theme setup completed - Version: ' . GI_THEME_VERSION);
}
?>
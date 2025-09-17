<?php
/**
 * Ultra Modern Search Section Template - Dark Mode Edition
 * Grant Insight Perfect - ダークモード完全対応版
 * 
 * @version 22.0-dark-mode
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 都道府県データ
$prefectures = array(
    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県',
    '岐阜県', '静岡県', '愛知県', '三重県',
    '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
    '鳥取県', '島根県', '岡山県', '広島県', '山口県',
    '徳島県', '香川県', '愛媛県', '高知県',
    '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
);

// カテゴリとタグの取得
$grant_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'number' => 15
));

$popular_tags = get_terms(array(
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 8
));

// エラーハンドリング
if (is_wp_error($grant_categories)) {
    $grant_categories = array();
}
if (is_wp_error($popular_tags)) {
    $popular_tags = array();
}

// nonce生成
$search_nonce = wp_create_nonce('gi_ajax_nonce');

// トレンドワードデータ
$trend_words = array(
    array('text' => 'IT導入補助金', 'count' => 256),
    array('text' => 'ものづくり補助金', 'count' => 189),
    array('text' => '事業再構築補助金', 'count' => 142),
    array('text' => '小規模事業者持続化補助金', 'count' => 98),
    array('text' => 'DX推進', 'count' => 87),
    array('text' => '創業支援', 'count' => 76),
    array('text' => '雇用調整助成金', 'count' => 65),
    array('text' => 'キャリアアップ助成金', 'count' => 54),
    array('text' => '働き方改革', 'count' => 48),
    array('text' => '省エネ補助金', 'count' => 45),
    array('text' => '人材開発支援', 'count' => 42),
    array('text' => '設備投資', 'count' => 38),
    array('text' => '販路開拓', 'count' => 35),
    array('text' => '研究開発', 'count' => 32),
    array('text' => '事業承継', 'count' => 28),
    array('text' => '海外展開', 'count' => 25),
    array('text' => 'インバウンド対策', 'count' => 22),
    array('text' => 'テレワーク導入', 'count' => 20),
    array('text' => 'サイバーセキュリティ', 'count' => 18),
    array('text' => 'カーボンニュートラル', 'count' => 15),
);
?>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- 🎯 ダークモード対応検索セクション -->
<section id="search-section" class="ultra-modern-search" data-theme="auto" role="search" aria-label="助成金検索">
    
    <!-- ダークモード切替ボタン -->
    <button type="button" class="theme-toggle" aria-label="Toggle dark mode">
        <span class="theme-toggle-light">
            <i class="fas fa-sun"></i>
        </span>
        <span class="theme-toggle-dark">
            <i class="fas fa-moon"></i>
        </span>
    </button>
    
    <!-- ✨ ミニマルヒーローエリア -->
    <div class="minimal-hero">
        <div class="hero-background">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-pattern"></div>
        </div>
        
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="title-line">
                        <span class="title-word">おすすめの</span>
                        <span class="title-word accent">助成金</span>
                        <span class="title-word">に出会える</span>
                    </span>
                    <span class="title-sub">統計アプリ</span>
                </h1>
                
                <!-- メイン検索フォーム -->
                <form id="search-form" class="modern-search-form" role="search">
                    <!-- 隠しフィールド -->
                    <input type="hidden" id="search-nonce" value="<?php echo esc_attr($search_nonce); ?>">
                    <input type="hidden" id="ajax-url" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                    
                    <div class="search-input-group">
                        <div class="search-field-wrapper">
                            <input 
                                type="text" 
                                id="main-search-input" 
                                name="keyword"
                                class="search-field"
                                placeholder="キーワードで検索"
                                autocomplete="off"
                                aria-label="検索キーワードを入力"
                            >
                            <button type="button" class="clear-btn" id="search-clear" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="search-button" id="search-submit">
                            <span class="btn-text">検索</span>
                            <span class="btn-icon"><i class="fas fa-search"></i></span>
                            <div class="btn-loading" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </div>
                </form>
                
                <!-- アクションボタン -->
                <div class="hero-actions">
                    <button type="button" class="action-chip" id="voice-search">
                        <i class="fas fa-microphone"></i>
                        <span>音声検索</span>
                    </button>
                    <button type="button" class="action-chip mobile-only" id="filter-toggle-mobile">
                        <i class="fas fa-sliders-h"></i>
                        <span>詳細検索</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 スマートフィルターパネル -->
    <div class="smart-filters expanded-on-desktop" id="filters-content">
        <div class="filters-container">
            <div class="filters-header">
                <h3 class="filters-title">
                    <i class="fas fa-sliders-h"></i>
                    詳細検索
                </h3>
                <button type="button" class="filter-close mobile-only" id="filter-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="filters-grid">
                <!-- カテゴリ -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-folder"></i>
                        カテゴリ
                    </label>
                    <select id="category-filter" name="category" class="filter-select">
                        <option value="">すべて</option>
                        <?php if (!empty($grant_categories)): ?>
                            <?php foreach ($grant_categories as $category): ?>
                                <option value="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 地域 -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-map-marker-alt"></i>
                        地域
                    </label>
                    <select id="prefecture-filter" name="prefecture" class="filter-select">
                        <option value="">全国</option>
                        <?php foreach ($prefectures as $prefecture): ?>
                            <option value="<?php echo esc_attr($prefecture); ?>">
                                <?php echo esc_html($prefecture); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 金額 -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-yen-sign"></i>
                        金額
                    </label>
                    <select id="amount-filter" name="amount" class="filter-select">
                        <option value="">指定なし</option>
                        <option value="0-100">〜100万円</option>
                        <option value="100-500">100〜500万円</option>
                        <option value="500-1000">500〜1000万円</option>
                        <option value="1000+">1000万円〜</option>
                    </select>
                </div>

                <!-- ステータス -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-circle"></i>
                        状態
                    </label>
                    <select id="status-filter" name="status" class="filter-select">
                        <option value="">すべて</option>
                        <option value="active">募集中</option>
                        <option value="upcoming">募集予定</option>
                        <option value="closed">募集終了</option>
                    </select>
                </div>

                <!-- 難易度 -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-star"></i>
                        難易度
                    </label>
                    <select id="difficulty-filter" name="difficulty" class="filter-select">
                        <option value="">すべて</option>
                        <option value="easy">易しい</option>
                        <option value="normal">普通</option>
                        <option value="hard">難しい</option>
                        <option value="expert">専門的</option>
                    </select>
                </div>

                <!-- 並び順 -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-sort"></i>
                        並び順
                    </label>
                    <select id="sort-filter" name="orderby" class="filter-select">
                        <option value="date_desc">新着順</option>
                        <option value="amount_desc">金額が高い順</option>
                        <option value="deadline_asc">締切が近い順</option>
                        <option value="success_rate_desc">採択率順</option>
                    </select>
                </div>
            </div>
            
            <div class="filter-footer">
                <button type="button" class="reset-btn" id="reset-search">
                    <i class="fas fa-undo"></i>
                    リセット
                </button>
            </div>
        </div>
    </div>

    <!-- 🏷️ トレンドタグ -->
    <div class="trend-tags">
        <div class="tags-container">
            <div class="tags-header">
                <span class="tags-title">
                    <i class="fas fa-fire"></i>
                    トレンドワード
                </span>
            </div>
            <div class="tags-list">
                <?php foreach ($trend_words as $word): ?>
                <button type="button" class="trend-tag keyword-tag" data-tag="<?php echo esc_attr($word['text']); ?>">
                    <span class="tag-text"><?php echo esc_html($word['text']); ?></span>
                    <span class="tag-count"><?php echo esc_html($word['count']); ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 📋 検索結果エリア -->
    <div class="results-area" id="search-results">
        <div class="results-container-wrapper">
            <!-- 結果ヘッダー -->
            <div class="results-header" style="display: none;">
                <div class="results-info" id="results-info">
                    <span class="info-text">検索結果</span>
                </div>
                <div class="results-actions">
                    <div class="view-toggle">
                        <button class="view-btn active" id="grid-view" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" id="list-view" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <button class="export-btn" id="export-results">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>

            <!-- 結果表示エリア -->
            <div class="results-container" id="results-container">
                <!-- 初期状態 -->
                <div class="empty-state">
                    <div class="empty-illustration">
                        <div class="illustration-shape">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <h3 class="empty-title">検索してみましょう</h3>
                    <p class="empty-text">キーワードを入力するか、トレンドワードから選択してください</p>
                </div>
            </div>

            <!-- ページネーション -->
            <div class="pagination-container" id="pagination-container"></div>
        </div>
    </div>

    <!-- ローディング -->
    <div class="modern-loading" id="search-loading" style="display: none;">
        <div class="loading-content">
            <div class="loading-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="loading-text">検索中...</p>
        </div>
    </div>

    <!-- エラー -->
    <div class="modern-error" id="search-error" style="display: none;">
        <div class="error-content">
            <i class="fas fa-exclamation-circle"></i>
            <h3>エラーが発生しました</h3>
            <p id="error-message">検索中に問題が発生しました</p>
            <button class="retry-btn" id="retry-search">
                再試行
            </button>
        </div>
    </div>
</section>

<!-- 🎨 ダークモード対応スタイル -->
<style>
/* CSS変数定義 - ライトモード */
:root {
    /* カラー */
    --primary: #000000;
    --primary-rgb: 0, 0, 0;
    --accent: #FF6B6B;
    --accent-rgb: 255, 107, 107;
    --success: #4ECDC4;
    --warning: #FFE66D;
    --info: #4A90E2;
    
    /* テキスト */
    --text-primary: #1a1a1a;
    --text-secondary: #6b6b6b;
    --text-light: #9a9a9a;
    --text-inverse: #ffffff;
    
    /* 背景 */
    --bg-primary: #ffffff;
    --bg-secondary: #f8f8f8;
    --bg-tertiary: #f0f0f0;
    --bg-card: #ffffff;
    --bg-hover: #f8f8f8;
    --bg-gradient: linear-gradient(180deg, #ffffff 0%, #f8f8f8 100%);
    
    /* ボーダー */
    --border: #e0e0e0;
    --border-light: #f0f0f0;
    
    /* シャドウ */
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.12);
    --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.16);
    
    /* エフェクト */
    --orb-opacity: 0.3;
    --grid-opacity: 0.5;
    --grid-color: rgba(0, 0, 0, 0.03);
    --overlay-bg: rgba(255, 255, 255, 0.95);
    
    /* その他 */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;
    --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ダークモード変数 */
[data-theme="dark"] {
    /* カラー */
    --primary: #ffffff;
    --primary-rgb: 255, 255, 255;
    --accent: #ff8787;
    --accent-rgb: 255, 135, 135;
    --success: #5edcd5;
    --warning: #ffe066;
    --info: #60a5fa;
    
    /* テキスト */
    --text-primary: #f0f0f0;
    --text-secondary: #a0a0a0;
    --text-light: #707070;
    --text-inverse: #1a1a1a;
    
    /* 背景 */
    --bg-primary: #1a1a1a;
    --bg-secondary: #0f0f0f;
    --bg-tertiary: #2a2a2a;
    --bg-card: #1e1e1e;
    --bg-hover: #2a2a2a;
    --bg-gradient: linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 100%);
    
    /* ボーダー */
    --border: #2a2a2a;
    --border-light: #1f1f1f;
    
    /* シャドウ */
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.4);
    --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.5);
    
    /* エフェクト */
    --orb-opacity: 0.1;
    --grid-opacity: 0.3;
    --grid-color: rgba(255, 255, 255, 0.02);
    --overlay-bg: rgba(0, 0, 0, 0.95);
}

/* システムのダークモード設定に従う */
@media (prefers-color-scheme: dark) {
    [data-theme="auto"] {
        /* カラー */
        --primary: #ffffff;
        --primary-rgb: 255, 255, 255;
        --accent: #ff8787;
        --accent-rgb: 255, 135, 135;
        --success: #5edcd5;
        --warning: #ffe066;
        --info: #60a5fa;
        
        /* テキスト */
        --text-primary: #f0f0f0;
        --text-secondary: #a0a0a0;
        --text-light: #707070;
        --text-inverse: #1a1a1a;
        
        /* 背景 */
        --bg-primary: #1a1a1a;
        --bg-secondary: #0f0f0f;
        --bg-tertiary: #2a2a2a;
        --bg-card: #1e1e1e;
        --bg-hover: #2a2a2a;
        --bg-gradient: linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 100%);
        
        /* ボーダー */
        --border: #2a2a2a;
        --border-light: #1f1f1f;
        
        /* シャドウ */
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.3);
        --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.4);
        --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.5);
        
        /* エフェクト */
        --orb-opacity: 0.1;
        --grid-opacity: 0.3;
        --grid-color: rgba(255, 255, 255, 0.02);
        --overlay-bg: rgba(0, 0, 0, 0.95);
    }
}

/* ベース設定 */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.ultra-modern-search {
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text-primary);
    background: var(--bg-primary);
    position: relative;
    overflow: hidden;
    transition: background var(--transition), color var(--transition);
}

/* テーマ切替ボタン */
.theme-toggle {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--bg-card);
    border: 2px solid var(--border);
    box-shadow: var(--shadow-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
}

.theme-toggle:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-lg);
}

.theme-toggle-light,
.theme-toggle-dark {
    position: absolute;
    font-size: 20px;
    color: var(--text-primary);
    transition: opacity var(--transition), transform var(--transition);
}

.theme-toggle-light {
    opacity: 1;
    transform: rotate(0deg);
}

.theme-toggle-dark {
    opacity: 0;
    transform: rotate(180deg);
}

[data-theme="dark"] .theme-toggle-light {
    opacity: 0;
    transform: rotate(180deg);
}

[data-theme="dark"] .theme-toggle-dark {
    opacity: 1;
    transform: rotate(0deg);
}

/* ミニマルヒーロー */
.minimal-hero {
    position: relative;
    padding: 80px 20px 40px;
    background: var(--bg-gradient);
    overflow: hidden;
    transition: background var(--transition);
}

.hero-background {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: var(--orb-opacity);
    animation: float 20s ease-in-out infinite;
    transition: opacity var(--transition);
}

.orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    top: -200px;
    left: -100px;
}

[data-theme="dark"] .orb-1 {
    background: linear-gradient(135deg, #4c1d95 0%, #5b21b6 100%);
}

.orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    bottom: -150px;
    right: -50px;
    animation-delay: -5s;
}

[data-theme="dark"] .orb-2 {
    background: linear-gradient(135deg, #831843 0%, #be123c 100%);
}

.orb-3 {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: -10s;
}

[data-theme="dark"] .orb-3 {
    background: linear-gradient(135deg, #1e3a8a 0%, #0c4a6e 100%);
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    33% {
        transform: translateY(-30px) rotate(120deg);
    }
    66% {
        transform: translateY(30px) rotate(240deg);
    }
}

.grid-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(var(--grid-color) 1px, transparent 1px),
        linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: var(--grid-opacity);
    transition: opacity var(--transition);
}

.hero-container {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-content {
    text-align: center;
}

.hero-title {
    margin-bottom: 40px;
    animation: fadeInUp 0.8s ease;
}

.title-line {
    display: block;
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 900;
    line-height: 1.2;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    color: var(--text-primary);
}

.title-word {
    display: inline-block;
    margin: 0 4px;
}

.title-word.accent {
    background: linear-gradient(135deg, var(--accent), #ff8787);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

[data-theme="dark"] .title-word.accent {
    background: linear-gradient(135deg, #ff8787, #ffa5a5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.title-sub {
    display: block;
    font-size: 18px;
    font-weight: 500;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
}

/* モダン検索フォーム */
.modern-search-form {
    margin-bottom: 24px;
    animation: fadeInUp 0.8s ease 0.1s both;
}

.search-input-group {
    display: flex;
    gap: 12px;
    max-width: 600px;
    margin: 0 auto;
}

.search-field-wrapper {
    flex: 1;
    position: relative;
}

.search-field {
    width: 100%;
    height: 56px;
    padding: 0 48px 0 24px;
    border: 2px solid var(--border);
    border-radius: var(--radius-full);
    font-size: 16px;
    font-weight: 500;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: var(--transition);
    outline: none;
}

.search-field:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
}

.search-field::placeholder {
    color: var(--text-light);
}

.clear-btn {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border: none;
    background: var(--bg-tertiary);
    border-radius: 50%;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.clear-btn:hover {
    background: var(--primary);
    color: var(--text-inverse);
}

.search-button {
    height: 56px;
    padding: 0 32px;
    background: var(--primary);
    color: var(--text-inverse);
    border: none;
    border-radius: var(--radius-full);
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
}

.search-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(var(--primary-rgb), 0.25);
}

.search-button:active {
    transform: translateY(0);
}

.btn-loading {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary);
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(var(--primary-rgb), 0.3);
    border-top-color: var(--text-inverse);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* アクションチップ */
.hero-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    animation: fadeInUp 0.8s ease 0.2s both;
}

.action-chip {
    padding: 10px 20px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-full);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-chip:hover {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.action-chip i {
    font-size: 14px;
}

/* モバイル専用要素 */
.mobile-only {
    display: none;
}

/* スマートフィルター */
.smart-filters {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-light);
}

.smart-filters.expanded,
.smart-filters.expanded-on-desktop {
    max-height: 500px;
    border-bottom: 1px solid var(--border-light);
}

/* PC表示時は最初から開く */
@media (min-width: 769px) {
    .smart-filters.expanded-on-desktop {
        max-height: 500px !important;
        overflow: visible !important;
    }
}

.filters-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 20px;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.filters-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.filters-title i {
    color: var(--text-secondary);
    font-size: 14px;
}

.filter-close {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.filter-close:hover {
    background: var(--bg-tertiary);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-label i {
    font-size: 12px;
    opacity: 0.6;
}

.filter-select {
    height: 44px;
    padding: 0 16px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%236b6b6b' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 40px;
}

[data-theme="dark"] .filter-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23a0a0a0' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.filter-footer {
    display: flex;
    justify-content: center;
}

.reset-btn {
    padding: 10px 24px;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: var(--radius-full);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.reset-btn:hover {
    background: var(--bg-primary);
    border-color: var(--primary);
    color: var(--primary);
}

/* トレンドタグ */
.trend-tags {
    padding: 32px 20px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-light);
    transition: background var(--transition);
}

.tags-container {
    max-width: 1200px;
    margin: 0 auto;
}

.tags-header {
    margin-bottom: 20px;
}

.tags-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tags-title i {
    color: var(--accent);
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.trend-tag {
    padding: 12px 20px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-full);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
}

.trend-tag:hover {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.trend-tag.active {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
}

.tag-count {
    padding: 2px 8px;
    background: rgba(var(--accent-rgb), 0.1);
    color: var(--accent);
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 600;
}

.trend-tag:hover .tag-count,
.trend-tag.active .tag-count {
    background: rgba(var(--primary-rgb), 0.2);
    color: var(--text-inverse);
}

/* 結果エリア */
.results-area {
    min-height: 400px;
    padding: 40px 20px;
    background: var(--bg-secondary);
    transition: background var(--transition);
}

.results-container-wrapper {
    max-width: 1200px;
    margin: 0 auto;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 20px;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.results-info {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
}

.results-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.view-toggle {
    display: flex;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 4px;
}

.view-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
}

.view-btn.active {
    background: var(--primary);
    color: var(--text-inverse);
}

.export-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.export-btn:hover {
    background: var(--primary);
    color: var(--text-inverse);
}

/* 結果コンテナ */
.results-container {
    display: grid;
    gap: 24px;
    margin-bottom: 40px;
}

.results-container.grid-view {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
}

.results-container.list-view {
    grid-template-columns: 1fr;
}

/* 空状態 */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
}

.empty-illustration {
    margin-bottom: 32px;
}

.illustration-shape {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: var(--text-light);
}

.empty-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.empty-text {
    font-size: 16px;
    color: var(--text-secondary);
    max-width: 400px;
    margin: 0 auto;
}

/* ページネーション */
.pagination-container {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.pagination-btn {
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-btn:hover {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
}

.pagination-btn.active {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
}

/* ローディング */
.modern-loading {
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    backdrop-filter: blur(10px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-content {
    text-align: center;
}

.loading-dots {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-bottom: 16px;
}

.loading-dots span {
    width: 12px;
    height: 12px;
    background: var(--primary);
    border-radius: 50%;
    animation: bounce 1.4s ease-in-out infinite both;
}

.loading-dots span:nth-child(1) { animation-delay: -0.32s; }
.loading-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

.loading-text {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
}

/* エラー */
.modern-error {
    position: fixed;
    inset: 0;
    background: var(--overlay-bg);
    backdrop-filter: blur(10px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-content {
    text-align: center;
    padding: 40px;
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    max-width: 400px;
}

.error-content i {
    font-size: 48px;
    color: var(--accent);
    margin-bottom: 24px;
}

.error-content h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.error-content p {
    font-size: 14px;
    color: var(--text-secondary);
    margin-bottom: 24px;
}

.retry-btn {
    padding: 12px 32px;
    background: var(--primary);
    color: var(--text-inverse);
    border: none;
    border-radius: var(--radius-full);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.retry-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* アニメーション */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* トランジション */
* {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-duration: 0.3s;
    transition-timing-function: ease;
}

/* レスポンシブ */
@media (max-width: 768px) {
    .minimal-hero {
        padding: 60px 16px 32px;
    }
    
    .theme-toggle {
        width: 40px;
        height: 40px;
        top: 10px;
        right: 10px;
    }
    
    .theme-toggle-light,
    .theme-toggle-dark {
        font-size: 16px;
    }
    
    .title-line {
        font-size: 28px;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .search-button {
        width: 100%;
    }
    
    .hero-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .action-chip {
        width: 100%;
        justify-content: center;
    }
    
    .mobile-only {
        display: flex;
    }
    
    /* モバイル時はフィルターを閉じた状態に */
    .smart-filters.expanded-on-desktop {
        max-height: 0;
        overflow: hidden;
    }
    
    .smart-filters.expanded {
        max-height: 600px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .tags-list {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 8px;
        -webkit-overflow-scrolling: touch;
    }
    
    .trend-tag {
        flex-shrink: 0;
    }
    
    .results-container.grid-view {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .title-line {
        font-size: 24px;
    }
    
    .title-sub {
        font-size: 14px;
    }
    
    .search-field {
        font-size: 14px;
        height: 48px;
    }
    
    .search-button {
        height: 48px;
        font-size: 14px;
    }
}
</style>

<!-- 🚀 JavaScript（ダークモード対応） -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // DOM要素の取得
    const searchSection = document.querySelector('.ultra-modern-search');
    const themeToggle = document.querySelector('.theme-toggle');
    const searchForm = document.getElementById('search-form');
    const mainSearchInput = document.getElementById('main-search-input');
    const searchClear = document.getElementById('search-clear');
    const voiceSearchBtn = document.getElementById('voice-search');
    const searchSubmitBtn = document.getElementById('search-submit');
    const resetBtn = document.getElementById('reset-search');
    const searchLoading = document.getElementById('search-loading');
    const searchError = document.getElementById('search-error');
    const searchResults = document.getElementById('search-results');
    const resultsContainer = document.getElementById('results-container');
    const resultsInfo = document.getElementById('results-info');
    const paginationContainer = document.getElementById('pagination-container');
    const keywordTags = document.querySelectorAll('.keyword-tag');
    const gridViewBtn = document.getElementById('grid-view');
    const listViewBtn = document.getElementById('list-view');
    const exportBtn = document.getElementById('export-results');
    const filterToggleMobile = document.getElementById('filter-toggle-mobile');
    const filterClose = document.getElementById('filter-close');
    const filtersContent = document.getElementById('filters-content');

    // フィルター要素
    const categoryFilter = document.getElementById('category-filter');
    const prefectureFilter = document.getElementById('prefecture-filter');
    const amountFilter = document.getElementById('amount-filter');
    const statusFilter = document.getElementById('status-filter');
    const difficultyFilter = document.getElementById('difficulty-filter');
    const sortFilter = document.getElementById('sort-filter');

    // 設定値
    const CONFIG = {
        debounceDelay: 300,
        apiUrl: document.getElementById('ajax-url')?.value || '/wp-admin/admin-ajax.php',
        nonce: document.getElementById('search-nonce')?.value || '',
        resultsPerPage: 12
    };

    // 状態管理
    let currentSearchParams = {};
    let currentPage = 1;
    let currentView = 'grid';
    let searchHistory = JSON.parse(localStorage.getItem('search_history') || '[]');
    let isVoiceListening = false;
    let debounceTimer = null;
    let abortController = null;

    // ダークモード管理
    initDarkMode();

    function initDarkMode() {
        // ローカルストレージから設定を読み込み
        const savedTheme = localStorage.getItem('search-theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // 初期テーマ設定
        if (savedTheme) {
            searchSection.setAttribute('data-theme', savedTheme);
        } else if (systemPrefersDark) {
            searchSection.setAttribute('data-theme', 'dark');
        } else {
            searchSection.setAttribute('data-theme', 'light');
        }
        
        // テーマ切替
        themeToggle.addEventListener('click', function() {
            const currentTheme = searchSection.getAttribute('data-theme');
            let newTheme;
            
            if (currentTheme === 'light' || (currentTheme === 'auto' && !systemPrefersDark)) {
                newTheme = 'dark';
            } else {
                newTheme = 'light';
            }
            
            searchSection.setAttribute('data-theme', newTheme);
            localStorage.setItem('search-theme', newTheme);
            
            // アニメーション
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
        
        // システムテーマ変更監視
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (searchSection.getAttribute('data-theme') === 'auto') {
                // 自動モードの場合のみ反応
            }
        });
    }

    // 初期化
    init();

    function init() {
        setupEventListeners();
        console.log('🌙 ダークモード対応検索システム初期化完了');
    }

    // イベントリスナー設定
    function setupEventListeners() {
        // フォーム送信
        if (searchForm) {
            searchForm.addEventListener('submit', handleFormSubmit);
        }

        // 検索入力
        if (mainSearchInput) {
            mainSearchInput.addEventListener('input', handleSearchInput);
        }

        // クリアボタン
        if (searchClear) {
            searchClear.addEventListener('click', clearSearch);
        }

        // 音声検索
        if (voiceSearchBtn) {
            voiceSearchBtn.addEventListener('click', handleVoiceSearch);
        }

        // リセットボタン
        if (resetBtn) {
            resetBtn.addEventListener('click', resetSearch);
        }

        // モバイル用フィルタートグル
        if (filterToggleMobile) {
            filterToggleMobile.addEventListener('click', toggleFiltersMobile);
        }

        // フィルター閉じるボタン
        if (filterClose) {
            filterClose.addEventListener('click', closeFiltersMobile);
        }

        // キーワードタグ
        keywordTags.forEach(tag => {
            tag.addEventListener('click', handleTagClick);
        });

        // フィルター変更
        const filters = [categoryFilter, prefectureFilter, amountFilter, statusFilter, difficultyFilter, sortFilter];
        filters.forEach(filter => {
            if (filter) {
                filter.addEventListener('change', handleFilterChange);
            }
        });

        // ビュー切り替え
        if (gridViewBtn) gridViewBtn.addEventListener('click', () => switchView('grid'));
        if (listViewBtn) listViewBtn.addEventListener('click', () => switchView('list'));

        // エクスポート
        if (exportBtn) exportBtn.addEventListener('click', exportResults);

        // エラー再試行
        const retryBtn = document.getElementById('retry-search');
        if (retryBtn) retryBtn.addEventListener('click', retrySearch);
    }

    // 既存の関数はそのまま維持（以下省略部分も同様）
    // ... 既存のJavaScript関数をすべて含める ...

    // モバイル用フィルタートグル
    function toggleFiltersMobile() {
        if (!filtersContent) return;
        
        const isExpanded = filtersContent.classList.contains('expanded');
        
        if (isExpanded) {
            filtersContent.classList.remove('expanded');
        } else {
            filtersContent.classList.add('expanded');
        }
    }

    // モバイル用フィルター閉じる
    function closeFiltersMobile() {
        if (!filtersContent) return;
        filtersContent.classList.remove('expanded');
    }

    // フォーム送信処理
    async function handleFormSubmit(event) {
        event.preventDefault();
        
        if (searchSubmitBtn && searchSubmitBtn.disabled) return;
        
        const searchData = collectFormData();
        if (!validateSearchData(searchData)) return;
        
        try {
            await performSearch(searchData, 1);
            addToSearchHistory(searchData);
        } catch (error) {
            console.error('検索エラー:', error);
            showError('検索中にエラーが発生しました。');
        }
    }

    // 以下、既存の全関数を含める
    function collectFormData() {
        return {
            search: mainSearchInput ? mainSearchInput.value.trim() : '',
            categories: categoryFilter ? [categoryFilter.value].filter(Boolean) : [],
            prefectures: prefectureFilter ? [prefectureFilter.value].filter(Boolean) : [],
            amount: amountFilter ? amountFilter.value : '',
            status: statusFilter ? [statusFilter.value].filter(Boolean) : [],
            difficulty: difficultyFilter ? [difficultyFilter.value].filter(Boolean) : [],
            sort: sortFilter ? sortFilter.value : 'date_desc',
            nonce: CONFIG.nonce
        };
    }

    function validateSearchData(data) {
        if (!data.search && !data.categories.length && !data.prefectures.length && 
            !data.amount && !data.status.length && !data.difficulty.length) {
            showToast('検索キーワードまたはフィルター条件を指定してください', 'warning');
            return false;
        }
        return true;
    }

    async function performSearch(searchData, page = 1) {
        if (abortController) {
            abortController.abort();
        }
        
        abortController = new AbortController();
        setLoadingState(true);
        currentPage = page;
        currentSearchParams = { ...searchData, page };

        const resultsHeader = document.querySelector('.results-header');
        if (resultsHeader) {
            resultsHeader.style.display = 'flex';
        }

        try {
            const formData = new FormData();
            formData.append('action', 'gi_load_grants');
            formData.append('search', searchData.search);
            formData.append('categories', JSON.stringify(searchData.categories));
            formData.append('prefectures', JSON.stringify(searchData.prefectures));
            formData.append('amount', searchData.amount);
            formData.append('status', JSON.stringify(searchData.status));
            formData.append('difficulty', JSON.stringify(searchData.difficulty));
            formData.append('sort', searchData.sort);
            formData.append('view', currentView);
            formData.append('page', page);
            formData.append('nonce', CONFIG.nonce);

            const response = await fetch(CONFIG.apiUrl, {
                method: 'POST',
                body: formData,
                signal: abortController.signal
            });

            const data = await response.json();

            if (data.success) {
                displayResults(data.data);
            } else {
                throw new Error(data.data || '検索に失敗しました');
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            console.error('検索エラー:', error);
            showError(error.message || '検索中にエラーが発生しました。');
        } finally {
            setLoadingState(false);
        }
    }

    // その他の既存関数をすべて含める
    function displayResults(data) {
        if (!data || !data.grants) {
            showError('検索結果の取得に失敗しました。');
            return;
        }

        updateResultsInfo(data);
        renderResults(data.grants);
        renderPagination(data.pagination);

        if (searchResults) {
            searchResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function updateResultsInfo(data) {
        if (!resultsInfo) return;
        
        const total = data.found_posts || 0;
        const start = ((currentPage - 1) * CONFIG.resultsPerPage) + 1;
        const end = Math.min(start + CONFIG.resultsPerPage - 1, total);
        
        resultsInfo.innerHTML = `
            <span class="info-text">
                <strong>${total.toLocaleString('ja-JP')}</strong>件中 
                ${start.toLocaleString('ja-JP')}-${end.toLocaleString('ja-JP')}件を表示
            </span>
        `;
    }

    function renderResults(grants) {
        if (!resultsContainer) return;

        if (!grants || grants.length === 0) {
            resultsContainer.innerHTML = `
                <div class="empty-state">
                    <div class="empty-illustration">
                        <div class="illustration-shape">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <h3 class="empty-title">該当する助成金が見つかりませんでした</h3>
                    <p class="empty-text">検索条件を変更して再度お試しください</p>
                </div>
            `;
            return;
        }

        resultsContainer.className = `results-container ${currentView}-view`;

        let html = '';
        grants.forEach(grant => {
            if (grant.html) {
                html += grant.html;
            }
        });
        
        resultsContainer.innerHTML = html;
        animateCards();
        initializeCardEvents();
    }

    function initializeCardEvents() {
        const favoriteButtons = document.querySelectorAll('.favorite-btn:not([data-initialized])');
        favoriteButtons.forEach(button => {
            button.dataset.initialized = 'true';
            button.addEventListener('click', function(e) {
                e.preventDefault();
                handleFavoriteToggle(this);
            });
        });

        const shareButtons = document.querySelectorAll('.share-btn:not([data-initialized])');
        shareButtons.forEach(button => {
            button.dataset.initialized = 'true';
            button.addEventListener('click', function(e) {
                e.preventDefault();
                handleShare(this);
            });
        });
    }

    async function handleFavoriteToggle(button) {
        const postId = button.dataset.postId;
        if (!postId) return;

        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);

        try {
            const formData = new FormData();
            formData.append('action', 'gi_toggle_favorite');
            formData.append('post_id', postId);
            formData.append('nonce', CONFIG.nonce);

            const response = await fetch(CONFIG.apiUrl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                const heartIcon = button.querySelector('i');
                if (heartIcon) {
                    heartIcon.className = data.data.is_favorite ? 'fas fa-heart' : 'far fa-heart';
                    button.style.color = data.data.is_favorite ? '#ef4444' : 'var(--text-secondary)';
                }
                showToast(data.data.message, 'success');
            } else {
                throw new Error(data.data || 'お気に入りの切り替えに失敗しました');
            }
        } catch (error) {
            console.error('お気に入りエラー:', error);
            showToast('お気に入りの切り替えに失敗しました', 'error');
        }
    }

    function handleShare(button) {
        const url = button.dataset.url;
        const title = button.dataset.title;
        
        if (!url) return;

        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(err => console.log('共有エラー:', err));
        } else {
            navigator.clipboard.writeText(url).then(() => {
                showToast('URLをコピーしました', 'success');
            }).catch(err => {
                showToast('コピーに失敗しました', 'error');
                console.log('コピーエラー:', err);
            });
        }
    }

    function renderPagination(pagination) {
        if (!paginationContainer || !pagination || pagination.total_pages <= 1) {
            if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }
            return;
        }

        const { current_page, total_pages } = pagination;
        let html = '';

        if (current_page > 1) {
            html += `
                <button class="pagination-btn" data-page="${current_page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
        }

        const startPage = Math.max(1, current_page - 2);
        const endPage = Math.min(total_pages, current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === current_page;
            html += `
                <button class="pagination-btn ${isActive ? 'active' : ''}" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        if (current_page < total_pages) {
            html += `
                <button class="pagination-btn" data-page="${current_page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        }

        paginationContainer.innerHTML = html;

        paginationContainer.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const page = parseInt(e.target.closest('.pagination-btn').dataset.page);
                if (page && page !== currentPage) {
                    await performSearch(currentSearchParams, page);
                }
            });
        });
    }

    function handleSearchInput() {
        if (!mainSearchInput || !searchClear) return;
        
        const value = mainSearchInput.value.trim();
        searchClear.style.display = value ? 'block' : 'none';
    }

    function clearSearch() {
        if (!mainSearchInput || !searchClear) return;
        
        mainSearchInput.value = '';
        searchClear.style.display = 'none';
        mainSearchInput.focus();
    }

    function handleVoiceSearch() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            showToast('音声認識がサポートされていません', 'error');
            return;
        }

        if (isVoiceListening) {
            stopVoiceRecognition();
            return;
        }

        startVoiceRecognition();
    }

    function startVoiceRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();

        recognition.lang = 'ja-JP';
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onstart = () => {
            isVoiceListening = true;
            if (voiceSearchBtn) {
                voiceSearchBtn.classList.add('listening');
            }
            showToast('音声を聞き取り中...', 'info');
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            if (mainSearchInput) {
                mainSearchInput.value = transcript;
                handleSearchInput();
            }
            showToast(`「${transcript}」と認識しました`, 'success');
        };

        recognition.onerror = (event) => {
            showToast('音声認識エラーが発生しました', 'error');
            stopVoiceRecognition();
        };

        recognition.onend = () => {
            stopVoiceRecognition();
        };

        recognition.start();
    }

    function stopVoiceRecognition() {
        isVoiceListening = false;
        if (voiceSearchBtn) {
            voiceSearchBtn.classList.remove('listening');
        }
    }

    function resetSearch() {
        if (searchForm) {
            searchForm.reset();
        }
        
        if (mainSearchInput) {
            mainSearchInput.value = '';
        }
        
        if (searchClear) {
            searchClear.style.display = 'none';
        }

        keywordTags.forEach(tag => tag.classList.remove('active'));

        if (window.innerWidth <= 768 && filtersContent) {
            filtersContent.classList.remove('expanded');
        }

        if (resultsContainer) {
            resultsContainer.innerHTML = `
                <div class="empty-state">
                    <div class="empty-illustration">
                        <div class="illustration-shape">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <h3 class="empty-title">検索してみましょう</h3>
                    <p class="empty-text">キーワードを入力するか、トレンドワードから選択してください</p>
                </div>
            `;
        }

        const resultsHeader = document.querySelector('.results-header');
        if (resultsHeader) {
            resultsHeader.style.display = 'none';
        }

        if (mainSearchInput) {
            mainSearchInput.focus();
        }

        showToast('検索条件をリセットしました', 'success');
    }

    function handleTagClick(event) {
        const tag = event.target.closest('.keyword-tag');
        if (!tag) return;

        const tagValue = tag.dataset.tag;
        tag.classList.toggle('active');

        if (mainSearchInput && tag.classList.contains('active')) {
            mainSearchInput.value = tagValue;
            handleSearchInput();
        }

        const searchData = collectFormData();
        if (validateSearchData(searchData)) {
            performSearch(searchData, 1);
        }
    }

    function handleFilterChange() {
        if ((mainSearchInput && mainSearchInput.value.trim()) || hasActiveFilters()) {
            const searchData = collectFormData();
            if (validateSearchData(searchData)) {
                performSearch(searchData, 1);
            }
        }
    }

    function hasActiveFilters() {
        const filters = [categoryFilter, prefectureFilter, amountFilter, statusFilter, difficultyFilter];
        return filters.some(filter => filter && filter.value);
    }

    function switchView(viewType) {
        if (currentView === viewType) return;

        currentView = viewType;

        if (gridViewBtn) {
            gridViewBtn.classList.toggle('active', viewType === 'grid');
        }
        if (listViewBtn) {
            listViewBtn.classList.toggle('active', viewType === 'list');
        }

        if (resultsContainer && resultsContainer.children.length > 0) {
            resultsContainer.className = `results-container ${viewType}-view`;

            if (currentSearchParams && Object.keys(currentSearchParams).length > 0) {
                performSearch(currentSearchParams, currentPage);
            }
        }
    }

    function exportResults() {
        if (!currentSearchParams || Object.keys(currentSearchParams).length === 0) {
            showToast('エクスポートする検索結果がありません', 'warning');
            return;
        }

        showToast('エクスポート機能は開発中です', 'info');
    }

    function retrySearch() {
        if (searchError) {
            searchError.style.display = 'none';
        }

        if (currentSearchParams && Object.keys(currentSearchParams).length > 0) {
            performSearch(currentSearchParams, currentPage);
        }
    }

    function setLoadingState(isLoading) {
        if (!searchSubmitBtn) return;

        if (isLoading) {
            searchSubmitBtn.disabled = true;
            const btnText = searchSubmitBtn.querySelector('.btn-text');
            const btnIcon = searchSubmitBtn.querySelector('.btn-icon');
            const btnLoading = searchSubmitBtn.querySelector('.btn-loading');
            
            if (btnText) btnText.style.display = 'none';
            if (btnIcon) btnIcon.style.display = 'none';
            if (btnLoading) btnLoading.style.display = 'flex';
            if (searchLoading) searchLoading.style.display = 'flex';
        } else {
            searchSubmitBtn.disabled = false;
            const btnText = searchSubmitBtn.querySelector('.btn-text');
            const btnIcon = searchSubmitBtn.querySelector('.btn-icon');
            const btnLoading = searchSubmitBtn.querySelector('.btn-loading');
            
            if (btnText) btnText.style.display = 'inline';
            if (btnIcon) btnIcon.style.display = 'inline';
            if (btnLoading) btnLoading.style.display = 'none';
            if (searchLoading) searchLoading.style.display = 'none';
        }
    }

    function showError(message) {
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
        if (searchError) {
            searchError.style.display = 'flex';
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.textContent = message;

        const colors = {
            info: '#4A90E2',
            success: '#4ECDC4',
            warning: '#FFE66D',
            error: '#FF6B6B'
        };

        toast.style.cssText = `
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: ${colors[type] || colors.info};
            color: white;
            padding: 1rem 2rem;
            border-radius: 9999px;
            font-weight: 500;
            z-index: 10000;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
            max-width: 90vw;
            text-align: center;
            font-size: 14px;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideDown 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    function animateCards() {
        const cards = document.querySelectorAll('.grant-card-enhanced, .grant-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 60);
        });
    }

    function addToSearchHistory(searchData) {
        const historyItem = {
            keyword: searchData.search,
            categories: searchData.categories,
            prefectures: searchData.prefectures,
            timestamp: Date.now()
        };

        searchHistory = searchHistory.filter(item =>
            item.keyword !== historyItem.keyword ||
            JSON.stringify(item.categories) !== JSON.stringify(historyItem.categories)
        );

        searchHistory.unshift(historyItem);
        searchHistory = searchHistory.slice(0, 10);

        localStorage.setItem('search_history', JSON.stringify(searchHistory));
    }
});

// CSS アニメーション追加
const animationStyle = document.createElement('style');
animationStyle.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }

    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        to {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
    }

    .action-chip.listening {
        background: var(--accent) !important;
        color: white !important;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7);
        }
        70% {
            box-shadow: 0 0 0 20px rgba(255, 107, 107, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 107, 107, 0);
        }
    }
`;
document.head.appendChild(animationStyle);
</script>

<?php
/**
 * Ultra Modern Grant Archive Template - Professional Edition
 * Grant Insight Perfect - Next Generation Archive Design
 * 
 * Features:
 * - Unified card rendering with ajax-functions.php
 * - Fixed view switching (grid/list)
 * - Professional minimalist design
 * - Mobile optimization
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// URLパラメータから検索条件を取得
$initial_search = sanitize_text_field($_GET['search'] ?? '');
$initial_category = sanitize_text_field($_GET['category'] ?? '');
$initial_prefecture = sanitize_text_field($_GET['prefecture'] ?? '');
$initial_amount = sanitize_text_field($_GET['amount'] ?? '');
$initial_status = sanitize_text_field($_GET['status'] ?? '');
$initial_orderby = sanitize_text_field($_GET['orderby'] ?? 'date_desc');

// JavaScriptに渡すための初期値
$initial_params = array(
    'search' => $initial_search,
    'category' => $initial_category,
    'prefecture' => $initial_prefecture,
    'amount' => $initial_amount,
    'status' => $initial_status,
    'orderby' => $initial_orderby
);

// 統計データ取得
$total_grants = wp_count_posts('grant')->publish;
$active_grants = get_posts(array(
    'post_type' => 'grant',
    'meta_query' => array(
        array(
            'key' => 'application_status',
            'value' => 'open',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$prefecture_count = wp_count_terms(array('taxonomy' => 'grant_prefecture', 'hide_empty' => false));

get_header(); 
?>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- 🎯 ウルトラモダンアーカイブ -->
<div class="ultra-modern-archive">
    
    <!-- ✨ ミニマルヒーロー -->
    <section class="minimal-hero-section">
        <div class="hero-background">
            <div class="gradient-mesh mesh-1"></div>
            <div class="gradient-mesh mesh-2"></div>
            <div class="gradient-mesh mesh-3"></div>
            <div class="geometric-pattern"></div>
        </div>
        
        <div class="hero-container">
            <div class="hero-content">
                <!-- バッジ -->
                <div class="hero-badge">
                    <i class="fas fa-coins"></i>
                    <span>Grant Database</span>
                </div>
                
                <!-- タイトル -->
                <h1 class="hero-title">
                    <span class="title-main">助成金・補助金</span>
                    <span class="title-sub">データベース</span>
                </h1>
                
                <!-- 説明 -->
                <p class="hero-description">
                    <?php if ($initial_search || $initial_category || $initial_prefecture): ?>
                        検索条件に該当する助成金を表示しています
                    <?php else: ?>
                        あなたのビジネスに最適な助成金・補助金を見つけましょう
                    <?php endif; ?>
                </p>
                
                <!-- 統計 -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($total_grants); ?></div>
                        <div class="stat-label">総助成金数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format(count($active_grants)); ?></div>
                        <div class="stat-label">募集中</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($prefecture_count); ?></div>
                        <div class="stat-label">対象地域</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 🔍 検索・フィルターセクション -->
    <section class="search-filter-section">
        <div class="section-container">
            
            <!-- 検索バー -->
            <div class="search-bar-wrapper">
                <div class="search-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input 
                        type="text" 
                        id="grant-search" 
                        class="search-input"
                        placeholder="キーワードで検索..."
                        value="<?php echo esc_attr($initial_search); ?>"
                    >
                    <button id="search-clear" class="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                    <button id="search-btn" class="search-button">
                        検索
                    </button>
                </div>
            </div>

            <!-- クイックフィルター -->
            <div class="quick-filters">
                <button class="quick-filter-btn <?php echo empty($initial_status) ? 'active' : ''; ?>" data-filter="all">
                    すべて
                </button>
                <button class="quick-filter-btn <?php echo $initial_status === 'active' ? 'active' : ''; ?>" data-filter="active">
                    <span class="filter-dot active"></span>
                    募集中
                </button>
                <button class="quick-filter-btn <?php echo $initial_status === 'upcoming' ? 'active' : ''; ?>" data-filter="upcoming">
                    <span class="filter-dot upcoming"></span>
                    募集予定
                </button>
                <button class="quick-filter-btn" data-filter="national">
                    <i class="fas fa-globe-asia"></i>
                    全国対応
                </button>
                <button class="quick-filter-btn" data-filter="high-rate">
                    <i class="fas fa-chart-line"></i>
                    高採択率
                </button>
            </div>

            <!-- コントロールバー -->
            <div class="controls-bar">
                <div class="controls-left">
                    <!-- 並び順 -->
                    <select id="sort-order" class="control-select">
                        <option value="date_desc" <?php selected($initial_orderby, 'date_desc'); ?>>新着順</option>
                        <option value="amount_desc" <?php selected($initial_orderby, 'amount_desc'); ?>>金額が高い順</option>
                        <option value="deadline_asc" <?php selected($initial_orderby, 'deadline_asc'); ?>>締切が近い順</option>
                        <option value="success_rate_desc" <?php selected($initial_orderby, 'success_rate_desc'); ?>>採択率順</option>
                    </select>

                    <!-- 詳細フィルター -->
                    <button id="filter-toggle" class="filter-toggle-btn">
                        <i class="fas fa-filter"></i>
                        <span>詳細フィルター</span>
                        <span id="filter-count" class="filter-count" style="display: none;">0</span>
                    </button>
                </div>

                <div class="controls-right">
                    <!-- 表示切り替え -->
                    <div class="view-switcher">
                        <button id="grid-view" class="view-btn active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button id="list-view" class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 📊 メインコンテンツ -->
    <section class="main-content-section">
        <div class="content-container">
            
            <!-- サイドバー（フィルター） -->
            <aside id="filter-sidebar" class="filter-sidebar">
                <div class="filter-panel">
                    <!-- ヘッダー -->
                    <div class="filter-header">
                        <h3 class="filter-title">
                            <i class="fas fa-sliders-h"></i>
                            詳細フィルター
                        </h3>
                        <button id="close-filter" class="close-filter">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- フィルターコンテンツ -->
                    <div class="filter-content">
                        
                        <!-- 都道府県 -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">
                                <i class="fas fa-map-marker-alt"></i>
                                対象地域
                            </h4>
                            <div class="filter-options">
                                <?php
                                $prefectures = get_terms(array(
                                    'taxonomy' => 'grant_prefecture',
                                    'hide_empty' => false,
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 10
                                ));
                                
                                if (!empty($prefectures) && !is_wp_error($prefectures)):
                                    foreach ($prefectures as $prefecture):
                                        $checked = ($initial_prefecture === $prefecture->slug) ? 'checked' : '';
                                ?>
                                <label class="filter-option">
                                    <input type="checkbox" name="prefecture[]" value="<?php echo esc_attr($prefecture->slug); ?>" 
                                           class="filter-checkbox prefecture-checkbox" <?php echo $checked; ?>>
                                    <span class="option-label"><?php echo esc_html($prefecture->name); ?></span>
                                    <span class="option-count"><?php echo $prefecture->count; ?></span>
                                </label>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                        </div>

                        <!-- カテゴリ -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">
                                <i class="fas fa-folder"></i>
                                カテゴリ
                            </h4>
                            <div class="filter-options">
                                <?php
                                $categories = get_terms(array(
                                    'taxonomy' => 'grant_category',
                                    'hide_empty' => false,
                                    'orderby' => 'count',
                                    'order' => 'DESC',
                                    'number' => 8
                                ));
                                
                                if (!empty($categories) && !is_wp_error($categories)):
                                    foreach ($categories as $category):
                                        $checked = ($initial_category === $category->slug) ? 'checked' : '';
                                ?>
                                <label class="filter-option">
                                    <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->slug); ?>" 
                                           class="filter-checkbox category-checkbox" <?php echo $checked; ?>>
                                    <span class="option-label"><?php echo esc_html($category->name); ?></span>
                                    <span class="option-count"><?php echo $category->count; ?></span>
                                </label>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                        </div>

                        <!-- 金額 -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">
                                <i class="fas fa-yen-sign"></i>
                                助成金額
                            </h4>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="radio" name="amount" value="" <?php checked($initial_amount, ''); ?> 
                                           class="filter-radio">
                                    <span class="option-label">すべて</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="amount" value="0-100" <?php checked($initial_amount, '0-100'); ?> 
                                           class="filter-radio">
                                    <span class="option-label">〜100万円</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="amount" value="100-500" <?php checked($initial_amount, '100-500'); ?> 
                                           class="filter-radio">
                                    <span class="option-label">100〜500万円</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="amount" value="500-1000" <?php checked($initial_amount, '500-1000'); ?> 
                                           class="filter-radio">
                                    <span class="option-label">500〜1000万円</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="amount" value="1000+" <?php checked($initial_amount, '1000+'); ?> 
                                           class="filter-radio">
                                    <span class="option-label">1000万円〜</span>
                                </label>
                            </div>
                        </div>

                        <!-- 採択率 -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">
                                <i class="fas fa-percentage"></i>
                                採択率
                            </h4>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="success_rate[]" value="high" class="filter-checkbox">
                                    <span class="option-label">高い（70%以上）</span>
                                    <span class="rate-indicator high"></span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="success_rate[]" value="medium" class="filter-checkbox">
                                    <span class="option-label">普通（50-69%）</span>
                                    <span class="rate-indicator medium"></span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="success_rate[]" value="low" class="filter-checkbox">
                                    <span class="option-label">低い（50%未満）</span>
                                    <span class="rate-indicator low"></span>
                                </label>
                            </div>
                        </div>

                        <!-- ステータス -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">
                                <i class="fas fa-info-circle"></i>
                                募集状況
                            </h4>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" name="status[]" value="active" <?php checked($initial_status, 'active'); ?> 
                                           class="filter-checkbox status-checkbox">
                                    <span class="option-label">募集中</span>
                                    <span class="status-indicator active"></span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="status[]" value="upcoming" <?php checked($initial_status, 'upcoming'); ?> 
                                           class="filter-checkbox status-checkbox">
                                    <span class="option-label">募集予定</span>
                                    <span class="status-indicator upcoming"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- フィルターフッター -->
                    <div class="filter-footer">
                        <button id="clear-filters" class="filter-clear-btn">
                            <i class="fas fa-undo"></i>
                            リセット
                        </button>
                        <button id="apply-filters" class="filter-apply-btn">
                            <i class="fas fa-check"></i>
                            適用する
                        </button>
                    </div>
                </div>
            </aside>

            <!-- メインコンテンツ -->
            <main class="main-content">
                
                <!-- 結果ヘッダー -->
                <div class="results-header">
                    <div class="results-info">
                        <h2 id="results-count" class="results-title">
                            <?php echo number_format($total_grants); ?>件の助成金
                        </h2>
                        <p id="results-description" class="results-desc">
                            <?php if ($initial_search): ?>
                                「<?php echo esc_html($initial_search); ?>」の検索結果
                            <?php endif; ?>
                        </p>
                    </div>
                    <div id="loading-indicator" class="loading-indicator" style="display: none;">
                        <div class="spinner"></div>
                        <span>読み込み中...</span>
                    </div>
                </div>

                <!-- アクティブフィルター -->
                <div id="active-filters" class="active-filters" style="display: none;">
                    <!-- アクティブフィルターがここに表示される -->
                </div>

                <!-- 助成金コンテナ -->
                <div id="grants-container" class="grants-container">
                    <div id="grants-display" class="grants-display">
                        <?php
                        // 初期表示用のAJAXリクエストをシミュレート
                        $args = array(
                            'post_type' => 'grant',
                            'posts_per_page' => 12,
                            'post_status' => 'publish',
                            'orderby' => 'date',
                            'order' => 'DESC'
                        );
                        
                        // URLパラメータがある場合は適用
                        if ($initial_search) {
                            $args['s'] = $initial_search;
                        }
                        
                        if ($initial_category) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'grant_category',
                                'field' => 'slug',
                                'terms' => $initial_category
                            );
                        }
                        
                        if ($initial_prefecture) {
                            $args['tax_query'][] = array(
                                'taxonomy' => 'grant_prefecture',
                                'field' => 'slug',
                                'terms' => $initial_prefecture
                            );
                        }
                        
                        $grants_query = new WP_Query($args);
                        
                        if ($grants_query->have_posts()) {
                            echo '<div class="grants-grid">';
                            while ($grants_query->have_posts()) {
                                $grants_query->the_post();
                                
                                // ajax-functions.phpと同じ形式でカードを生成
                                $grant_id = get_the_ID();
                                $title = get_the_title();
                                $permalink = get_permalink();
                                $excerpt = get_the_excerpt();
                                $amount = get_post_meta($grant_id, 'max_amount', true) ?: '未定';
                                $deadline = get_post_meta($grant_id, 'deadline_date', true);
                                $status = get_post_meta($grant_id, 'application_status', true) ?: 'closed';
                                $success_rate = get_post_meta($grant_id, 'grant_success_rate', true) ?: 0;
                                
                                // ステータスの変換
                                $status_text = '';
                                $status_class = '';
                                switch ($status) {
                                    case 'open':
                                        $status_text = '募集中';
                                        $status_class = 'active';
                                        break;
                                    case 'upcoming':
                                        $status_text = '募集予定';
                                        $status_class = 'upcoming';
                                        break;
                                    default:
                                        $status_text = '募集終了';
                                        $status_class = 'closed';
                                }
                                
                                // 締切日のフォーマット
                                $deadline_text = '';
                                if ($deadline) {
                                    $deadline_timestamp = strtotime($deadline);
                                    $days_left = floor(($deadline_timestamp - time()) / (60 * 60 * 24));
                                    if ($days_left > 0) {
                                        $deadline_text = "あと{$days_left}日";
                                    } else if ($days_left == 0) {
                                        $deadline_text = "本日締切";
                                    } else {
                                        $deadline_text = "締切終了";
                                    }
                                }
                                ?>
                                <article class="grant-card">
                                    <div class="card-header">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                        <?php if ($success_rate > 0): ?>
                                        <span class="success-rate">
                                            採択率 <?php echo $success_rate; ?>%
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h3 class="card-title">
                                            <a href="<?php echo esc_url($permalink); ?>">
                                                <?php echo esc_html($title); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="card-excerpt">
                                            <?php echo wp_trim_words($excerpt, 50, '...'); ?>
                                        </p>
                                        
                                        <div class="card-meta">
                                            <div class="amount">
                                                <i class="fas fa-yen-sign"></i>
                                                <span><?php echo esc_html($amount); ?></span>
                                            </div>
                                            
                                            <?php if ($deadline_text): ?>
                                            <div class="deadline">
                                                <i class="fas fa-clock"></i>
                                                <span><?php echo $deadline_text; ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <a href="<?php echo esc_url($permalink); ?>" class="detail-btn">
                                            詳細を見る
                                        </a>
                                        <button class="favorite-btn" data-id="<?php echo $grant_id; ?>">
                                            <i class="far fa-heart"></i>
                                        </button>
                                        <button class="share-btn" data-url="<?php echo esc_url($permalink); ?>">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </article>
                                <?php
                            }
                            echo '</div>';
                            wp_reset_postdata();
                        } else {
                            echo '<div class="no-results">助成金が見つかりませんでした。</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- ページネーション -->
                <div id="pagination-container" class="pagination-container">
                    <!-- ページネーションがここに表示される -->
                </div>

                <!-- 結果なし -->
                <div id="no-results" class="no-results-container" style="display: none;">
                    <div class="no-results-content">
                        <i class="fas fa-search no-results-icon"></i>
                        <h3>該当する助成金が見つかりませんでした</h3>
                        <p>検索条件を変更して再度お試しください</p>
                        <button id="reset-search" class="reset-search-btn">
                            検索条件をリセット
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </section>
</div>

<!-- 🎨 ウルトラモダンスタイル -->
<style>
/* ベース設定 */
:root {
    --primary: #000000;
    --primary-rgb: 0, 0, 0;
    --accent: #FF6B6B;
    --success: #4ECDC4;
    --warning: #FFE66D;
    --info: #4A90E2;
    
    --text-primary: #1a1a1a;
    --text-secondary: #6b6b6b;
    --text-light: #9a9a9a;
    
    --bg-primary: #ffffff;
    --bg-secondary: #f8f8f8;
    --bg-tertiary: #f0f0f0;
    
    --border: #e0e0e0;
    --border-light: #f0f0f0;
    
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.12);
    
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    
    --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.ultra-modern-archive {
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text-primary);
    background: var(--bg-secondary);
    min-height: 100vh;
}

/* ミニマルヒーロー */
.minimal-hero-section {
    position: relative;
    background: linear-gradient(180deg, #ffffff 0%, #f8f8f8 100%);
    padding: 80px 20px 60px;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.gradient-mesh {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
}

.mesh-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    top: -100px;
    left: -50px;
}

.mesh-2 {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    bottom: -100px;
    right: -50px;
}

.mesh-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.geometric-pattern {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.02) 1px, transparent 1px);
    background-size: 50px 50px;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-content {
    text-align: center;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: var(--primary);
    color: white;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 24px;
}

.hero-title {
    margin-bottom: 16px;
}

.title-main {
    display: block;
    font-size: clamp(32px, 5vw, 56px);
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
}

.title-sub {
    display: block;
    font-size: clamp(18px, 3vw, 24px);
    font-weight: 400;
    color: var(--text-secondary);
}

.hero-description {
    font-size: 18px;
    color: var(--text-secondary);
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 36px;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* 検索・フィルターセクション */
.search-filter-section {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-light);
    padding: 32px 20px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
}

.search-bar-wrapper {
    margin-bottom: 24px;
}

.search-bar {
    display: flex;
    align-items: center;
    background: var(--bg-secondary);
    border: 2px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 8px;
    transition: var(--transition);
}

.search-bar:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
}

.search-icon {
    padding: 0 16px;
    color: var(--text-secondary);
}

.search-input {
    flex: 1;
    border: none;
    background: none;
    padding: 12px 0;
    font-size: 16px;
    color: var(--text-primary);
    outline: none;
}

.search-clear {
    padding: 8px 12px;
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
}

.search-clear:hover {
    color: var(--text-primary);
}

.search-button {
    padding: 12px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.search-button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* クイックフィルター */
.quick-filters {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.quick-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
}

.quick-filter-btn:hover {
    background: var(--bg-tertiary);
    border-color: var(--primary);
}

.quick-filter-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.filter-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.filter-dot.active {
    background: #4ECDC4;
}

.filter-dot.upcoming {
    background: #FFE66D;
}

/* コントロールバー */
.controls-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.controls-left {
    display: flex;
    gap: 12px;
    align-items: center;
}

.control-select {
    padding: 10px 16px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    color: var(--text-primary);
    cursor: pointer;
    outline: none;
}

.filter-toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.filter-toggle-btn:hover {
    background: var(--bg-tertiary);
}

.filter-count {
    padding: 2px 8px;
    background: var(--accent);
    color: white;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}

.view-switcher {
    display: flex;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    padding: 4px;
}

.view-btn {
    padding: 8px 12px;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    border-radius: var(--radius-sm);
}

.view-btn.active {
    background: var(--primary);
    color: white;
}

/* メインコンテンツ */
.main-content-section {
    padding: 40px 20px;
}

.content-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    gap: 32px;
}

/* フィルターサイドバー */
.filter-sidebar {
    width: 300px;
    position: sticky;
    top: 200px;
    height: fit-content;
}

.filter-panel {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--border-light);
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}

.close-filter {
    display: none;
    width: 32px;
    height: 32px;
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
}

.filter-content {
    padding: 24px;
    max-height: 600px;
    overflow-y: auto;
}

.filter-group {
    margin-bottom: 32px;
}

.filter-group:last-child {
    margin-bottom: 0;
}

.filter-group-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.filter-option {
    display: flex;
    align-items: center;
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition);
}

.filter-option:hover {
    background: var(--bg-tertiary);
}

.filter-checkbox,
.filter-radio {
    margin-right: 12px;
}

.option-label {
    flex: 1;
    font-size: 14px;
    color: var(--text-primary);
}

.option-count {
    padding: 2px 8px;
    background: var(--bg-tertiary);
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
}

.rate-indicator,
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.rate-indicator.high {
    background: #4ECDC4;
}

.rate-indicator.medium {
    background: #FFE66D;
}

.rate-indicator.low {
    background: #FF6B6B;
}

.status-indicator.active {
    background: #4ECDC4;
    animation: pulse 2s infinite;
}

.status-indicator.upcoming {
    background: #FFE66D;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(78, 205, 196, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0);
    }
}

.filter-footer {
    display: flex;
    gap: 12px;
    padding: 24px;
    border-top: 1px solid var(--border-light);
}

.filter-clear-btn,
.filter-apply-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.filter-clear-btn {
    background: var(--bg-secondary);
    color: var(--text-primary);
}

.filter-apply-btn {
    background: var(--primary);
    color: white;
}

.filter-clear-btn:hover {
    background: var(--bg-tertiary);
}

.filter-apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* メインコンテンツ */
.main-content {
    flex: 1;
    min-width: 0;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 24px;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.results-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 4px;
}

.results-desc {
    font-size: 14px;
    color: var(--text-secondary);
}

.loading-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-secondary);
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* アクティブフィルター */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 24px;
}

.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--primary);
    color: white;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
}

.filter-tag button {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0;
    display: flex;
}

/* 助成金カード表示 */
.grants-container {
    position: relative;
    min-height: 400px;
}

.grants-display {
    width: 100%;
}

/* グリッド表示 */
.grants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}

/* リスト表示 */
.grants-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 48px;
}

/* 助成金カード */
.grant-card {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.grant-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-badge.active {
    background: rgba(78, 205, 196, 0.1);
    color: #4ECDC4;
}

.status-badge.upcoming {
    background: rgba(255, 230, 109, 0.1);
    color: #f59e0b;
}

.status-badge.closed {
    background: rgba(255, 107, 107, 0.1);
    color: #FF6B6B;
}

.success-rate {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 600;
}

.card-body {
    padding: 20px;
    flex: 1;
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.4;
}

.card-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: var(--transition);
}

.card-title a:hover {
    color: var(--primary);
}

.card-excerpt {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 16px;
}

.card-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.card-meta > div {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: var(--text-secondary);
}

.card-meta i {
    font-size: 12px;
    color: var(--text-light);
}

.amount {
    font-weight: 600;
    color: var(--primary);
}

.deadline {
    color: var(--accent);
}

.card-footer {
    padding: 16px 20px;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    gap: 12px;
}

.detail-btn {
    flex: 1;
    padding: 10px 20px;
    background: var(--primary);
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    transition: var(--transition);
}

.detail-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.favorite-btn,
.share-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
}

.favorite-btn:hover,
.share-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* リスト表示時のカード調整 */
.grants-list .grant-card {
    flex-direction: row;
}

.grants-list .card-header {
    writing-mode: vertical-rl;
    padding: 20px 12px;
    border-bottom: none;
    border-right: 1px solid var(--border-light);
}

.grants-list .card-body {
    flex: 1;
}

.grants-list .card-footer {
    width: 160px;
    flex-direction: column;
    gap: 8px;
}

.grants-list .detail-btn {
    width: 100%;
}

.grants-list .favorite-btn,
.grants-list .share-btn {
    width: 100%;
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
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.pagination-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* 結果なし */
.no-results-container {
    text-align: center;
    padding: 80px 20px;
}

.no-results-content {
    max-width: 400px;
    margin: 0 auto;
}

.no-results-icon {
    font-size: 64px;
    color: var(--text-light);
    margin-bottom: 24px;
}

.no-results-content h3 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.no-results-content p {
    font-size: 16px;
    color: var(--text-secondary);
    margin-bottom: 32px;
}

.reset-search-btn {
    padding: 12px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.reset-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* レスポンシブ */
@media (max-width: 1024px) {
    .content-container {
        flex-direction: column;
    }
    
    .filter-sidebar {
        width: 100%;
        position: static;
    }
    
    .grants-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .minimal-hero-section {
        padding: 60px 16px 40px;
    }
    
    .hero-stats {
        gap: 32px;
    }
    
    .stat-value {
        font-size: 28px;
    }
    
    .search-filter-section {
        padding: 20px 16px;
        position: relative;
    }
    
    .quick-filters {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .quick-filters::-webkit-scrollbar {
        display: none;
    }
    
    .controls-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .controls-left {
        width: 100%;
        justify-content: space-between;
    }
    
    .controls-right {
        display: none;
    }
    
    .main-content-section {
        padding: 20px 16px;
    }
    
    /* モバイル用フィルターサイドバー */
    .filter-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--bg-primary);
        z-index: 1000;
        transition: left 0.3s ease;
    }
    
    .filter-sidebar.active {
        left: 0;
    }
    
    .filter-panel {
        height: 100%;
        display: flex;
        flex-direction: column;
        border-radius: 0;
    }
    
    .close-filter {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .filter-content {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 100px;
    }
    
    .filter-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-primary);
        border-top: 1px solid var(--border);
        padding: 16px;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.1);
        z-index: 1001;
    }
    
    .grants-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    /* モバイルではリスト表示を無効化 */
    .grants-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .grants-list .grant-card {
        flex-direction: column;
    }
    
    .grants-list .card-header {
        writing-mode: initial;
        padding: 16px 20px;
        border-right: none;
        border-bottom: 1px solid var(--border-light);
    }
    
    .grants-list .card-footer {
        width: 100%;
        flex-direction: row;
        padding: 16px 20px;
    }
}

/* ダークモード対応 */
@media (prefers-color-scheme: dark) {
    :root {
        --text-primary: #f0f0f0;
        --text-secondary: #a0a0a0;
        --text-light: #707070;
        --bg-primary: #1a1a1a;
        --bg-secondary: #0f0f0f;
        --bg-tertiary: #2a2a2a;
        --border: #2a2a2a;
        --border-light: #1f1f1f;
    }
    
    .minimal-hero-section {
        background: linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 100%);
    }
    
    .gradient-mesh {
        opacity: 0.1;
    }
}
</style>

<!-- 🚀 JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const GrantArchive = {
        // 設定
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('gi_ajax_nonce'); ?>',
        initialParams: <?php echo json_encode($initial_params); ?>,
        
        // 状態管理
        currentView: 'grid',
        currentPage: 1,
        isLoading: false,
        filters: {
            search: '',
            categories: [],
            prefectures: [],
            amount: '',
            status: [],
            success_rate: [],
            sort: 'date_desc'
        },

        // 初期化
        init() {
            this.bindEvents();
            this.applyInitialParams();
            this.updateFilterCount();
        },

        // イベントバインディング
        bindEvents() {
            // 検索
            const searchInput = document.getElementById('grant-search');
            const searchBtn = document.getElementById('search-btn');
            const searchClear = document.getElementById('search-clear');
            
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.filters.search = e.target.value;
                    searchClear.style.display = e.target.value ? 'block' : 'none';
                    this.debounce(() => this.loadGrants(), 500)();
                });
            }
            
            if (searchBtn) {
                searchBtn.addEventListener('click', () => this.loadGrants());
            }
            
            if (searchClear) {
                searchClear.addEventListener('click', () => {
                    searchInput.value = '';
                    searchClear.style.display = 'none';
                    this.filters.search = '';
                    this.loadGrants();
                });
            }

            // クイックフィルター
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const filter = e.currentTarget.dataset.filter;
                    this.applyQuickFilter(filter);
                });
            });

            // 並び順
            const sortOrder = document.getElementById('sort-order');
            if (sortOrder) {
                sortOrder.addEventListener('change', (e) => {
                    this.filters.sort = e.target.value;
                    this.loadGrants();
                });
            }

            // フィルタートグル
            const filterToggle = document.getElementById('filter-toggle');
            const closeFilter = document.getElementById('close-filter');
            const filterSidebar = document.getElementById('filter-sidebar');
            
            if (filterToggle) {
                filterToggle.addEventListener('click', () => {
                    filterSidebar.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if (closeFilter) {
                closeFilter.addEventListener('click', () => {
                    filterSidebar.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // フィルター適用
            const applyFilters = document.getElementById('apply-filters');
            if (applyFilters) {
                applyFilters.addEventListener('click', () => {
                    this.applyFilters();
                    if (window.innerWidth <= 768) {
                        filterSidebar.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            }

            // フィルタークリア
            const clearFilters = document.getElementById('clear-filters');
            if (clearFilters) {
                clearFilters.addEventListener('click', () => {
                    this.clearFilters();
                });
            }

            // ビュー切り替え（修正版）
            const gridViewBtn = document.getElementById('grid-view');
            const listViewBtn = document.getElementById('list-view');
            
            if (gridViewBtn) {
                gridViewBtn.addEventListener('click', () => {
                    if (this.currentView !== 'grid') {
                        this.switchView('grid');
                    }
                });
            }
            
            if (listViewBtn) {
                listViewBtn.addEventListener('click', () => {
                    if (this.currentView !== 'list') {
                        this.switchView('list');
                    }
                });
            }

            // フィルターチェックボックス
            document.querySelectorAll('.filter-checkbox, .filter-radio').forEach(input => {
                input.addEventListener('change', () => {
                    this.updateFilterCount();
                    if (window.innerWidth > 768) {
                        this.applyFilters();
                    }
                });
            });

            // 検索リセット
            const resetSearch = document.getElementById('reset-search');
            if (resetSearch) {
                resetSearch.addEventListener('click', () => {
                    this.clearFilters();
                    this.loadGrants();
                });
            }
        },

        // 初期パラメータ適用
        applyInitialParams() {
            if (this.initialParams.search) {
                this.filters.search = this.initialParams.search;
                document.getElementById('grant-search').value = this.initialParams.search;
                document.getElementById('search-clear').style.display = 'block';
            }
            
            if (this.initialParams.category) {
                this.filters.categories = [this.initialParams.category];
            }
            
            if (this.initialParams.prefecture) {
                this.filters.prefectures = [this.initialParams.prefecture];
            }
            
            if (this.initialParams.amount) {
                this.filters.amount = this.initialParams.amount;
            }
            
            if (this.initialParams.status) {
                this.filters.status = [this.initialParams.status];
            }
            
            if (this.initialParams.orderby) {
                this.filters.sort = this.initialParams.orderby;
            }
            
            // 初期パラメータがある場合は検索を実行
            if (this.hasActiveFilters()) {
                // 少し遅延させてDOMの準備を待つ
                setTimeout(() => {
                    this.loadGrants();
                }, 100);
            }
        },

        // クイックフィルター適用
        applyQuickFilter(filter) {
            // ボタンのアクティブ状態を更新
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.currentTarget.classList.add('active');

            // フィルターをリセット
            this.clearFilters(false);

            // 特定のフィルターを適用
            switch(filter) {
                case 'all':
                    break;
                case 'active':
                    this.filters.status = ['active'];
                    const activeCheckbox = document.querySelector('input[name="status[]"][value="active"]');
                    if (activeCheckbox) activeCheckbox.checked = true;
                    break;
                case 'upcoming':
                    this.filters.status = ['upcoming'];
                    const upcomingCheckbox = document.querySelector('input[name="status[]"][value="upcoming"]');
                    if (upcomingCheckbox) upcomingCheckbox.checked = true;
                    break;
                case 'national':
                    // 全国対応の都道府県を選択
                    const nationalCheckbox = document.querySelector('.prefecture-checkbox[value="national"]');
                    if (nationalCheckbox) {
                        nationalCheckbox.checked = true;
                        this.filters.prefectures = ['national'];
                    }
                    break;
                case 'high-rate':
                    this.filters.success_rate = ['high'];
                    const highRateCheckbox = document.querySelector('input[name="success_rate[]"][value="high"]');
                    if (highRateCheckbox) highRateCheckbox.checked = true;
                    break;
            }

            this.updateFilterCount();
            this.loadGrants();
        },

        // フィルター適用
        applyFilters() {
            // カテゴリ
            this.filters.categories = Array.from(
                document.querySelectorAll('.category-checkbox:checked')
            ).map(cb => cb.value);

            // 都道府県
            this.filters.prefectures = Array.from(
                document.querySelectorAll('.prefecture-checkbox:checked')
            ).map(cb => cb.value);

            // 金額
            const amountRadio = document.querySelector('input[name="amount"]:checked');
            this.filters.amount = amountRadio ? amountRadio.value : '';

            // 採択率
            this.filters.success_rate = Array.from(
                document.querySelectorAll('input[name="success_rate[]"]:checked')
            ).map(cb => cb.value);

            // ステータス
            this.filters.status = Array.from(
                document.querySelectorAll('.status-checkbox:checked')
            ).map(cb => cb.value);

            this.updateFilterCount();
            this.updateActiveFilters();
            this.loadGrants();
        },

        // フィルタークリア
        clearFilters(reload = true) {
            // フィルターをリセット
            this.filters = {
                search: '',
                categories: [],
                prefectures: [],
                amount: '',
                status: [],
                success_rate: [],
                sort: this.filters.sort
            };

            // UI更新
            document.getElementById('grant-search').value = '';
            document.getElementById('search-clear').style.display = 'none';
            document.querySelectorAll('.filter-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.filter-radio').forEach(rb => {
                rb.checked = rb.value === '';
            });
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.quick-filter-btn[data-filter="all"]').classList.add('active');

            this.updateFilterCount();
            this.updateActiveFilters();
            
            if (reload) {
                this.loadGrants();
            }
        },

        // フィルター数更新
        updateFilterCount() {
            const count = 
                this.filters.categories.length +
                this.filters.prefectures.length +
                (this.filters.amount ? 1 : 0) +
                this.filters.success_rate.length +
                this.filters.status.length;

            const filterCount = document.getElementById('filter-count');
            if (filterCount) {
                filterCount.textContent = count;
                filterCount.style.display = count > 0 ? 'inline-block' : 'none';
            }
        },

        // アクティブフィルター表示更新
        updateActiveFilters() {
            const container = document.getElementById('active-filters');
            if (!container) return;

            if (!this.hasActiveFilters()) {
                container.style.display = 'none';
                return;
            }

            let html = '';
            
            // カテゴリ
            this.filters.categories.forEach(cat => {
                const label = document.querySelector(`.category-checkbox[value="${cat}"]`)
                    ?.closest('.filter-option')
                    ?.querySelector('.option-label')?.textContent || cat;
                html += this.createFilterTag(label, 'category', cat);
            });

            // 都道府県
            this.filters.prefectures.forEach(pref => {
                const label = document.querySelector(`.prefecture-checkbox[value="${pref}"]`)
                    ?.closest('.filter-option')
                    ?.querySelector('.option-label')?.textContent || pref;
                html += this.createFilterTag(label, 'prefecture', pref);
            });

            // 金額
            if (this.filters.amount) {
                const label = document.querySelector(`input[name="amount"][value="${this.filters.amount}"]`)
                    ?.closest('.filter-option')
                    ?.querySelector('.option-label')?.textContent || this.filters.amount;
                html += this.createFilterTag(label, 'amount', this.filters.amount);
            }

            container.innerHTML = html;
            container.style.display = 'flex';
        },

        // フィルタータグ作成
        createFilterTag(label, type, value) {
            return `
                <div class="filter-tag">
                    ${label}
                    <button onclick="GrantArchive.removeFilter('${type}', '${value}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        },

        // フィルター削除
        removeFilter(type, value) {
            switch(type) {
                case 'category':
                    this.filters.categories = this.filters.categories.filter(c => c !== value);
                    document.querySelector(`.category-checkbox[value="${value}"]`).checked = false;
                    break;
                case 'prefecture':
                    this.filters.prefectures = this.filters.prefectures.filter(p => p !== value);
                    document.querySelector(`.prefecture-checkbox[value="${value}"]`).checked = false;
                    break;
                case 'amount':
                    this.filters.amount = '';
                    document.querySelector('input[name="amount"][value=""]').checked = true;
                    break;
            }
            
            this.updateFilterCount();
            this.updateActiveFilters();
            this.loadGrants();
        },

        // ビュー切り替え（完全修正版）
        switchView(view) {
            if (this.currentView === view) return;
            
            this.currentView = view;
            
            // ボタンのアクティブ状態を更新
            document.getElementById('grid-view').classList.toggle('active', view === 'grid');
            document.getElementById('list-view').classList.toggle('active', view === 'list');
            
            // 既存のデータを再表示（再読み込みせずに表示切り替え）
            const grantsDisplay = document.getElementById('grants-display');
            if (grantsDisplay) {
                const currentContent = grantsDisplay.innerHTML;
                
                // グリッド/リストのクラスを切り替え
                if (view === 'grid') {
                    // リストからグリッドへ
                    grantsDisplay.innerHTML = currentContent.replace('grants-list', 'grants-grid');
                } else {
                    // グリッドからリストへ
                    grantsDisplay.innerHTML = currentContent.replace('grants-grid', 'grants-list');
                }
            }
            
            // 新しいビューでデータを再読み込み
            this.loadGrants();
        },

        // 助成金読み込み
        async loadGrants() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            this.showLoading();

            try {
                const response = await fetch(this.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'gi_load_grants',
                        nonce: this.nonce,
                        search: this.filters.search,
                        categories: JSON.stringify(this.filters.categories),
                        prefectures: JSON.stringify(this.filters.prefectures),
                        amount: this.filters.amount,
                        status: JSON.stringify(this.filters.status),
                        success_rate: JSON.stringify(this.filters.success_rate),
                        sort: this.filters.sort,
                        view: this.currentView,
                        page: this.currentPage
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.renderGrants(data.data);
                } else {
                    this.showNoResults();
                }
            } catch (error) {
                console.error('Error loading grants:', error);
                this.showNoResults();
            } finally {
                this.isLoading = false;
                this.hideLoading();
            }
        },

        // 助成金レンダリング
        renderGrants(data) {
            const { grants, found_posts, pagination } = data;
            
            // 結果数更新
            document.getElementById('results-count').textContent = `${found_posts}件の助成金`;
            
            // 助成金表示
            const grantsDisplay = document.getElementById('grants-display');
            if (grants && grants.length > 0) {
                // ビューに応じたコンテナクラスを設定
                const containerClass = this.currentView === 'grid' ? 'grants-grid' : 'grants-list';
                grantsDisplay.innerHTML = `<div class="${containerClass}">${grants.map(grant => grant.html).join('')}</div>`;
                
                document.getElementById('grants-container').style.display = 'block';
                document.getElementById('no-results').style.display = 'none';
            } else {
                this.showNoResults();
            }

            // ページネーション
            if (pagination && pagination.html) {
                document.getElementById('pagination-container').innerHTML = pagination.html;
            }
        },

        // ローディング表示
        showLoading() {
            const indicator = document.getElementById('loading-indicator');
            if (indicator) indicator.style.display = 'flex';
        },

        hideLoading() {
            const indicator = document.getElementById('loading-indicator');
            if (indicator) indicator.style.display = 'none';
        },

        // 結果なし表示
        showNoResults() {
            document.getElementById('grants-container').style.display = 'none';
            document.getElementById('no-results').style.display = 'block';
        },

        // アクティブフィルター確認
        hasActiveFilters() {
            return this.filters.search ||
                   this.filters.categories.length > 0 ||
                   this.filters.prefectures.length > 0 ||
                   this.filters.amount ||
                   this.filters.status.length > 0 ||
                   this.filters.success_rate.length > 0;
        },

        // デバウンス
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    // グローバルに公開
    window.GrantArchive = GrantArchive;
    
    // 初期化
    GrantArchive.init();
});
</script>

<?php
get_footer();
?>

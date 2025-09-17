<?php
/**
 * Search Results Template
 * Grant Insight Perfect - 検索結果表示テンプレート
 * 
 * @package Grant_Insight_Perfect
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); 
?>

<div class="search-results-page">
    <!-- ヘッダー部分 -->
    <section class="search-results-header bg-gradient-to-br from-emerald-50 to-teal-50 py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-search text-emerald-600 mr-3"></i>
                    検索結果
                </h1>
                <?php if (have_posts()): ?>
                    <p class="text-lg text-gray-600">
                        「<span class="font-semibold text-emerald-600"><?php echo get_search_query(); ?></span>」の検索結果: 
                        <span class="font-bold"><?php echo $wp_query->found_posts; ?>件</span>
                    </p>
                <?php else: ?>
                    <p class="text-lg text-gray-600">
                        「<span class="font-semibold text-red-500"><?php echo get_search_query(); ?></span>」に該当する結果が見つかりませんでした
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- メインコンテンツ -->
    <section class="search-results-content py-16 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (have_posts()): ?>
                <div class="search-results-grid grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php while (have_posts()): the_post(); ?>
                        <div class="search-result-card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                            
                            <!-- サムネイル -->
                            <?php if (has_post_thumbnail()): ?>
                                <div class="card-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('gi-card-thumb', array('class' => 'w-full h-48 object-cover')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- コンテンツ -->
                            <div class="card-content p-6">
                                <!-- 投稿タイプバッジ -->
                                <div class="mb-3">
                                    <?php
                                    $post_type = get_post_type();
                                    $badge_class = '';
                                    $badge_text = '';
                                    
                                    switch ($post_type) {
                                        case 'grant':
                                            $badge_class = 'bg-emerald-100 text-emerald-800';
                                            $badge_text = '助成金・補助金';
                                            break;
                                        case 'tool':
                                            $badge_class = 'bg-blue-100 text-blue-800';
                                            $badge_text = 'ビジネスツール';
                                            break;
                                        case 'case_study':
                                            $badge_class = 'bg-purple-100 text-purple-800';
                                            $badge_text = '成功事例';
                                            break;
                                        case 'guide':
                                            $badge_class = 'bg-orange-100 text-orange-800';
                                            $badge_text = 'ガイド・解説';
                                            break;
                                        case 'grant_tip':
                                            $badge_class = 'bg-yellow-100 text-yellow-800';
                                            $badge_text = '申請のコツ';
                                            break;
                                        default:
                                            $badge_class = 'bg-gray-100 text-gray-800';
                                            $badge_text = '記事';
                                    }
                                    ?>
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $badge_class; ?>">
                                        <?php echo $badge_text; ?>
                                    </span>
                                </div>

                                <!-- タイトル -->
                                <h2 class="card-title text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-emerald-600 transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>

                                <!-- 抜粋 -->
                                <p class="card-excerpt text-gray-600 mb-4 line-clamp-3">
                                    <?php 
                                    $excerpt = get_the_excerpt();
                                    echo wp_trim_words($excerpt, 20, '...');
                                    ?>
                                </p>

                                <!-- メタ情報 -->
                                <div class="card-meta flex items-center justify-between text-sm text-gray-500">
                                    <span class="date">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?php echo get_the_date('Y.m.d'); ?>
                                    </span>
                                    
                                    <?php if ($post_type === 'grant'): ?>
                                        <?php 
                                        $deadline = get_post_meta(get_the_ID(), 'deadline_date', true);
                                        if ($deadline): 
                                        ?>
                                            <span class="deadline text-red-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo date('m/d まで', $deadline); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- カテゴリ・タグ（該当する場合） -->
                                <?php if ($post_type === 'grant'): ?>
                                    <?php 
                                    $categories = get_the_terms(get_the_ID(), 'grant_category');
                                    if ($categories && !is_wp_error($categories)): 
                                    ?>
                                        <div class="card-categories mt-3 flex flex-wrap gap-2">
                                            <?php foreach (array_slice($categories, 0, 2) as $category): ?>
                                                <span class="category-tag text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                                    <?php echo esc_html($category->name); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- 詳細ボタン -->
                                <div class="card-actions mt-4">
                                    <a href="<?php the_permalink(); ?>" class="btn-primary inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors">
                                        詳細を見る
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- ページネーション -->
                <div class="search-pagination mt-12 flex justify-center">
                    <?php
                    echo paginate_links(array(
                        'total' => $wp_query->max_num_pages,
                        'prev_text' => '<i class="fas fa-chevron-left"></i> 前へ',
                        'next_text' => '次へ <i class="fas fa-chevron-right"></i>',
                        'class' => 'pagination-link px-4 py-2 mx-1 border border-gray-300 rounded text-gray-700 hover:bg-emerald-50 hover:border-emerald-500 hover:text-emerald-600'
                    ));
                    ?>
                </div>

            <?php else: ?>
                <!-- 検索結果が見つからない場合 -->
                <div class="no-results text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8">
                            <i class="fas fa-search text-6xl text-gray-300"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-700 mb-4">検索結果が見つかりませんでした</h2>
                        <p class="text-gray-600 mb-8">
                            別のキーワードでお試しいただくか、以下の方法をお試しください：
                        </p>
                        <div class="suggestions text-left bg-white p-6 rounded-lg shadow">
                            <h3 class="font-semibold text-gray-800 mb-3">検索のコツ：</h3>
                            <ul class="space-y-2 text-gray-600">
                                <li><i class="fas fa-check text-emerald-500 mr-2"></i>キーワードを短くしてみる</li>
                                <li><i class="fas fa-check text-emerald-500 mr-2"></i>類義語や関連語で検索する</li>
                                <li><i class="fas fa-check text-emerald-500 mr-2"></i>ひらがな・カタカナ・漢字を変えてみる</li>
                                <li><i class="fas fa-check text-emerald-500 mr-2"></i>詳細検索機能を使用する</li>
                            </ul>
                        </div>
                        <div class="mt-8">
                            <a href="<?php echo home_url('/search/'); ?>" class="btn-secondary inline-flex items-center px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                                <i class="fas fa-search mr-2"></i>
                                詳細検索ページへ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.search-result-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.search-result-card:hover {
    transform: translateY(-2px);
}

.pagination-link.current {
    background-color: #059669 !important;
    color: white !important;
    border-color: #059669 !important;
}
</style>

<?php get_footer(); ?>
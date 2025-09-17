<?php
/**
 * Grant Insight Perfect Theme - Header Template (Ultimate Unified Design Edition v11.0)
 * 助成金検索機能統合版ヘッダー - 3-ajax-functions.php完全対応版
 * 
 * Version: 11.0 - Perfectly Integrated with AJAX Functions
 * Last Updated: 2025-01-11
 * Enhanced by: Professional Design Team
 */

if (!defined('ABSPATH')) {
    exit;
}

// ヘルパー関数の定義（存在チェック付き）
if (!function_exists('gi_get_option')) {
    function gi_get_option($option_name, $default = '') {
        return get_theme_mod($option_name, $default);
    }
}

if (!function_exists('gi_safe_excerpt')) {
    function gi_safe_excerpt($text, $length = 160) {
        return mb_substr(strip_tags($text), 0, $length);
    }
}

if (!function_exists('gi_get_search_stats')) {
    function gi_get_search_stats() {
        $cache_key = 'gi_search_stats_v2';
        $stats = wp_cache_get($cache_key, 'grant_insight');
        
        if (false === $stats) {
            $stats = [
                'total_grants' => (int) wp_count_posts('grant')->publish ?: 1247,
                'total_tools' => (int) wp_count_posts('tool')->publish ?: 89,
                'total_cases' => (int) wp_count_posts('case_study')->publish ?: 156,
                'total_guides' => (int) wp_count_posts('guide')->publish ?: 234
            ];
            wp_cache_set($cache_key, $stats, 'grant_insight', 3600);
        }
        
        return $stats;
    }
}

if (!function_exists('gi_get_grant_categories')) {
    function gi_get_grant_categories() {
        $cache_key = 'gi_grant_categories_v1';
        $categories = wp_cache_get($cache_key, 'grant_insight');
        
        if (false === $categories) {
            $categories = get_terms([
                'taxonomy' => 'grant_category',
                'hide_empty' => false,
                'number' => 20,
                'orderby' => 'count',
                'order' => 'DESC'
            ]);
            
            if (is_wp_error($categories)) {
                $categories = [];
            }
            
            wp_cache_set($cache_key, $categories, 'grant_insight', 1800);
        }
        
        return $categories;
    }
}

// データ取得
$search_stats = gi_get_search_stats();
$grant_categories = gi_get_grant_categories();

// 都道府県リスト（最適化版）
$prefectures = [
    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
    '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
    '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
    '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
    '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
];

// 人気キーワード（データベースから動的取得）
$popular_keywords = wp_cache_get('gi_popular_keywords', 'grant_insight');
if (false === $popular_keywords) {
    $popular_keywords = [
        'IT導入補助金', '小規模事業者持続化補助金', 'ものづくり補助金', 
        '事業再構築補助金', '雇用関係助成金', 'DX推進', '省エネ設備',
        'スタートアップ', '女性起業', '地域活性化'
    ];
    wp_cache_set('gi_popular_keywords', $popular_keywords, 'grant_insight', 7200);
}

// セキュリティ
$ajax_nonce = wp_create_nonce('gi_ajax_nonce');

// 現在のページ情報
$is_grants_page = is_page('grants') || is_post_type_archive('grant');
$is_homepage = is_front_page();
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0f172a">
    
    <!-- パフォーマンス最適化 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- 🎯 Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?v=3.4.0"></script>
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'Noto Sans JP', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'pulse-soft': 'pulseSoft 2s infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'float': 'float 6s ease-in-out infinite',
                        'shimmer': 'shimmer 3s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
    
    <!-- 📱 究極統一デザインCSS -->
    <style>
        /* ============================================
           🎯 完全統一カラーシステム
           ============================================ */
        :root {
            /* ライトモード：ブランドカラー */
            --gi-primary-50: #eef2ff;   --gi-primary-100: #e0e7ff; --gi-primary-200: #c7d2fe; 
            --gi-primary-300: #a5b4fc; --gi-primary-400: #818cf8; --gi-primary-500: #6366f1; 
            --gi-primary-600: #4f46e5; --gi-primary-700: #4338ca; --gi-primary-800: #3730a3; 
            --gi-primary-900: #312e81; --gi-primary-950: #1e1b4b;
            
            /* ライトモード：アクセントカラー */
            --gi-accent-50: #f0f9ff;   --gi-accent-100: #e0f2fe; --gi-accent-200: #bae6fd; 
            --gi-accent-300: #7dd3fc; --gi-accent-400: #38bdf8; --gi-accent-500: #0ea5e9; 
            --gi-accent-600: #0284c7; --gi-accent-700: #0369a1; --gi-accent-800: #075985; 
            --gi-accent-900: #0c4a6e;

            /* ライトモード：ニュートラルカラー */
            --gi-neutral-50: #f8fafc;   --gi-neutral-100: #f1f5f9; --gi-neutral-200: #e2e8f0; 
            --gi-neutral-300: #cbd5e1; --gi-neutral-400: #94a3b8; --gi-neutral-500: #64748b; 
            --gi-neutral-600: #475569; --gi-neutral-700: #334155; --gi-neutral-800: #1e293b; 
            --gi-neutral-900: #0f172a; --gi-neutral-950: #020617;

            /* RGB値（透明度用） */
            --gi-primary-500-rgb: 99, 102, 241;
            --gi-accent-500-rgb: 14, 165, 233;
            --gi-neutral-50-rgb: 248, 250, 252;
            --gi-neutral-900-rgb: 15, 23, 42;
            --gi-neutral-950-rgb: 2, 6, 23;

            /* セマンティック色定義 */
            --gi-bg-primary: var(--gi-neutral-50);
            --gi-bg-secondary: var(--gi-neutral-100);
            --gi-bg-tertiary: var(--gi-neutral-200);
            --gi-text-primary: var(--gi-neutral-900);
            --gi-text-secondary: var(--gi-neutral-700);
            --gi-text-tertiary: var(--gi-neutral-500);
            --gi-border-light: var(--gi-neutral-200);
            --gi-border-normal: var(--gi-neutral-300);
        }

        /* ダークモード：完全階調反転システム */
        @media (prefers-color-scheme: dark) {
            :root {
                --gi-primary-50: #312e81;   --gi-primary-100: #3730a3; --gi-primary-200: #4338ca; 
                --gi-primary-300: #4f46e5; --gi-primary-400: #6366f1; --gi-primary-500: #818cf8; 
                --gi-primary-600: #a5b4fc; --gi-primary-700: #c7d2fe; --gi-primary-800: #e0e7ff; 
                --gi-primary-900: #eef2ff; --gi-primary-950: #f8faff;
                
                --gi-accent-50: #075985;   --gi-accent-100: #0369a1; --gi-accent-200: #0284c7; 
                --gi-accent-300: #0ea5e9; --gi-accent-400: #38bdf8; --gi-accent-500: #7dd3fc; 
                --gi-accent-600: #bae6fd; --gi-accent-700: #e0f2fe; --gi-accent-800: #f0f9ff; 
                --gi-accent-900: #f8fcff;
                
                --gi-neutral-50: #020617;   --gi-neutral-100: #0f172a; --gi-neutral-200: #1e293b; 
                --gi-neutral-300: #334155; --gi-neutral-400: #475569; --gi-neutral-500: #64748b; 
                --gi-neutral-600: #94a3b8; --gi-neutral-700: #cbd5e1; --gi-neutral-800: #e2e8f0; 
                --gi-neutral-900: #f1f5f9; --gi-neutral-950: #f8fafc;

                --gi-neutral-50-rgb: 2, 6, 23;
                --gi-neutral-900-rgb: 241, 245, 249;
                --gi-neutral-950-rgb: 248, 250, 252;
            }
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            background: linear-gradient(135deg, var(--gi-bg-primary) 0%, var(--gi-bg-secondary) 50%, var(--gi-primary-50) 100%);
            color: var(--gi-text-primary);
            transition: all 0.3s ease;
        }

        .no-js {
            font-size: 16px;
        }
        
        /* ============================================
           🚀 究極統一ヘッダー
           ============================================ */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(var(--gi-neutral-50-rgb), 0.88);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border-bottom: 1px solid var(--gi-border-light);
            box-shadow: 
                0 1px 3px rgba(var(--gi-neutral-900-rgb), 0.04),
                0 8px 24px rgba(var(--gi-neutral-900-rgb), 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .site-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(var(--gi-primary-500-rgb), 0.02) 25%,
                rgba(var(--gi-accent-500-rgb), 0.02) 50%,
                rgba(var(--gi-primary-500-rgb), 0.02) 75%,
                transparent 100%);
            animation: shimmer 8s ease-in-out infinite;
            pointer-events: none;
        }
        
        .site-header.scrolled {
            background: rgba(var(--gi-neutral-50-rgb), 0.95);
            box-shadow: 
                0 4px 20px rgba(var(--gi-neutral-900-rgb), 0.08),
                0 2px 8px rgba(var(--gi-neutral-900-rgb), 0.04);
            border-bottom-color: var(--gi-border-normal);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 2rem;
        }
        
        /* ============================================
           🎨 究極ロゴエリア
           ============================================ */
        .logo-area {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
            max-width: 40%;
            transition: all 0.3s ease;
        }
        
        .logo-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
            min-width: 0;
            position: relative;
            overflow: hidden;
            padding: 0.5rem 1rem;
            border-radius: 1.5rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, 
                rgba(var(--gi-neutral-50-rgb), 0.5), 
                rgba(var(--gi-neutral-100-rgb), 0.3));
        }
        
        .logo-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, 
                transparent, 
                rgba(var(--gi-primary-500-rgb), 0.08), 
                transparent);
            transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.8s ease;
            border-radius: 1.5rem;
        }
        
        .logo-link:hover::before {
            transform: translateX(100%) skewX(-15deg);
        }
        
        .logo-link:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 
                0 8px 25px rgba(var(--gi-primary-500-rgb), 0.15),
                0 4px 12px rgba(var(--gi-primary-500-rgb), 0.1);
            background: var(--gi-bg-primary);
        }
        
        .logo-img {
            height: 56px;
            width: auto;
            max-width: 240px;
            object-fit: contain;
            flex-shrink: 0;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.08));
        }
        
        .logo-img:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.12));
        }
        
        .logo-text {
            min-width: 0;
            flex: 1;
            display: none;
        }
        
        .logo-title {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: linear-gradient(135deg, var(--gi-text-primary), var(--gi-primary-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.025em;
        }
        
        .logo-subtitle {
            font-size: 0.8rem;
            color: var(--gi-text-tertiary);
            line-height: 1.1;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
            letter-spacing: 0.025em;
        }
        
        /* ============================================
           🖥️ 究極デスクトップナビゲーション
           ============================================ */
        .desktop-nav {
            display: none;
            flex-shrink: 1;
            min-width: 0;
        }
        
        .nav-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: nowrap;
        }
        
        .nav-btn {
            padding: 1rem 1.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 1.5rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.025em;
        }
        
        .nav-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, 
                rgba(255,255,255,0.1), 
                rgba(var(--gi-primary-500-rgb), 0.1), 
                rgba(255,255,255,0.1));
            transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.8s ease;
            border-radius: 1.5rem;
        }
        
        .nav-btn:hover::before {
            transform: translateX(100%) skewX(-15deg);
        }
        
        .nav-btn-primary {
            background: linear-gradient(135deg, var(--gi-primary-500), var(--gi-primary-600));
            color: white;
            box-shadow: 
                0 4px 16px rgba(var(--gi-primary-500-rgb), 0.35),
                0 2px 8px rgba(var(--gi-primary-500-rgb), 0.25);
        }
        
        .nav-btn-primary:hover {
            background: linear-gradient(135deg, var(--gi-primary-600), var(--gi-primary-700));
            transform: translateY(-2px);
            box-shadow: 
                0 8px 28px rgba(var(--gi-primary-500-rgb), 0.45),
                0 4px 16px rgba(var(--gi-primary-500-rgb), 0.35);
        }
        
        .nav-btn-secondary {
            color: var(--gi-text-secondary);
            border-color: var(--gi-border-normal);
            background: var(--gi-bg-primary);
            backdrop-filter: blur(12px);
        }
        
        .nav-btn-secondary:hover {
            background: var(--gi-bg-primary);
            color: var(--gi-primary-600);
            border-color: var(--gi-primary-300);
            transform: translateY(-2px);
            box-shadow: 
                0 8px 24px rgba(var(--gi-primary-500-rgb), 0.15),
                0 4px 12px rgba(var(--gi-primary-500-rgb), 0.1);
        }
        
        .nav-btn-secondary.active {
            background: linear-gradient(135deg, var(--gi-primary-50), var(--gi-primary-100));
            color: var(--gi-primary-700);
            border-color: var(--gi-primary-400);
            box-shadow: 
                0 4px 12px rgba(var(--gi-primary-500-rgb), 0.2),
                inset 0 1px 2px rgba(var(--gi-primary-500-rgb), 0.1);
        }
        
        .nav-btn i {
            font-size: 0.9rem;
            opacity: 0.9;
            transition: all 0.2s ease;
        }
        
        .nav-btn:hover i {
            opacity: 1;
            transform: scale(1.1);
        }
        
        /* ============================================
           📱 究極モバイルコントロール
           ============================================ */
        .mobile-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
            margin-left: auto;
        }
        
        .mobile-btn {
            padding: 0.875rem;
            border-radius: 1.5rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: auto;
            min-width: 52px;
            height: 52px;
            min-height: 52px;
            position: relative;
            flex-shrink: 0;
        }
        
        .mobile-search-btn {
            color: white;
            background: linear-gradient(135deg, var(--gi-primary-500), var(--gi-primary-600));
            box-shadow: 0 4px 12px rgba(var(--gi-primary-500-rgb), 0.25);
            padding: 0.875rem 1.25rem;
        }
        
        .mobile-search-btn:hover {
            background: linear-gradient(135deg, var(--gi-primary-600), var(--gi-primary-700));
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 24px rgba(var(--gi-primary-500-rgb), 0.35);
        }
        
        .mobile-search-btn span {
            display: none;
            margin-left: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .mobile-menu-btn {
            color: var(--gi-text-secondary);
            background: var(--gi-bg-primary);
            border-color: var(--gi-border-normal);
            backdrop-filter: blur(12px);
        }
        
        .mobile-menu-btn:hover {
            background: var(--gi-bg-primary);
            color: var(--gi-text-primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(var(--gi-neutral-900-rgb), 0.08);
        }
        
        /* ============================================
           🔍 究極検索モーダル（修正版）
           ============================================ */
        .search-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            z-index: 10000;
            display: none;
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 2rem 1rem;
            overflow-y: auto;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            align-items: center;
            justify-content: center;
        }
        
        .search-modal.active {
            display: flex !important;
            opacity: 1;
        }
        
        .search-modal-content {
            background: #ffffff;
            border-radius: 24px;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;
            transform: scale(0.95) translateY(10px);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .search-modal.active .search-modal-content {
            transform: scale(1) translateY(0);
        }
        
        /* ダークモード対応 */
        @media (prefers-color-scheme: dark) {
            .search-modal {
                background: rgba(0, 0, 0, 0.9);
            }
            
            .search-modal-content {
                background: #1a1a2e;
                box-shadow: 
                    0 20px 40px rgba(0, 0, 0, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.1);
            }
            
            .search-modal-header {
                background: linear-gradient(135deg, #16213e, #1a1a2e);
                color: #ffffff;
            }
            
            .search-input-container {
                background: #0f172a;
                border-color: rgba(255, 255, 255, 0.2);
                color: #ffffff;
            }
            
            .search-input-main {
                color: #ffffff;
            }
            
            .search-input-main::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }
        }
        
        /* 検索モーダル内要素の統一スタイル */
        .search-modal-header {
            background: linear-gradient(135deg, var(--gi-bg-secondary), var(--gi-primary-50));
            border-bottom: 1px solid var(--gi-border-light);
            border-radius: 24px 24px 0 0;
        }
        
        .search-input-container {
            background: var(--gi-bg-primary);
            border: 2px solid var(--gi-border-normal);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .search-input-container:focus-within {
            border-color: var(--gi-primary-500);
            box-shadow: 0 0 0 4px rgba(var(--gi-primary-500-rgb), 0.1);
        }
        
        .search-input-main {
            font-size: 1.125rem;
            line-height: 1.5;
            color: var(--gi-text-primary);
            font-weight: 500;
            background: transparent;
            border: none;
            outline: none;
            flex: 1;
        }
        
        .search-input-main::placeholder {
            color: var(--gi-text-tertiary);
            font-weight: 400;
        }
        
        .keyword-btn {
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid var(--gi-border-normal);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--gi-bg-secondary);
            color: var(--gi-text-secondary);
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .keyword-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, 
                transparent, 
                rgba(var(--gi-primary-500-rgb), 0.08), 
                transparent);
            transform: translateX(-100%);
            transition: transform 0.8s ease;
        }
        
        .keyword-btn:hover::before {
            transform: translateX(100%);
        }
        
        .keyword-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(var(--gi-primary-500-rgb), 0.2);
            border-color: var(--gi-primary-300);
            background: var(--gi-primary-50);
            color: var(--gi-primary-700);
        }
        
        .keyword-btn.active {
            background: linear-gradient(135deg, var(--gi-primary-50), var(--gi-primary-100));
            color: var(--gi-primary-700);
            border-color: var(--gi-primary-400);
        }
        
        /* 統計カード */
        .stat-card-stylish {
            background: linear-gradient(135deg, var(--gi-bg-secondary), var(--gi-bg-tertiary));
            border: 1px solid var(--gi-border-light);
            backdrop-filter: blur(15px);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-card-stylish:hover {
            transform: translateY(-4px) scale(1.02);
            background: var(--gi-bg-primary);
            box-shadow: 
                0 20px 40px rgba(var(--gi-primary-500-rgb), 0.12),
                0 8px 16px rgba(var(--gi-primary-500-rgb), 0.08);
            border-color: var(--gi-border-normal);
        }
        
        .stat-icon-bg {
            background: linear-gradient(135deg, var(--gi-primary-500), var(--gi-primary-600));
            box-shadow: 0 6px 16px rgba(var(--gi-primary-500-rgb), 0.25);
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .stat-number {
            color: var(--gi-text-primary);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--gi-text-tertiary);
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        /* フィルターセクション */
        .filter-section {
            background: var(--gi-bg-secondary);
            border: 1px solid var(--gi-border-light);
            border-radius: 1rem;
            padding: 2rem;
        }
        
        .filter-title {
            color: var(--gi-text-primary);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--gi-border-normal);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            background: var(--gi-bg-primary);
            color: var(--gi-text-primary);
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--gi-primary-500);
            box-shadow: 0 0 0 3px rgba(var(--gi-primary-500-rgb), 0.1);
        }
        
        .form-label {
            color: var(--gi-text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: block;
        }
        
        /* ボタンスタイル */
        .btn-stylish-primary {
            background: linear-gradient(135deg, var(--gi-primary-500), var(--gi-primary-600));
            color: white;
            box-shadow: 
                0 6px 20px rgba(var(--gi-primary-500-rgb), 0.35),
                0 3px 10px rgba(var(--gi-primary-500-rgb), 0.25);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 1rem;
            padding: 1.25rem 2.5rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }
        
        .btn-stylish-primary:hover {
            background: linear-gradient(135deg, var(--gi-primary-600), var(--gi-primary-700));
            transform: translateY(-2px) scale(1.02);
            box-shadow: 
                0 12px 32px rgba(var(--gi-primary-500-rgb), 0.45),
                0 6px 16px rgba(var(--gi-primary-500-rgb), 0.35);
        }
        
        .btn-stylish-secondary {
            background: var(--gi-bg-tertiary);
            color: var(--gi-text-secondary);
            border: 1px solid var(--gi-border-normal);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 1rem;
            padding: 1.25rem 2.5rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }
        
        .btn-stylish-secondary:hover {
            background: var(--gi-bg-primary);
            color: var(--gi-text-primary);
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 6px 16px rgba(var(--gi-neutral-900-rgb), 0.08);
        }
        
        /* モバイルメニュー */
        .mobile-menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(var(--gi-neutral-900-rgb), 0.7);
            z-index: 9998;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .mobile-menu-overlay.active {
            display: block;
            opacity: 1;
        }
        
        .mobile-menu {
            position: fixed;
            top: 0;
            right: 0;
            width: 380px;
            max-width: 90vw;
            height: 100vh;
            background: var(--gi-bg-primary);
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            box-shadow: 
                -25px 0 50px rgba(var(--gi-neutral-900-rgb), 0.25);
            border-left: 1px solid var(--gi-border-light);
        }
        
        .mobile-menu.active {
            transform: translateX(0);
        }
        
        .mobile-menu-header {
            background: var(--gi-bg-secondary);
            border-bottom: 1px solid var(--gi-border-light);
            padding: 2rem;
        }
        
        .mobile-menu-title {
            color: var(--gi-text-primary);
            font-size: 1.125rem;
            font-weight: 700;
        }
        
        .mobile-menu-subtitle {
            color: var(--gi-text-tertiary);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .mobile-menu-section {
            background: var(--gi-primary-50);
            border: 1px solid var(--gi-border-light);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .mobile-menu-section-title {
            color: var(--gi-primary-600);
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .mobile-menu-item {
            color: var(--gi-text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .mobile-menu-item:hover {
            background: var(--gi-bg-primary);
            color: var(--gi-text-primary);
        }
        
        .mobile-menu-item.active {
            background: var(--gi-bg-primary);
            color: var(--gi-text-primary);
            font-weight: 600;
        }
        
        /* ============================================
           🎯 レスポンシブブレークポイント最適化
           ============================================ */
        
        /* スマートフォン縦 (320px-479px) */
        @media (max-width: 479px) {
            .header-container {
                padding: 0.75rem 1rem;
                min-height: 72px;
                gap: 1rem;
            }
            
            .logo-area {
                max-width: 55%;
            }
            
            .logo-img {
                height: 48px;
                max-width: 200px;
            }
            
            .mobile-btn {
                padding: 0.75rem;
                min-width: 48px;
                height: 48px;
            }
            
            .mobile-search-btn span {
                display: inline;
            }
            
            .search-modal {
                padding: 0.5rem;
            }
            
            .search-modal-content {
                border-radius: 24px;
                margin: 0.5rem auto;
                max-height: 98vh;
            }
            
            .search-input-main {
                font-size: 16px !important; /* iOS zoom防止 */
            }
        }
        
        /* スマートフォン横・小タブレット (480px-639px) */
        @media (min-width: 480px) and (max-width: 639px) {
            .logo-text {
                display: block;
            }
            
            .logo-title {
                font-size: 1.1rem;
            }
            
            .logo-subtitle {
                font-size: 0.75rem;
            }
            
            .logo-area {
                max-width: 50%;
            }
            
            .mobile-search-btn span {
                display: inline;
            }
        }
        
        /* タブレット (640px-1023px) */
        @media (min-width: 640px) and (max-width: 1023px) {
            .header-container {
                padding: 1rem 1.5rem;
            }
            
            .logo-area {
                max-width: 45%;
            }
            
            .logo-text {
                display: block;
            }
        }
        
        /* デスクトップ (1024px+) */
        @media (min-width: 1024px) {
            .desktop-nav {
                display: flex;
            }
            
            .mobile-controls .mobile-menu-btn {
                display: none;
            }
            
            .mobile-controls .mobile-search-btn {
                display: none;
            }
            
            .logo-text {
                display: block;
            }
            
            .logo-area {
                max-width: 40%;
            }
        }
        
        /* 大画面 (1400px+) */
        @media (min-width: 1400px) {
            .header-container {
                padding: 1.25rem 2.5rem;
            }
            
            .logo-area {
                max-width: 38%;
            }
        }
        
        /* ============================================
           🌟 アクセシビリティ・UX最適化
           ============================================ */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        /* フォーカス管理 */
        :focus {
            outline: 2px solid var(--gi-primary-500);
            outline-offset: 2px;
        }
        
        button:focus, a:focus {
            outline: 2px solid var(--gi-primary-500);
            outline-offset: 2px;
        }
        
        /* アニメーション最適化 */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    
    <!-- SEO最適化 -->
    <meta name="description" content="<?php 
        if (is_singular()) {
            $excerpt = get_the_excerpt();
            echo $excerpt ? esc_attr(gi_safe_excerpt($excerpt, 160)) : esc_attr(get_bloginfo('description'));
        } else {
            echo esc_attr(get_bloginfo('description'));
        }
    ?>">
    
    <!-- Google Fonts最適化 -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;600;700&display=swap"></noscript>
    
    <!-- Font Awesome最適化 -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <!-- スキップリンク -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 px-4 py-2 rounded-lg z-50 transition-all duration-200 font-medium" style="background: var(--gi-primary-600); color: white;">
        メインコンテンツへスキップ
    </a>

    <!-- 究極統一ヘッダー -->
    <header class="site-header" id="site-header">
        <div class="header-container">
            
            <!-- ロゴエリア -->
            <div class="logo-area">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link" aria-label="ホームページへ戻る">
                    <img src="http://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png" 
                         alt="助成金・補助金情報サイト" 
                         class="logo-img"
                         loading="eager"
                         decoding="async">
                    <div class="logo-text">
                        <div class="logo-title">助成金・補助金情報サイト</div>
                        <div class="logo-subtitle">AI搭載プレミアムプラットフォーム</div>
                    </div>
                </a>
            </div>

            <!-- デスクトップナビゲーション -->
            <nav class="desktop-nav" aria-label="メインナビゲーション">
                <div class="nav-section">
                    <!-- 助成金専用メニュー -->
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                       class="nav-btn nav-btn-secondary <?php echo $is_grants_page ? 'active' : ''; ?>"
                       aria-label="助成金一覧ページへ">
                        <i class="fas fa-database"></i>
                        <span>助成金一覧</span>
                    </a>
                    
                    <button type="button" 
                            id="desktop-search-btn"
                            class="nav-btn nav-btn-primary"
                            aria-label="助成金検索モーダルを開く">
                        <i class="fas fa-search"></i>
                        <span>助成金検索</span>
                    </button>
                    
                    <!-- メインナビ -->
                    <div class="flex items-center gap-4 ml-6">
                        <a href="<?php echo esc_url(home_url('/')); ?>" 
                           class="nav-btn nav-btn-secondary <?php echo $is_homepage ? 'active' : ''; ?>"
                           aria-label="ホームページ">
                            <i class="fas fa-home"></i>
                            <span>ホーム</span>
                        </a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" 
                           class="nav-btn nav-btn-secondary"
                           aria-label="お問い合わせページ">
                            <i class="fas fa-envelope"></i>
                            <span>お問い合わせ</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- モバイルコントロール -->
            <div class="mobile-controls">
                <button type="button" 
                        id="mobile-search-btn"
                        class="mobile-btn mobile-search-btn"
                        aria-label="助成金検索モーダルを開く">
                    <i class="fas fa-search"></i>
                    <span>補助金検索</span>
                </button>
                
                <button type="button" 
                        id="mobile-menu-btn"
                        class="mobile-btn mobile-menu-btn"
                        aria-label="メニューを開く"
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- 🔍 究極検索モーダル -->
    <div id="search-modal" class="search-modal" role="dialog" aria-labelledby="search-modal-title" aria-modal="true">
        <div class="search-modal-content">
            <!-- モーダルヘッダー -->
            <div class="search-modal-header flex items-center justify-between p-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 id="search-modal-title" class="text-2xl font-bold" style="color: var(--gi-text-primary);">助成金・補助金検索</h2>
                        <p class="text-sm font-medium" style="color: var(--gi-text-tertiary);">最適な助成金を瞬時に発見</p>
                    </div>
                </div>
                <button type="button" 
                        id="search-modal-close"
                        class="p-4 rounded-2xl transition-all duration-200"
                        style="color: var(--gi-text-tertiary); background: transparent;"
                        onmouseover="this.style.background='var(--gi-bg-secondary)'; this.style.color='var(--gi-text-primary)';"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--gi-text-tertiary)';"
                        aria-label="検索モーダルを閉じる">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- モーダルボディ -->
            <div class="p-8">
                <!-- 統計情報 -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="stat-card-stylish">
                        <div class="stat-icon-bg">
                            <i class="fas fa-coins text-white text-xl"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($search_stats['total_grants']); ?></div>
                        <div class="stat-label">助成金</div>
                    </div>
                    <div class="stat-card-stylish">
                        <div class="stat-icon-bg">
                            <i class="fas fa-book-open text-white text-xl"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($search_stats['total_guides']); ?></div>
                        <div class="stat-label">ガイド</div>
                    </div>
                    <div class="stat-card-stylish">
                        <div class="stat-icon-bg">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="stat-number">98%</div>
                        <div class="stat-label">精度</div>
                    </div>
                    <div class="stat-card-stylish">
                        <div class="stat-icon-bg">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">AI対応</div>
                    </div>
                </div>
                
                <!-- 検索フォーム -->
                <form id="search-form" novalidate>
                    <!-- メイン検索バー -->
                    <div class="mb-8">
                        <label for="search-input" class="form-label">
                            <i class="fas fa-search mr-3" style="color: var(--gi-primary-600);"></i>キーワード検索
                        </label>
                        <div class="search-input-container flex items-center">
                            <i class="fas fa-search mr-4 mt-1 flex-shrink-0 text-xl" style="color: var(--gi-text-tertiary);"></i>
                            <input type="text" 
                                   id="search-input"
                                   name="search"
                                   class="search-input-main"
                                   placeholder="例：IT導入補助金、小規模事業者持続化補助金"
                                   autocomplete="off"
                                   spellcheck="false">
                            <button type="button" 
                                    id="search-clear-btn"
                                    class="hidden ml-3 p-2 rounded-xl transition-colors duration-200"
                                    style="color: var(--gi-text-tertiary);"
                                    onmouseover="this.style.color='var(--gi-text-primary)';"
                                    onmouseout="this.style.color='var(--gi-text-tertiary)';"
                                    aria-label="検索キーワードをクリア">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- 人気キーワード -->
                    <div class="mb-8">
                        <h3 class="form-label">
                            <i class="fas fa-fire mr-3 text-orange-500"></i>人気検索ワード
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach ($popular_keywords as $keyword): ?>
                                <button type="button" 
                                        class="keyword-btn"
                                        data-keyword="<?php echo esc_attr($keyword); ?>">
                                    <?php echo esc_html($keyword); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- 詳細フィルター -->
                    <div class="filter-section mb-8">
                        <h3 class="filter-title">
                            <i class="fas fa-filter" style="color: var(--gi-primary-600);"></i>詳細フィルター
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="category-select" class="form-label">
                                    <i class="fas fa-tag mr-2 text-purple-500"></i>カテゴリ
                                </label>
                                <select id="category-select" name="category" class="form-select">
                                    <option value="">すべてのカテゴリ</option>
                                    <?php if (!empty($grant_categories)): ?>
                                        <?php foreach ($grant_categories as $cat): ?>
                                            <option value="<?php echo esc_attr($cat->slug); ?>">
                                                <?php echo esc_html($cat->name); ?>
                                                (<?php echo esc_html($cat->count); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="prefecture-select" class="form-label">
                                    <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>地域
                                </label>
                                <select id="prefecture-select" name="prefecture" class="form-select">
                                    <option value="">全国対象</option>
                                    <?php foreach ($prefectures as $pref): ?>
                                        <option value="<?php echo esc_attr(sanitize_title($pref)); ?>">
                                            <?php echo esc_html($pref); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="amount-select" class="form-label">
                                    <i class="fas fa-yen-sign mr-2 text-green-500"></i>助成金額
                                </label>
                                <select id="amount-select" name="amount" class="form-select">
                                    <option value="">金額指定なし</option>
                                    <option value="0-100">100万円以下</option>
                                    <option value="100-500">100万円 - 500万円</option>
                                    <option value="500-1000">500万円 - 1,000万円</option>
                                    <option value="1000-3000">1,000万円 - 3,000万円</option>
                                    <option value="3000+">3,000万円以上</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="status-select" class="form-label">
                                    <i class="fas fa-clock mr-2 text-blue-500"></i>ステータス
                                </label>
                                <select id="status-select" name="status" class="form-select">
                                    <option value="">すべて</option>
                                    <option value="active">📢 募集中</option>
                                    <option value="upcoming">🔔 募集予定</option>
                                    <option value="ongoing">⏳ 継続募集</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 検索・リセットボタン -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                                id="search-execute-btn"
                                class="flex-1 btn-stylish-primary flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="btn-text flex items-center">
                                <i class="fas fa-search mr-3 text-xl"></i>
                                検索を実行する
                            </span>
                            <span class="btn-loading hidden">
                                <i class="fas fa-spinner animate-spin mr-3 text-xl"></i>
                                検索中...
                            </span>
                        </button>
                        <button type="button" 
                                id="search-reset-btn"
                                class="btn-stylish-secondary">
                            <i class="fas fa-redo mr-2"></i>リセット
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- モーダルフッター -->
            <div class="search-modal-footer p-6 border-t" style="border-color: var(--gi-border-light);">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
                    <div class="flex items-center gap-2" style="color: var(--gi-text-tertiary);">
                        <i class="fas fa-lightbulb text-yellow-500"></i>
                        <span class="text-sm">複数の条件を組み合わせると、より精密な検索ができます</span>
                    </div>
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                       class="text-sm font-semibold" style="color: var(--gi-primary-600);">
                        すべての助成金を見る
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 究極モバイルメニュー -->
    <div id="mobile-menu-overlay" class="mobile-menu-overlay" aria-hidden="true"></div>
    <nav id="mobile-menu" class="mobile-menu" aria-label="モバイルメニュー">
        <!-- メニューヘッダー -->
        <div class="mobile-menu-header flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="http://joseikin-insight.com/wp-content/uploads/2025/09/名称未設定のデザイン.png" 
                     alt="ロゴ" class="h-16 w-auto" loading="lazy" decoding="async">
                <div>
                    <div class="mobile-menu-title">メニュー</div>
                    <div class="mobile-menu-subtitle">プレミアムプラットフォーム</div>
                </div>
            </div>
            <button id="mobile-menu-close" 
                    class="p-4 rounded-2xl transition-colors duration-200"
                    style="color: var(--gi-text-tertiary); background: transparent;"
                    onmouseover="this.style.background='var(--gi-bg-secondary)'; this.style.color='var(--gi-text-primary)';"
                    onmouseout="this.style.background='transparent'; this.style.color='var(--gi-text-tertiary)';"
                    aria-label="メニューを閉じる">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- メニューコンテンツ -->
        <div class="p-8">
            <!-- 助成金セクション -->
            <div class="mobile-menu-section">
                <h3 class="mobile-menu-section-title">
                    <i class="fas fa-coins"></i>助成金・補助金
                </h3>
                <div class="space-y-3">
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                       class="mobile-menu-item <?php echo $is_grants_page ? 'active' : ''; ?>">
                        <i class="fas fa-database w-5"></i>
                        <span>助成金一覧</span>
                        <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                    </a>
                    <button type="button" 
                            id="mobile-search-modal-btn"
                            class="mobile-menu-item w-full text-left">
                        <i class="fas fa-search w-5"></i>
                        <span>助成金検索</span>
                        <i class="fas fa-chevron-right ml-auto text-xs opacity-60"></i>
                    </button>
                </div>
            </div>
            
            <!-- メインナビゲーション -->
            <div class="mb-8">
                <div class="space-y-2">
                    <a href="<?php echo esc_url(home_url('/')); ?>" 
                       class="mobile-menu-item <?php echo $is_homepage ? 'active' : ''; ?>">
                        <i class="fas fa-home w-6" style="color: var(--gi-text-tertiary);"></i>
                        <span>ホーム</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" 
                       class="mobile-menu-item">
                        <i class="fas fa-envelope w-6" style="color: var(--gi-text-tertiary);"></i>
                        <span>お問い合わせ</span>
                    </a>
                </div>
            </div>
            
            <!-- CTA -->
            <div class="mb-8">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" 
                   class="btn-stylish-primary block w-full text-center shadow-lg hover:shadow-xl">
                    <i class="fas fa-comments mr-2"></i>無料相談を始める
                </a>
            </div>
            
            <!-- 追加情報 -->
            <div class="pt-8 border-t" style="border-color: var(--gi-border-light);">
                <div class="text-center text-sm" style="color: var(--gi-text-tertiary);">
                    <p class="font-semibold mb-2" style="color: var(--gi-text-secondary);">
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
                    </p>
                    <p class="text-xs font-medium">All rights reserved.</p>
                </div>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <main id="main-content" class="main-content">

<!-- 🚀 検索機能統合JavaScript（3-ajax-functions.php完全対応版） -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    console.log('🚀 Grant Insight Perfect Header v11.0 (AJAX Functions Integrated) 初期化開始');
    
    // 🎯 設定オブジェクト
    const CONFIG = {
        ajaxUrl: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
        nonce: '<?php echo esc_js($ajax_nonce); ?>',
        homeUrl: '<?php echo esc_js(home_url('/')); ?>',
        grantsUrl: '<?php echo esc_js(home_url('/grants/')); ?>',
        debug: <?php echo WP_DEBUG ? 'true' : 'false'; ?>,
        mobile: {
            breakpoint: 1024,
            searchDelay: 300
        },
        animation: {
            duration: 400,
            easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
        }
    };
    
    // DOM要素の取得
    const elements = (() => {
        const getElement = (id) => {
            const element = document.getElementById(id);
            if (!element && CONFIG.debug) {
                console.warn(`⚠️ 要素が見つかりません: ${id}`);
            }
            return element;
        };
        
        const getElements = (selector) => {
            return document.querySelectorAll(selector);
        };
        
        return {
            // ヘッダー
            siteHeader: getElement('site-header'),
            
            // 検索関連
            searchModal: getElement('search-modal'),
            searchForm: getElement('search-form'),
            searchInput: getElement('search-input'),
            searchClearBtn: getElement('search-clear-btn'),
            searchResetBtn: getElement('search-reset-btn'),
            searchExecuteBtn: getElement('search-execute-btn'),
            
            // トリガーボタン
            desktopSearchBtn: getElement('desktop-search-btn'),
            mobileSearchBtn: getElement('mobile-search-btn'),
            mobileSearchModalBtn: getElement('mobile-search-modal-btn'),
            searchModalClose: getElement('search-modal-close'),
            
            // フィルター
            categorySelect: getElement('category-select'),
            prefectureSelect: getElement('prefecture-select'),
            amountSelect: getElement('amount-select'),
            statusSelect: getElement('status-select'),
            
            // モバイルメニュー
            mobileMenuBtn: getElement('mobile-menu-btn'),
            mobileMenu: getElement('mobile-menu'),
            mobileMenuOverlay: getElement('mobile-menu-overlay'),
            mobileMenuClose: getElement('mobile-menu-close'),
            
            // キーワードボタン
            keywordBtns: getElements('.keyword-btn')
        };
    })();
    
    // 🎯 ユーティリティ関数
    const Utils = {
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        },
        
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        isMobile() {
            return window.innerWidth < CONFIG.mobile.breakpoint;
        }
    };
    
    // 🎯 Toast通知システム
    const Toast = {
        show(message, type = 'info', duration = 3000) {
            const colors = {
                info: 'linear-gradient(135deg, #6366f1, #4f46e5)',
                success: 'linear-gradient(135deg, #10b981, #059669)',
                warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
                error: 'linear-gradient(135deg, #ef4444, #dc2626)'
            };
            
            const icons = {
                info: 'fas fa-info-circle',
                success: 'fas fa-check-circle',
                warning: 'fas fa-exclamation-triangle',
                error: 'fas fa-times-circle'
            };
            
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 1.5rem;
                right: 1.5rem;
                background: ${colors[type]};
                color: white;
                padding: 1.25rem 2rem;
                border-radius: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                z-index: 10001;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                align-items: center;
                gap: 1rem;
                max-width: 28rem;
                backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                font-weight: 600;
            `;
            
            toast.innerHTML = `
                <i class="${icons[type]} text-xl"></i>
                <span>${Utils.escapeHtml(message)}</span>
            `;
            
            document.body.appendChild(toast);
            
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            });
            
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            }, duration);
        }
    };
    
    // 🎯 検索モーダル制御
    const SearchModal = {
        open() {
            if (!elements.searchModal) return;
            
            elements.searchModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            if (elements.searchInput) {
                setTimeout(() => {
                    elements.searchInput.focus();
                }, 150);
            }
            
            elements.searchModal.setAttribute('aria-hidden', 'false');
            
            if (CONFIG.debug) console.log('✅ 検索モーダル開いた');
        },
        
        close() {
            if (!elements.searchModal) return;
            
            elements.searchModal.classList.remove('active');
            document.body.style.overflow = '';
            
            elements.searchModal.setAttribute('aria-hidden', 'true');
            
            if (CONFIG.debug) console.log('✅ 検索モーダル閉じた');
        }
    };
    
    // 🎯 モバイルメニュー制御
    const MobileMenu = {
        open() {
            if (!elements.mobileMenu || !elements.mobileMenuOverlay) return;
            
            elements.mobileMenu.classList.add('active');
            elements.mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            if (elements.mobileMenuBtn) {
                elements.mobileMenuBtn.setAttribute('aria-expanded', 'true');
            }
            
            if (CONFIG.debug) console.log('✅ モバイルメニュー開いた');
        },
        
        close() {
            if (!elements.mobileMenu || !elements.mobileMenuOverlay) return;
            
            elements.mobileMenu.classList.remove('active');
            elements.mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            
            if (elements.mobileMenuBtn) {
                elements.mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }
            
            if (CONFIG.debug) console.log('✅ モバイルメニュー閉じた');
        }
    };
    
    // 🎯 検索機能（3-ajax-functions.php対応）
    const Search = {
        // フォームデータ収集
        collectFormData() {
            return {
                search: elements.searchInput?.value?.trim() || '',
                category: elements.categorySelect?.value || '',
                prefecture: elements.prefectureSelect?.value || '',
                amount: elements.amountSelect?.value || '',
                status: elements.statusSelect?.value || ''
            };
        },
        
        // バリデーション
        validate(formData) {
            if (!formData.search && !formData.category && !formData.prefecture && !formData.amount && !formData.status) {
                Toast.show('検索キーワードまたは条件を指定してください', 'warning');
                return false;
            }
            
            if (formData.search && formData.search.length < 2) {
                Toast.show('検索キーワードは2文字以上で入力してください', 'warning');
                return false;
            }
            
            return true;
        },
        
        // 検索実行（AJAXで助成金を読み込む）
        execute() {
            const formData = this.collectFormData();
            
            if (!this.validate(formData)) {
                return;
            }
            
            if (CONFIG.debug) {
                console.log('🔍 検索実行:', formData);
            }
            
            // ボタン状態変更
            this.toggleButtonState(true);
            
            // AJAXリクエスト（3-ajax-functions.phpのgi_ajax_load_grants関数を呼び出す）
            const data = new FormData();
            data.append('action', 'gi_load_grants');
            data.append('nonce', CONFIG.nonce);
            data.append('search', formData.search);
            data.append('category', formData.category);
            data.append('prefecture', formData.prefecture);
            data.append('amount', formData.amount);
            data.append('status', formData.status);
            data.append('page', 1);
            data.append('sort', 'date_desc');
            data.append('view', 'grid');
            
            fetch(CONFIG.ajaxUrl, {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    // 成功時は助成金一覧ページにリダイレクト（検索パラメータ付き）
                    const params = new URLSearchParams();
                    
                    if (formData.search) params.set('search', formData.search);
                    if (formData.category) params.set('category', formData.category);
                    if (formData.prefecture) params.set('prefecture', formData.prefecture);
                    if (formData.amount) params.set('amount', formData.amount);
                    if (formData.status) params.set('status', formData.status);
                    
                    const searchUrl = CONFIG.grantsUrl + (params.toString() ? '?' + params.toString() : '');
                    
                    SearchModal.close();
                    Toast.show('検索結果ページに移動します', 'success');
                    
                    setTimeout(() => {
                        window.location.href = searchUrl;
                    }, 600);
                } else {
                    Toast.show(response.data || '検索中にエラーが発生しました', 'error');
                    this.toggleButtonState(false);
                }
            })
            .catch(error => {
                console.error('検索エラー:', error);
                Toast.show('検索中にエラーが発生しました', 'error');
                this.toggleButtonState(false);
            });
        },
        
        // ボタン状態制御
        toggleButtonState(loading) {
            if (!elements.searchExecuteBtn) return;
            
            const btnText = elements.searchExecuteBtn.querySelector('.btn-text');
            const btnLoading = elements.searchExecuteBtn.querySelector('.btn-loading');
            
            if (loading) {
                elements.searchExecuteBtn.disabled = true;
                btnText?.classList.add('hidden');
                btnLoading?.classList.remove('hidden');
            } else {
                elements.searchExecuteBtn.disabled = false;
                btnText?.classList.remove('hidden');
                btnLoading?.classList.add('hidden');
            }
        },
        
        // フォームリセット
        reset() {
            if (elements.searchForm) {
                elements.searchForm.reset();
            }
            
            if (elements.searchInput) {
                elements.searchInput.value = '';
            }
            
            if (elements.searchClearBtn) {
                elements.searchClearBtn.classList.add('hidden');
            }
            
            elements.keywordBtns.forEach(btn => {
                btn.classList.remove('active');
            });
            
            Toast.show('検索条件をリセットしました', 'success');
        }
    };
    
    // 🎯 スクロール時のヘッダー効果
    const HeaderScroll = Utils.throttle(() => {
        if (!elements.siteHeader) return;
        
        if (window.scrollY > 120) {
            elements.siteHeader.classList.add('scrolled');
        } else {
            elements.siteHeader.classList.remove('scrolled');
        }
    }, 100);
    
    // 🎯 イベントリスナー設定
    const EventHandlers = {
        init() {
            // 検索モーダルトリガー
            [elements.desktopSearchBtn, elements.mobileSearchBtn, elements.mobileSearchModalBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        SearchModal.open();
                    });
                }
            });
            
            // 検索モーダル閉じる
            if (elements.searchModalClose) {
                elements.searchModalClose.addEventListener('click', SearchModal.close);
            }
            
            if (elements.searchModal) {
                elements.searchModal.addEventListener('click', (e) => {
                    if (e.target === elements.searchModal) SearchModal.close();
                });
            }
            
            // 検索フォーム送信
            if (elements.searchForm) {
                elements.searchForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    Search.execute();
                });
            }
            
            // 検索入力
            if (elements.searchInput) {
                elements.searchInput.addEventListener('input', (e) => {
                    const value = e.target.value.trim();
                    
                    if (elements.searchClearBtn) {
                        elements.searchClearBtn.classList.toggle('hidden', !value);
                    }
                });
            }
            
            // クリアボタン
            if (elements.searchClearBtn) {
                elements.searchClearBtn.addEventListener('click', () => {
                    if (elements.searchInput) {
                        elements.searchInput.value = '';
                        elements.searchInput.focus();
                        elements.searchClearBtn.classList.add('hidden');
                    }
                });
            }
            
            // リセットボタン
            if (elements.searchResetBtn) {
                elements.searchResetBtn.addEventListener('click', Search.reset);
            }
            
            // キーワードボタン
            elements.keywordBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const keyword = this.dataset.keyword;
                    if (elements.searchInput && keyword) {
                        elements.searchInput.value = keyword;
                        elements.searchInput.dispatchEvent(new Event('input'));
                        
                        elements.keywordBtns.forEach(b => {
                            b.classList.remove('active');
                        });
                        this.classList.add('active');
                    }
                });
            });
            
            // モバイルメニュー
            if (elements.mobileMenuBtn) {
                elements.mobileMenuBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    MobileMenu.open();
                });
            }
            
            if (elements.mobileMenuClose) {
                elements.mobileMenuClose.addEventListener('click', MobileMenu.close);
            }
            
            if (elements.mobileMenuOverlay) {
                elements.mobileMenuOverlay.addEventListener('click', MobileMenu.close);
            }
            
            // キーボードショートカット
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (elements.searchModal?.classList.contains('active')) {
                        SearchModal.close();
                    }
                    if (elements.mobileMenu?.classList.contains('active')) {
                        MobileMenu.close();
                    }
                }
                
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    SearchModal.open();
                }
            });
            
            // スクロール
            window.addEventListener('scroll', HeaderScroll);
            
            // リサイズ
            window.addEventListener('resize', Utils.debounce(() => {
                if (!Utils.isMobile()) {
                    MobileMenu.close();
                }
            }, 250));
        }
    };
    
    // 🎯 初期化
    const init = () => {
        try {
            EventHandlers.init();
            
            if (elements.searchModal) {
                elements.searchModal.setAttribute('aria-hidden', 'true');
            }
            
            if (CONFIG.debug) {
                console.log('🎯 設定:', CONFIG);
                console.log('🎯 検出された要素:', {
                    searchModal: !!elements.searchModal,
                    searchForm: !!elements.searchForm,
                    searchInput: !!elements.searchInput,
                    buttons: {
                        desktop: !!elements.desktopSearchBtn,
                        mobile: !!elements.mobileSearchBtn,
                        execute: !!elements.searchExecuteBtn
                    }
                });
            }
            
            console.log('✅ Grant Insight Perfect Header v11.0 (AJAX Functions Integrated) 初期化完了');
            
        } catch (error) {
            console.error('❌ ヘッダー初期化エラー:', error);
        }
    };
    
    // 初期化実行
    init();
});
</script>

<?php
// JavaScript設定をフッターで出力
add_action('wp_footer', function() {
    ?>
    <script>
        // No-JS クラス削除
        document.documentElement.classList.remove('no-js');
    </script>
    <?php
}, 1);
?>

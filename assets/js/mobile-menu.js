/**
 * Grant Insight Perfect - Mobile Menu Script (Optimized)
 * モバイルメニューの機能を最適化し、pointer-events問題を解決
 */

(function($) {
    'use strict';

    // グローバル変数
    let mobileMenuOpen = false;
    let touchStartY = 0;
    let touchEndY = 0;

    // DOM要素のキャッシュ
    const $window = $(window);
    const $document = $(document);
    const $body = $('body');

    /**
     * モバイルメニューの初期化
     */
    function initMobileMenu() {
        console.log('Mobile menu initializing...');

        // メニュー要素の作成・取得
        createMobileMenuElements();

        // イベントリスナーの設定
        bindMobileMenuEvents();

        // 初期状態の設定
        resetMobileMenuState();

        // リサイズ時の処理
        handleWindowResize();

        console.log('Mobile menu initialized successfully');
    }

    /**
     * モバイルメニュー要素の作成
     */
    function createMobileMenuElements() {
        // モバイルメニューボタンが存在しない場合は作成
        if ($('.mobile-menu-toggle').length === 0) {
            const toggleButton = `
                <button class="mobile-menu-toggle" aria-label="メニューを開く" aria-expanded="false">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            `;
            $('.site-header .container').append(toggleButton);
        }

        // モバイルメニューオーバーレイが存在しない場合は作成
        if ($('.mobile-menu-overlay').length === 0) {
            const menuOverlay = `
                <div class="mobile-menu-overlay" role="dialog" aria-label="モバイルメニュー" aria-hidden="true">
                    <div class="mobile-menu-container">
                        <div class="mobile-menu-header">
                            <button class="mobile-menu-close" aria-label="メニューを閉じる">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <nav class="mobile-menu-nav" role="navigation">
                            <!-- メニュー項目はPHPで動的に生成 -->
                        </nav>
                    </div>
                </div>
            `;
            $body.append(menuOverlay);
        }

        // CSS修正を即座に適用
        applyCSSFixes();
    }

    /**
     * CSS修正の即座適用
     */
    function applyCSSFixes() {
        // 重要なスタイルを直接適用してpointer-events問題を解決
        $('.mobile-menu-overlay').css({
            'pointer-events': 'auto',
            'touch-action': 'auto',
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'background-color': 'rgba(0, 0, 0, 0.8)',
            'z-index': '9999',
            'display': 'none'
        });

        $('.mobile-menu-toggle').css({
            'pointer-events': 'auto',
            'cursor': 'pointer',
            'z-index': '10000',
            'position': 'relative',
            'background': 'none',
            'border': 'none',
            'padding': '10px',
            'display': 'flex',
            'flex-direction': 'column',
            'justify-content': 'space-around',
            'width': '30px',
            'height': '30px'
        });

        $('.mobile-menu-container').css({
            'pointer-events': 'auto',
            'position': 'absolute',
            'top': '0',
            'right': '0',
            'width': '300px',
            'height': '100%',
            'background-color': '#ffffff',
            'transform': 'translateX(100%)',
            'transition': 'transform 0.3s ease-in-out',
            'overflow-y': 'auto'
        });

        $('.hamburger-line').css({
            'display': 'block',
            'height': '2px',
            'width': '100%',
            'background-color': '#333',
            'transition': 'all 0.3s ease'
        });
    }

    /**
     * イベントリスナーの設定
     */
    function bindMobileMenuEvents() {
        // メニュートグルボタンのクリック
        $document.off('click.mobileMenu', '.mobile-menu-toggle')
                 .on('click.mobileMenu', '.mobile-menu-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile menu toggle clicked');
            toggleMobileMenu();
        });

        // メニュー閉じるボタンのクリック
        $document.off('click.mobileMenuClose', '.mobile-menu-close')
                 .on('click.mobileMenuClose', '.mobile-menu-close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
        });

        // オーバーレイのクリック（メニュー外をクリックしたら閉じる）
        $document.off('click.overlayClose', '.mobile-menu-overlay')
                 .on('click.overlayClose', '.mobile-menu-overlay', function(e) {
            if (e.target === this) {
                closeMobileMenu();
            }
        });

        // メニュー内のリンククリック
        $document.off('click.menuLink', '.mobile-menu-nav a')
                 .on('click.menuLink', '.mobile-menu-nav a', function(e) {
            // 外部リンクでない場合はメニューを閉じる
            const href = $(this).attr('href');
            if (href && !href.startsWith('http') && href !== '#') {
                setTimeout(closeMobileMenu, 100);
            }
        });

        // ESCキーでメニューを閉じる
        $document.off('keydown.mobileMenu')
                 .on('keydown.mobileMenu', function(e) {
            if (e.keyCode === 27 && mobileMenuOpen) {
                closeMobileMenu();
            }
        });

        // タッチイベント（スワイプで閉じる）
        bindTouchEvents();

        // ウィンドウリサイズ時の処理
        $window.off('resize.mobileMenu')
               .on('resize.mobileMenu', debounce(handleWindowResize, 250));
    }

    /**
     * タッチイベントの設定
     */
    function bindTouchEvents() {
        $('.mobile-menu-container').off('touchstart touchend');
        
        $('.mobile-menu-container').on('touchstart', function(e) {
            touchStartY = e.originalEvent.touches[0].clientY;
        });

        $('.mobile-menu-container').on('touchend', function(e) {
            touchEndY = e.originalEvent.changedTouches[0].clientY;
            
            // 右方向にスワイプした場合メニューを閉じる
            if (touchStartY - touchEndY < -50) {
                closeMobileMenu();
            }
        });
    }

    /**
     * モバイルメニューを開く
     */
    function openMobileMenu() {
        if (mobileMenuOpen) return;

        console.log('Opening mobile menu');
        mobileMenuOpen = true;

        // オーバーレイ表示
        $('.mobile-menu-overlay').css('display', 'block');
        
        // アニメーション遅延後にメニューをスライドイン
        setTimeout(() => {
            $('.mobile-menu-container').css('transform', 'translateX(0)');
            $('.mobile-menu-overlay').addClass('active');
        }, 10);

        // ボディスクロール無効化
        $body.addClass('mobile-menu-open').css('overflow', 'hidden');

        // ハンバーガーアイコンのアニメーション
        animateHamburger(true);

        // アクセシビリティ属性更新
        $('.mobile-menu-toggle').attr('aria-expanded', 'true');
        $('.mobile-menu-overlay').attr('aria-hidden', 'false');

        // フォーカス管理
        setTimeout(() => {
            $('.mobile-menu-nav a:first').focus();
        }, 300);
    }

    /**
     * モバイルメニューを閉じる
     */
    function closeMobileMenu() {
        if (!mobileMenuOpen) return;

        console.log('Closing mobile menu');
        mobileMenuOpen = false;

        // メニューをスライドアウト
        $('.mobile-menu-container').css('transform', 'translateX(100%)');
        $('.mobile-menu-overlay').removeClass('active');

        // アニメーション完了後にオーバーレイを非表示
        setTimeout(() => {
            $('.mobile-menu-overlay').css('display', 'none');
        }, 300);

        // ボディスクロール有効化
        $body.removeClass('mobile-menu-open').css('overflow', '');

        // ハンバーガーアイコンのアニメーション
        animateHamburger(false);

        // アクセシビリティ属性更新
        $('.mobile-menu-toggle').attr('aria-expanded', 'false');
        $('.mobile-menu-overlay').attr('aria-hidden', 'true');

        // フォーカスをトグルボタンに戻す
        $('.mobile-menu-toggle').focus();
    }

    /**
     * モバイルメニューの開閉切り替え
     */
    function toggleMobileMenu() {
        if (mobileMenuOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    /**
     * ハンバーガーアイコンのアニメーション
     */
    function animateHamburger(isOpen) {
        const $lines = $('.hamburger-line');
        
        if (isOpen) {
            $lines.eq(0).css({
                'transform': 'rotate(45deg) translate(5px, 5px)'
            });
            $lines.eq(1).css({
                'opacity': '0'
            });
            $lines.eq(2).css({
                'transform': 'rotate(-45deg) translate(7px, -6px)'
            });
        } else {
            $lines.css({
                'transform': 'rotate(0deg) translate(0px, 0px)',
                'opacity': '1'
            });
        }
    }

    /**
     * ウィンドウリサイズ時の処理
     */
    function handleWindowResize() {
        const windowWidth = $window.width();
        
        // デスクトップサイズの場合はモバイルメニューを閉じる
        if (windowWidth >= 768 && mobileMenuOpen) {
            closeMobileMenu();
        }

        // モバイルメニュートグルの表示/非表示
        if (windowWidth >= 768) {
            $('.mobile-menu-toggle').css('display', 'none');
        } else {
            $('.mobile-menu-toggle').css('display', 'flex');
        }
    }

    /**
     * 初期状態のリセット
     */
    function resetMobileMenuState() {
        mobileMenuOpen = false;
        $body.removeClass('mobile-menu-open').css('overflow', '');
        $('.mobile-menu-overlay').css('display', 'none').attr('aria-hidden', 'true');
        $('.mobile-menu-toggle').attr('aria-expanded', 'false');
        animateHamburger(false);
    }

    /**
     * デバウンス関数
     */
    function debounce(func, wait) {
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

    /**
     * エラーハンドリング
     */
    function handleError(error) {
        console.error('Mobile menu error:', error);
        
        // エラー時はメニューを強制的に閉じる
        if (mobileMenuOpen) {
            resetMobileMenuState();
        }
    }

    /**
     * パフォーマンス最適化
     */
    function optimizePerformance() {
        // 不要なイベントリスナーをクリーンアップ
        const cleanup = () => {
            $document.off('.mobileMenu .mobileMenuClose .overlayClose .menuLink');
            $window.off('.mobileMenu');
        };

        // ページ離脱時のクリーンアップ
        $window.on('beforeunload', cleanup);
        
        // Intersection Observerで表示されていない時の処理を軽減
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting && mobileMenuOpen) {
                        // 画面外に出た場合の最適化
                        $('.mobile-menu-container').css('will-change', 'auto');
                    } else if (entry.isIntersecting) {
                        $('.mobile-menu-container').css('will-change', 'transform');
                    }
                });
            });

            const menuElement = document.querySelector('.mobile-menu-overlay');
            if (menuElement) {
                observer.observe(menuElement);
            }
        }
    }

    /**
     * 初期化とエラーハンドリング
     */
    $(document).ready(function() {
        try {
            initMobileMenu();
            optimizePerformance();
            
            // デバッグ情報
            if (typeof gi_ajax !== 'undefined' && gi_ajax.debug) {
                console.log('Mobile menu debug mode enabled');
                console.log('jQuery version:', $.fn.jquery);
                console.log('Window width:', $window.width());
            }
        } catch (error) {
            handleError(error);
        }
    });

    // 外部からアクセス可能な関数をエクスポート
    window.GIMobileMenu = {
        open: openMobileMenu,
        close: closeMobileMenu,
        toggle: toggleMobileMenu,
        isOpen: () => mobileMenuOpen,
        reset: resetMobileMenuState
    };

})(jQuery);

/**
 * 追加のユーティリティ関数
 */

// メニュー項目の動的追加
function addMobileMenuItem(text, url, icon = '') {
    const menuItem = `
        <li class="mobile-menu-item">
            <a href="${url}" class="mobile-menu-link">
                ${icon ? `<i class="${icon}"></i>` : ''}
                <span>${text}</span>
            </a>
        </li>
    `;
    $('.mobile-menu-nav ul').append(menuItem);
}

// 緊急時のメニューリセット
function emergencyResetMobileMenu() {
    console.log('Emergency reset triggered');
    
    // すべてのスタイルとクラスをクリア
    $('body').removeClass('mobile-menu-open').css('overflow', '');
    $('.mobile-menu-overlay').hide().removeClass('active');
    $('.mobile-menu-container').css('transform', 'translateX(100%)');
    
    // イベントリスナーを再初期化
    $(document).trigger('ready');
}

// パフォーマンス監視
if (typeof performance !== 'undefined' && performance.mark) {
    performance.mark('mobile-menu-script-start');
    
    $(window).on('load', function() {
        performance.mark('mobile-menu-script-end');
        performance.measure('mobile-menu-init', 'mobile-menu-script-start', 'mobile-menu-script-end');
        
        const measure = performance.getEntriesByName('mobile-menu-init')[0];
        if (measure) {
            console.log(`Mobile menu initialization took ${measure.duration.toFixed(2)}ms`);
        }
    });
}
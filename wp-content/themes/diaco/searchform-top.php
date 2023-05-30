<?php
/**
 * The template for displaying search results pages
 *
 *
 * @package Diaco
 */

get_header(); ?>

<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<!-- search-box-layout -->
<div class="wraper_flyout_search">
    <div class="table">
        <div class="table-cell">
            <div class="flyout-search-layer"></div>
            <div class="flyout-search-layer"></div>
            <div class="flyout-search-layer"></div>
            <div class="flyout-search-close">
                <span class="flyout-search-close-line"></span>
                <span class="flyout-search-close-line"></span>
            </div>
            <div class="flyout_search">
                <div class="flyout-search-title">
                    <h4><?php esc_html_e( 'Search','diaco' ); ?></h4>
                </div>
                <div class="flyout-search-bar">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <div class="form-row">
                            <input type="search" placeholder="<?php esc_attr_e( 'Type to search...','diaco' ); ?>" value="<?php echo get_search_query(); ?>" name="s" required="">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- search-box-layout end -->
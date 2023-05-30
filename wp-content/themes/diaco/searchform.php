<?php
/**
 * The template for displaying search results pages
 *
 *
 * @package Diaco
 */

get_header(); ?>
<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>
<div class="sidebar-search sidebar-widget">
    <div class="widget-content">
        <div class="form-group">
            <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search"  class="search-field" placeholder="<?php esc_attr_e( 'Type your search','diaco' ); ?>" value="<?php echo get_search_query(); ?>" name="s"  required="">
                <button type="submit"><i class="flaticon-magnifying-glass"></i></button>
            </form>
        </div>
    </div>
</div>

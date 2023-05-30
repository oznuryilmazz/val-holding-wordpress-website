<?php

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

class TNP_Pagination_Controller {

	private $items_per_page;
	private $total_items;
	private $filters;
	private $columns;
	private $controls;
	private $items;
	private $current_page;
	private $last_page;
	private $table;
	private $orderby_column;

	public function __construct( $table, $orderby_column, $filters = [], $items_per_page = 20, $columns = [] ) {
		$this->table          = $table;
		$this->orderby_column = $orderby_column;
		$this->items_per_page = $items_per_page;
		$this->columns        = $columns;
		$this->filters        = $filters;
		$this->total_items    = $this->get_total_items();
		$this->controls       = new NewsletterControls();
		$this->current_page   = $this->compute_current_page();
	}

	public function get_current_page() {
		return $this->current_page;
	}

	private function compute_current_page() {

		$current_page = ! empty( $this->controls->data['search_page'] ) ? (int) $this->controls->data['search_page'] : 1;

		if ( $this->controls->is_action( 'last' ) ) {
			$current_page = $this->get_last_page();
		}
		if ( $this->controls->is_action( 'first' ) || $this->controls->is_action( 'search' ) ) {
			$current_page = 1;
		}
		if ( $this->controls->is_action( 'next' ) ) {
			$current_page ++;
		}
		if ( $this->controls->is_action( 'prev' ) ) {
			$current_page --;
		}

		// Eventually fix the page
		if ( $current_page <= 0 ) {
			$current_page = 1;
		}

		if ( $current_page > $this->get_last_page() ) {
			$current_page = $this->get_last_page();
		}

		$this->controls->data['search_page'] = $current_page;

		return $current_page;

	}

	public function get_last_page() {

		if ( is_null( $this->last_page ) ) {
			$this->last_page = (int) floor( $this->total_items / $this->items_per_page ) + ( $this->total_items % $this->items_per_page > 0 ? 1 : 0 );
		}

		return $this->last_page;

	}

	public function get_items() {

		if ( is_null( $this->items ) ) {
			if ( $this->total_items > 0 ) {
				global $wpdb;
				$query = $wpdb->prepare( "SELECT " . $this->get_columns_query() . " FROM " . $this->table . $this->get_where_clause() . " ORDER BY " . $this->orderby_column . " DESC LIMIT %d,%d",
					( $this->current_page - 1 ) * $this->items_per_page, $this->items_per_page );

				$this->items = $wpdb->get_results( $query, OBJECT );
			} else {
				$this->items = [];
			}
		}

		return $this->items;

	}

	private function get_columns_query() {
		if ( empty( $this->columns ) ) {
			return " * ";
		} else {
			return " " . implode( ',', $this->columns ) . " ";
		}
	}

	public function get_total_items() {

		if ( is_null( $this->total_items ) ) {
			$this->total_items = Newsletter::instance()->store->get_count( $this->table, $this->get_where_clause() );
		}

		return $this->total_items;
	}

	private function get_where_clause() {
		if ( empty( $this->filters ) ) {
			return ' ';
		}

		$where = ' where ';
		foreach ( $this->filters as $column => $value ) {
			$where .= "$column=%s ";
		}

		global $wpdb;
		$where = $wpdb->prepare( $where, array_values( $this->filters ) );

		return $where;
	}

	public function display_paginator() {

		?>

        <div class="tnp-paginator">

			<?php $this->controls->btn( 'first', '«', ['tertiary'=>true] ); ?>
			<?php $this->controls->btn( 'prev', '‹', ['tertiary'=>true] ); ?>
			<?php $this->controls->text( 'search_page', 3 ); ?>
            of <?php echo $this->last_page ?> <?php $this->controls->btn( 'go', __( 'Go', 'newsletter' ), ['secondary'=>true] ); ?>
			<?php $this->controls->btn( 'next', '›', ['tertiary'=>true] ); ?>
			<?php $this->controls->btn( 'last', '»', ['tertiary'=>true] ); ?>
			<?php echo $this->get_total_items() ?> <?php _e( 'newsletter(s) found', 'newsletter' ) ?>

        </div>

		<?php

	}


}

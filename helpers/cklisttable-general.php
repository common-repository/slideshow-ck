<?php
/*  Copyright 2014  Matthew Van Andel  (email : matt@mattvanandel.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class SlideshowCKListTableGeneral extends \Slideshowck\CKListTable {

	/*	 * ******************************************************
	 * Added by Cedric KEIFLIN to get Data from the DB
	 * ******************************************************* */
	function get_data() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		// -- Preparing your query -- 
		$query = "SELECT * FROM $wpdb->posts WHERE post_type = 'slideshowck'";

		// -- Ordering parameters -- 
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? esc_attr($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? esc_attr($_GET["order"]) : '';
		if (!empty($orderby) & !empty($order)) {
			$query.=' ORDER BY ' . $orderby . ' ' . $order;
		}

		// -- Pagination parameters -- 
		//Number of elements in your table?
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		//How many to display per page?
		$perpage = 10;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? (int)$_GET["paged"] : '';
		//Page Number
		if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
			$paged = 1;
		}
		//How many pages do we have in total?
		$totalpages = ceil($totalitems / $perpage);
		//adjust the query to take pagination into account
		if (!empty($paged) && !empty($perpage)) {
			$offset = ($paged - 1) * $perpage;
			$query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		// -- Register the pagination -- 
		$this->set_pagination_args(array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		));
		//The pagination links are automatically built according to those parameters
		// -- Register the Columns -- 
		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id] = $columns;

		// -- Fetch the items -- 
		// $this->items = $wpdb->get_results($query);
		return $wpdb->get_results($query, OBJECT);
	}

	/**	 * ***********************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We 
	 * use the parent reference to set some default configs.
	 * ************************************************************************* */
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct(array(
			'singular' => 'slideshowck', //singular name of the listed records
			'plural' => 'slideshowcks', //plural name of the listed records
			'ajax' => false		//does this table support ajax?
		));
	}

	/**	 * ***********************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title() 
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as 
	 * possible. 
	 * 
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 * 
	 * For more detailed insight into how columns are handled, take a look at 
	 * WP_List_Table::single_row_columns()
	 * 
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 * @return string Text or HTML to be placed inside the column <td>
	 * ************************************************************************ */
	function column_default($item, $column_name) {
		switch ($column_name) {
			case 'post_title' :
				return '<a href="admin.php?page=slideshowck_edit&id=' . $item->ID . '">' . $item->post_title . '</a>';
			case 'post_shortcode' :
				return '[slideshowck id=' . $item->ID . ']';
			default :
				return $item->$column_name;
			case 'debug' :
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**	 * ***********************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Text to be placed inside the column <td> (movie title only)
	 * ************************************************************************ */
	function column_cb($item) {
		return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				/* $1%s */ $this->_args['singular'], //Let's simply repurpose the table's singular label
				/* $2%s */ $item->ID				//The value of the checkbox should be the record's id
		);
	}

	/**	 * ***********************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value 
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 * 
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 * ************************************************************************ */
	function get_columns() {
		return $columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'post_title' => __('Name'),
			'post_shortcode' => __('Shortcode'),
			'ID' => __('ID')
		);
	}

	/**	 * ***********************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
	 * you will need to register it here. This should return an array where the 
	 * key is the column that needs to be sortable, and the value is db column to 
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 * 
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 * 
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 * ************************************************************************ */
	function get_sortable_columns() {
		return $sortable = array(
			'ID' => array('ID', false),
			'post_title' => array('post_title', false),
			'post_status' => array('post_status', false)
		);
	}

	/**	 * ***********************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 * 
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 * 
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 * 
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 * ************************************************************************ */
	function get_bulk_actions() {
		$actions = array(
			'delete' => __('Delete'),
			'copy' => __('Copy'),
			'edit' => __('Edit')
		);
		return $actions;
	}

	function select_first_notice() {
	  ?>
	  <div class="update-nag notice"><div style="clear:both;"></div>
		  <p><?php _e( 'You must select an item for this action.', 'slideshow-ck' ); ?></p>
	  </div>
	  <?php
	}

	/**	 * ***********************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 * 
	 * @see $this->prepare_items()
	 * ************************************************************************ */
	function process_bulk_action() {

		// if (! current_user_can('manage_options')) {
			// wp_die('You are not allowed to edit !');
		// }
		//Detect when a bulk action is being triggered...
		if ('delete' === $this->current_action()) {
			//wp_die('Items deleted (or they would be if we had items to delete)!');
			if (! isset($_GET['slideshowck'][0]) || (int) $_GET['slideshowck'][0] == 0) {
				$this->select_first_notice();
				return;
			}
			// if (current_user_can('edit_plugins', 'delete_page')) {
				wp_delete_post( (int)$_GET['slideshowck'][0] );
			// } else {
				// wp_die('You are not allowed to delete !');
			// }
		} else if ('copy' === $this->current_action()) {
			// if no slideshow selected
			if (! isset($_GET['slideshowck'][0]) || (int) $_GET['slideshowck'][0] == 0) {
				$this->select_first_notice();
				return;
			}
			$post_id = (int)$_GET['slideshowck'][0];
			if ($post_id) {
				$post = get_post($post_id);
				$slideshowckparams = get_post_meta($post_id, 'slideshow-ck-params', TRUE);
				$slideshowckslides = get_post_meta($post_id, 'slideshow-ck-slides', TRUE);

				$ck_post = array(
					'ID' => 0,
					'post_title' => sanitize_text_field($post->post_title . ' (copy)'),
					'post_content' => '',
					'post_type' => 'slideshowck',
					'post_status' => 'publish',
					'comment_status' => 'closed',
					'ping_status' => 'closed'
				);

				// save the post into the database
				$ck_post_id = wp_insert_post($ck_post);

				// Update the meta field for the slideshow settings
				update_post_meta($ck_post_id, 'slideshow-ck-params', $slideshowckparams);
				update_post_meta($ck_post_id, 'slideshow-ck-slides', $slideshowckslides);

				// TODO : ajouter notice en haut de page
				\Slideshowck\CKFof::redirect(home_url() . '/wp-admin/admin.php?page=slideshowck_general');
//				wp_redirect(home_url() . '/wp-admin/admin.php?page=slideshowck_edit&action=updated&id=' . (int) $ck_post_id);
				exit;
			}
		} else if ('edit' === $this->current_action()) {
			// if (current_user_can('edit_plugins', 'delete_page')) {
				wp_redirect( home_url() . '/wp-admin/admin.php?page=slideshowck_edit&id=' . (int) $_GET['slideshowck'][0] );
				\Slideshowck\CKFof::redirect(home_url() . '/wp-admin/admin.php?page=slideshowck_edit&id=' . (int) $_GET['slideshowck'][0]);
			// } else {
				// wp_die('You are not allowed to delete !');
			// }
		}
	}

	/**	 * ***********************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 * 
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 * ************************************************************************ */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
//		$per_page = 5;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column 
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array($columns, $hidden, $sortable);


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example 
		 * package slightly different than one you might build on your own. In 
		 * this example, we'll be using array manipulation to sort and paginate 
		 * our data. In a real-world implementation, you will probably want to 
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->get_data();

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;
	}

}
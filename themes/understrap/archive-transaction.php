<?php

/**
 * The template for displaying archive pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');

function is_valid_date($date, $format = 'Y-m-d')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function is_valid_date_range($query_params)
{
	$fromDate = new DateTime($query_params['startDate']);
	$toDate = new DateTime($query_params['endDate']);

	return $fromDate <= $toDate;
}

// Don't want to duplicate the list of transaction type
global $crb_transaction_type;
$crb_transaction_type_copy = $crb_transaction_type;
$crb_transaction_type_copy['All'] = 'All';

// Don't want to duplicate the list of transaction category
global $crb_transaction_category;
$crb_transaction_category_copy = $crb_transaction_category;
$crb_transaction_category_copy['All'] = 'All';

$crb_transaction_type_copy_js = json_encode($crb_transaction_type_copy);
$crb_transaction_category_copy_js = json_encode($crb_transaction_category_copy);
?>

<script>
	function isValidDate(date) {
		return /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/g.test(date);
	}

	function isValidDateInput(queryParams) {
		if (queryParams.startDate.length <= 0 || !isValidDate(queryParams.startDate)) {
			alert("Invalid start date!");
			return;
		} else if (queryParams.endDate.length <= 0 || !isValidDate(queryParams.endDate)) {
			alert("Invalid end date!");
			return;
		}
		var startDateObj = new Date(queryParams.startDate);
		var endDateObj = new Date(queryParams.endDate);

		return startDateObj <= endDateObj;
	}

	<?php
	echo "var crb_transaction_type_copy_js = " . $crb_transaction_type_copy_js . ";\n";
	echo "var crb_transaction_category_copy_js = " . $crb_transaction_category_copy_js . ";\n";
	echo "var siteUrl = '" . get_post_type_archive_link('transaction') . "';\n";
	?>
	crb_transaction_type_copy_js = Object.keys(crb_transaction_type_copy_js);
	crb_transaction_category_copy_js = Object.keys(crb_transaction_category_copy_js);

	var queryParams = {
		search: 'txn',
		startDate: '',
		endDate: '',
		type: '',
		category: '',
		desc: ''
	};

	function updateQuery() {
		var queryString = Object.keys(queryParams).map(key => key + '=' + queryParams[key]).join('&');
		return '?' + queryString;
	}

	function handleChange(key, value) {
		queryParams[key] = value;
	}

	function handleSearch() {
		for (const key in queryParams) {
			switch (key) {
				case 'search':
					if (queryParams[key] !== 'txn') {
						alert('Invalid search!');
						return;
					}
					break;
				case 'startDate':
					var isValidStartDate = isValidDateInput(queryParams);
					if (!isValidStartDate) {
						alert('Transaction start date has to be earlier than end date!');
						return;
					}
					break;
				case 'endDate':
					var isValidEndDate = isValidDateInput(queryParams);
					if (!isValidEndDate) {
						alert('Transaction start date has to be earlier than end date!');
						return;
					}
					break;
				case 'type':
					if (!crb_transaction_type_copy_js.includes(queryParams[key])) {
						alert('Invalid transaction type!');
						return;
					}
					break;
				case 'category':
					if (!crb_transaction_category_copy_js.includes(queryParams[key])) {
						alert('Invalid transaction Category!');
						return;
					}
					break;
				// case 'desc':
				// 	if (queryParams.desc.length <= 0) {
				// 		alert('Invalid transaction description!');
				// 		return;
				// 	}
				// 	break;
				default:
					break;
			}
		}

		const query = updateQuery();

		window.location.href = siteUrl + encodeURI(query);
	}

	function handleEvent(event, key) {
		handleChange(key, event.target ? event.target.value : event.value);
	}
</script>

<div class="wrapper" id="transaction-wrapper">

	<div class="<?php echo esc_attr($container); ?> mb-5" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo get_home_url(); ?>">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php post_type_archive_title(); ?></li>
				</ol>
			</nav>

			<?php
			if (!is_user_logged_in()):
				get_template_part('loop-templates/content', 'none');
			else:
				$query_params = array(
					'search' => get_query_var('search'),
					'startDate' => get_query_var('startDate'),
					'endDate' => get_query_var('endDate'),
					'type' => get_query_var('type'),
					'category' => get_query_var('category'),
					'desc' => get_query_var('desc'),
				);

				$is_valid_params = true;
				foreach ($query_params as $key => $value) {
					if ($key != 'desc' && empty($value)) {
						$is_valid_params = false;
						break;
					}

					if ($key == 'search' && $value != 'txn') {
						$is_valid_params = false;
						break;
					} else if ($key == 'startDate' && !is_valid_date($value)) {
						$is_valid_params = false;
						break;
					} else if ($key == 'endDate' && !is_valid_date($value)) {
						$is_valid_params = false;
						break;
					} else if ($key == 'type' && !in_array($value, $crb_transaction_type_copy)) {
						$is_valid_params = false;
						break;
					} else if ($key == 'category' && !in_array($value, $crb_transaction_category_copy)) {
						$is_valid_params = false;
						break;
					}
				}

				if (!is_valid_date_range($query_params)) {
					$is_valid_params = false;
				}

				if ($is_valid_params):
					get_template_part(
						'loop-templates/transaction/content',
						'search-results',
						array('query_params' => $query_params)
					);
				else:
					get_template_part(
						'loop-templates/transaction/content',
						'search',
						array(
							'crb_transaction_type_copy' => $crb_transaction_type_copy,
							'crb_transaction_category_copy' => $crb_transaction_category_copy
						)
					);

					get_template_part(
						'loop-templates/transaction/content',
						'archive'
					);
				endif;

			endif;
			?>

		</main>

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();

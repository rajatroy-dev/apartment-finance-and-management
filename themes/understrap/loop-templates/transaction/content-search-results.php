<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$query_params = $args['query_params'];
$desc_inp = $query_params['desc'];
$query_params['desc'] = sanitize_text_field($desc_inp);

$search_args = array(
	'relation' => 'AND',
	array(
		'key' => 'crb_transaction_date',
		'value' => array($query_params['startDate'], $query_params['endDate']),
		'compare' => 'BETWEEN',
		'type' => 'DATE'
	)
);

if ($query_params['type'] !== 'All') {
	$search_args[] = array(
		'key' => 'crb_transaction_type',
		'value' => $query_params['type'],
		'compare' => '='
	);
}

if ($query_params['category'] !== 'All') {
	$search_args[] = array(
		'key' => 'crb_transaction_category',
		'value' => $query_params['category'],
		'compare' => '='
	);
}

if (empty($query_params['desc'])) {
	$search_query = new WP_Query(array(
		'post_type' => 'transaction',
		'post_status' => ['private'],
		'posts_per_page' => -1,
		'meta_query'    => $search_args
	));
} else {
	$search_query = new WP_Query(array(
		'post_type' => 'transaction',
		'post_status' => ['private'],
		'posts_per_page' => -1,
		's' => $query_params['desc'],
		'search_columns' => ['post_title'],
		'meta_query'    => $search_args
	));
}


$total_credit = 0.00;
$total_debit = 0.00;
?>

<div class="accordion mb-3" id="accordionResults">
	<div class="accordion-item">
		<h2 class="accordion-header">
			<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResults" aria-expanded="true" aria-controls="collapseResults">
				Showing transaction results for
			</button>
		</h2>
		<div id="collapseResults" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
			<div class="accordion-body">
				<table class="table">
					<tbody>
						<tr>
							<th scope="row">From</th>
							<td><?php echo $query_params['startDate'] ?></td>
						</tr>
						<tr>
							<th scope="row">To</th>
							<td><?php echo $query_params['endDate'] ?></td>
						</tr>
						<tr>
							<th scope="row">Type</th>
							<td><?php echo $query_params['type'] ?></td>
						</tr>
						<tr>
							<th scope="row">Category</th>
							<td><?php echo $query_params['category'] ?></td>
						</tr>
						<tr>
							<th scope="row">Description</th>
							<td><?php echo $query_params['desc'] ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="row">

	<?php
	$posts = array();
	while ($search_query->have_posts()):
		$search_query->the_post();

		$fromUserId = carbon_get_the_post_meta('crb_transaction_from_user')[0]['id'];
		$fromUser = get_user_by('id', esc_html($fromUserId));
		$toUserId = carbon_get_the_post_meta('crb_transaction_to_user')[0]['id'];
		$toUser = get_user_by('id', esc_html($toUserId));

		$post = array(
			'id' => get_the_ID(),
			'date' => esc_html(carbon_get_the_post_meta('crb_transaction_date')),
			'type' => esc_html(carbon_get_the_post_meta('crb_transaction_type')),
			'category' => esc_html(carbon_get_the_post_meta('crb_transaction_category')),
			'desc' => esc_html(get_the_title()),
			// https://stackoverflow.com/questions/17028946/php-number-format-without-comma
			'amount' => (float) number_format(esc_html(carbon_get_the_post_meta('crb_transaction_amount')), 2, '.', ''),
			'from' => $fromUser->display_name,
			'to' => $toUser->display_name,
			'receipts' => carbon_get_the_post_meta('crb_transaction_receipt')
		);

		$transaction_amount = $post['amount'];
		if ($post['type'] == 'Credit') {
			$total_credit = $total_credit + $transaction_amount;
		} else if ($post['type'] == 'Debit') {
			$total_debit = $total_debit + $transaction_amount;
		}

		array_push($posts, $post);
	endwhile;
	?>


	<table class="table mb-3">
		<tbody>
			<tr>
				<th scope="row">Total Credit</th>
				<td>₹<?php echo $total_credit ?></td>
			</tr>
			<tr>
				<th scope="row">Total Debit</th>
				<td>₹<?php echo $total_debit ?></td>
			</tr>
		</tbody>
	</table>

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Date</th>
					<th scope="col">Type</th>
					<th style="min-width: 12em;" scope="col">Category</th>
					<th style="min-width: 26em;" scope="col">Description</th>
					<th scope="col">Amount</th>
					<th scope="col">From</th>
					<th scope="col">To</th>
					<th scope="col">Receipts</th>
				</tr>
			</thead>
			<tbody class="table-group-divider">

				<?php
				// Start the loop.
				foreach ($posts as $post):
				?>

					<tr>
						<th scope="row"><?php echo $post['id']; ?></th>
						<td><?php echo $post['date']; ?></td>
						<td><?php echo $post['type']; ?></td>
						<td><?php echo $post['category']; ?></td>
						<td><?php echo $post['desc']; ?></td>
						<td>₹<?php echo $post['amount']; ?></td>
						<td><?php echo $post['from']; ?></td>
						<td><?php echo $post['to']; ?></td>

						<td>

							<?php $receipts = $post['receipts'] ?>

							<?php if (!empty($receipts)):
								$count = 0;
								foreach ($receipts as $receipt):
									$img = $receipt['receipt_image'];
									$img_url = wp_get_attachment_image_url($img, '');
							?>

									<a class="ms-1" href="<?php echo $img_url ?>" target="_blank"
										rel="noopener noreferrer"><?php echo ++$count; ?></a>,

							<?php
								endforeach;
							endif;
							?>

						</td>
					</tr>

				<?php
				endforeach;
				?>

			</tbody> <!-- table-group-divider -->
		</table> <!-- .table -->
	</div> <!-- .table-responsive -->

</div><!-- .row -->
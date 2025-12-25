<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$crb_transaction_type_copy = $args['crb_transaction_type_copy'];
$crb_transaction_category_copy = $args['crb_transaction_category_copy'];
?>

<div class="accordion mb-4" id="accordionSearch">
	<div class="accordion-item">
		<h2 class="accordion-header">

			<button
				class="accordion-button collapsed"
				type="button"
				data-bs-toggle="collapse"
				data-bs-target="#collapseSearch"
				aria-expanded="false"
				aria-controls="collapseSearch">
				Search
			</button>

		</h2>
		<div
			id="collapseSearch"
			class="accordion-collapse collapse"
			data-bs-parent="#accordionSearch">

			<div class="accordion-body">
				<div class="mb-2">
					<div class="row">
						<div class="col">
							<label for="startDate">From</label>
							<input id="startDate" class="form-control" type="date" onchange="handleEvent(event, 'startDate');" />
						</div>
						<div class="col">
							<label for="endDate">To</label>
							<input id="endDate" class="form-control" type="date" onchange="handleEvent(event, 'endDate');" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-sm-6 mb-2">

						<select class="form-select" aria-label="Transaction Type" onchange="handleEvent(this, 'type');">
							<option selected>-- Transaction Type --</option>
							<?php
							foreach ($crb_transaction_type_copy as $key => $value):
							?>
								<option value="<?php echo esc_html($value); ?>"><?php echo esc_html($key); ?></option>
							<?php
							endforeach;
							?>
						</select>

					</div>
					<div class="col-12 col-sm-6 mb-2">

						<select class="form-select" aria-label="Transaction Category" onchange="handleEvent(this, 'category');">
							<option selected>-- Transaction Category --</option>
							<?php
							foreach ($crb_transaction_category_copy as $key => $value):
							?>
								<option value="<?php echo esc_html($value); ?>"><?php echo esc_html($key); ?></option>
							<?php
							endforeach;
							?>
						</select>

					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="mb-2">
							<input
								type="text"
								class="form-control"
								id="formGroupExampleInput"
								placeholder="Transaction Description"
								onchange="handleEvent(event, 'desc');">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="mb-2">
							<button type="button" class="btn btn-primary w-100" onclick="handleSearch();">SEARCH</button>
						</div>
					</div>
				</div>
			</div> <!-- .accordion-body -->

		</div> <!-- #collapseSearch -->
	</div> <!-- .accordion-item -->
</div> <!-- #accordionSearch -->
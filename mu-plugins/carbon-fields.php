<?php

/**
 * Custom fields using Carbon Fields
 * https://docs.carbonfields.net/learn/containers/usage.html
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_attach_actionitem_fields');
add_action('carbon_fields_register_fields', 'crb_attach_transaction_fields');
add_action('carbon_fields_register_fields', 'crb_attach_passbook_fields');
add_action('carbon_fields_register_fields', 'crb_attach_pending_payment_fields');
add_action('carbon_fields_register_fields', 'crb_attach_transaction_audit_fields');

function crb_attach_actionitem_fields()
{
	Container::make('post_meta', 'Additional fields for an action item')
		->where('post_type', '=', 'actionitem')
		->add_fields(array(
			Field::make('complex', 'crb_actionitem_featured_image', __('Action Item Featured Images'))
				->set_required(true)
				->set_layout('tabbed-horizontal')
				->add_fields(array(
					Field::make('image', 'actionitem_image', 'Item Image')
				)),
			Field::make('select', 'crb_actionitem_priority', __('Action Item Priority'))
				->set_options(array(
					'Normal' => 'Normal',
					'High' => 'High',
					'Urgent' => 'Urgent',
				))
				->set_required(true),
			Field::make('select', 'crb_actionitem_status', __('Action Item Status'))
				->set_options(array(
					'Not Started' => 'Not Started',
					'In Progress' => 'In Progress',
					'Done' => 'Done',
				))
				->set_required(true),
			Field::make('text', 'crb_actionitem_least_quotation', __('Action Item Least Quotation'))
				->set_attribute('type', 'number')
				->set_attribute('readOnly', 'true')
				->set_default_value(0),
			Field::make('text', 'crb_actionitem_highest_quotation', __('Action Item Highest Quotation'))
				->set_attribute('type', 'number')
				->set_attribute('readOnly', 'true')
				->set_default_value(0),
			Field::make('complex', 'crb_actionitem_quotation', __('Action Item Quotation'))
				->set_layout('tabbed-horizontal')
				->add_fields(array(
					Field::make('image', 'quotation_image', __('Action Item Quotation Image'))
						->set_required(true),
					Field::make('text', 'quotation_amount', __('Action Item Quotation Amount'))
						->set_required(true),
					Field::make('text', 'quotation_proposer', __('Action Item Quotation Proposer'))
						->set_required(true),
					Field::make('text', 'quotation_approval_status', __('Action Item Quotation Approval Status'))
						->set_attribute('readOnly', 'true')
						->set_default_value('Pending')
						->set_required(true),
				))
				->set_required(true),
		));
}

$crb_transaction_category = array(
	'Regular Maintenance' => 'Regular Maintenance',
	'Adhoc Repair' => 'Adhoc Repair',
	'Handover' => 'Handover',
	'Passbook' => 'Passbook'
);
$crb_transaction_type = array(
	'Credit' => 'Credit',
	'Debit' => 'Debit',
);
function crb_attach_transaction_fields()
{
	Container::make('post_meta', 'Additional fields for a transaction')
		->where('post_type', '=', 'transaction')
		->add_fields(array(
			Field::make('date', 'crb_transaction_date', 'Transaction Date')
				->set_required(true),
			Field::make('select', 'crb_transaction_type', __('Transaction Type'))
				->set_options(array(
					'Credit' => 'Credit',
					'Debit' => 'Debit',
				))
				->set_required(true),
			Field::make('select', 'crb_transaction_category', __('Transaction Category'))
				->set_options(array(
					'Regular Maintenance' => 'Regular Maintenance',
					'Adhoc Repair' => 'Adhoc Repair',
					'Handover' => 'Handover',
					'Passbook' => 'Passbook'
				))
				->set_required(true),
			Field::make('text', 'crb_transaction_amount', __('Transaction Amount'))
				->set_attribute('type', 'number')
				->set_required(true)
				->set_default_value(0),
			Field::make('association', 'crb_transaction_from_user', __('From Account Holder'))
				->set_types(array(
					array('type' => 'user')
				))
				->set_required(true)
				->set_min(1)
				->set_max(1),
			Field::make('association', 'crb_transaction_to_user', __('To Account Holder'))
				->set_types(array(
					array('type' => 'user')
				))
				->set_required(true)
				->set_min(1)
				->set_max(1),
			Field::make('complex', 'crb_transaction_receipt', __('Transaction Receipts'))
				->set_layout('tabbed-horizontal')
				->add_fields(array(
					Field::make('image', 'receipt_image', __('Receipt Image')),
				)),
		));
}

function crb_attach_passbook_fields()
{
	Container::make('post_meta', 'Additional fields for a passbook entry')
		->where('post_type', '=', 'passbook')
		->add_fields(array(
			Field::make('date', 'crb_passbook_month', 'For The Month Of')
				->set_required(true),
			Field::make('text', 'crb_passbook_amount', __('Current Passbook Balance'))
				->set_attribute('type', 'number')
				->set_required(true)
				->set_default_value(0),
			Field::make('complex', 'crb_passbook_entry', __('Passbook Entries'))
				->set_layout('tabbed-horizontal')
				->set_required(true)
				->add_fields(array(
					Field::make('image', 'passbook_image', __('Passbook Image'))
						->set_required(true),
				)),
		));
}

function crb_attach_pending_payment_fields()
{
	Container::make('post_meta', 'Additional fields for a pending payment')
		->where('post_type', '=', 'pending-payment')
		->add_fields(array(
			Field::make('text', 'crb_pending_amount', __('Pending Amount'))
				->set_attribute('type', 'number')
				->set_required(true)
				->set_default_value(0),
			Field::make('association', 'crb_pending_with_user', __('Pending With User'))
				->set_types(array(
					array('type' => 'user')
				))
				->set_required(true)
				->set_min(1)
				->set_max(1),
			Field::make('complex', 'crb_clearance_receipt', __('Clearance Receipts'))
				->set_layout('tabbed-horizontal')
				->add_fields(array(
					Field::make('image', 'receipt_image', __('Receipt Image')),
				)),
		));
}

function crb_attach_transaction_audit_fields()
{
	Container::make('post_meta', 'Additional fields for a transaction audit')
		->where('post_type', '=', 'transaction-audit')
		->add_fields(array(
			Field::make('date', 'crb_transaction_audit_date', 'Transaction Audit Date'),
			Field::make('select', 'crb_transaction_audit_type', __('Transaction Audit Type'))
				->set_options(array(
					'Credit' => 'Credit',
					'Debit' => 'Debit',
				)),
			Field::make('select', 'crb_transaction_audit_category', __('Transaction Audit Category'))
				->set_options(array(
					'Regular Maintenance' => 'Regular Maintenance',
					'Adhoc Repair' => 'Adhoc Repair',
					'Handover' => 'Handover',
					'Passbook' => 'Passbook'
				)),
			Field::make('text', 'crb_transaction_audit_amount', __('Transaction Audit Amount'))
				->set_attribute('type', 'number')
				->set_default_value(0),
			Field::make('text', 'crb_transaction_audit_ref', __('Transaction Reference'))
				->set_attribute('type', 'number')
				->set_default_value(0),
			Field::make('association', 'crb_transaction_audit_from_user', __('From Account Holder'))
				->set_types(array(
					array('type' => 'user')
				))
				->set_min(1)
				->set_max(1),
			Field::make('association', 'crb_transaction_audit_to_user', __('To Account Holder'))
				->set_types(array(
					array('type' => 'user')
				))
				->set_min(1)
				->set_max(1),
			Field::make('complex', 'crb_transaction_audit_receipt', __('Transaction Audit Receipts'))
				->set_layout('tabbed-horizontal')
				->add_fields(array(
					Field::make('image', 'receipt_image', __('Receipt Image')),
				)),
		));
}

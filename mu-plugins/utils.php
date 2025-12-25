<?php

function apartment_body_class($classes)
{
	$classes[] = 'mb-5';
	return $classes;
}

add_filter('body_class', 'apartment_body_class');

add_filter('private_title_format', function ($format) {
	return '%s';
});

class ActionItem
{
	public static function statusIcon($status)
	{
		switch ($status) {
			case 'Not Started':
				return 'text-bg-danger';
			case 'In Progress':
				return 'text-bg-warning';
			case 'Done':
				return 'text-bg-success';

			default:
				return 'text-bg-danger';
		}
	}

	public static function approvalStatusIcon($status)
	{
		switch ($status) {
			case 'Rejected':
				return 'text-bg-danger';
			case 'Pending':
				return 'text-bg-warning';
			case 'Approved':
				return 'text-bg-success';

			default:
				return 'text-bg-danger';
		}
	}

	public static function priorityIcon($priority)
	{
		switch ($priority) {
			case 'Normal':
				return 'bi-chevron-up text-warning';
			case 'High':
				return 'bi-chevron-double-up text-danger-emphasis';
			case 'Urgent':
				return 'bi-arrow-up text-danger';

			default:
				return 'bi-chevron-up text-warning';
		}
	}
}

function is_null_or_empty_string(string|null $str)
{
	return $str === null || trim($str) === '';
}

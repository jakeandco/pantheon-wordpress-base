/**
 * Admin JavaScript for Airtable Sync plugin
 */

(function($) {
	'use strict';

	/**
	 * Initialize the admin functionality
	 */
	function init() {
		// Load bases button
		$('#airtable_sync_load_bases').on('click', loadBases);

		// Validate configuration button
		$('#airtable_sync_validate').on('click', validateConfig);

		// Field inspector: Load tables when page loads if credentials exist
		if ($('#airtable_inspector_table').length) {
			loadTablesForInspector();
		}

		// Field inspector: Enable/disable load button based on table selection
		$('#airtable_inspector_table').on('change', function() {
			const hasTable = $(this).val() !== '';
			$('#airtable_sync_load_table_fields').prop('disabled', !hasTable);
		});

		// Field inspector: Load fields button
		$('#airtable_sync_load_table_fields').on('click', loadTableFields);

		// Sync Now buttons
		$('.airtable-sync-now-btn').on('click', function() {
			runSync($(this));
		});

		// Monitor API key changes to enable/disable base selector
		$('#airtable_sync_api_key').on('input', function() {
			const hasApiKey = $(this).val().trim() !== '';
			$('#airtable_sync_base_id').prop('disabled', !hasApiKey);
			$('#airtable_sync_load_bases').prop('disabled', !hasApiKey);
		});
	}

	/**
	 * Load Airtable bases
	 */
	function loadBases() {
		const $button = $('#airtable_sync_load_bases');
		const $select = $('#airtable_sync_base_id');
		const $loading = $('#airtable_sync_base_loading');
		const apiKey = $('#airtable_sync_api_key').val();

		if (!apiKey) {
			alert('Please enter an API key first.');
			return;
		}

		// Show loading state
		$button.prop('disabled', true);
		$loading.show();

		$.ajax({
			url: airtableSyncAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'airtable_sync_get_bases',
				nonce: airtableSyncAdmin.nonce,
				api_key: apiKey
			},
			success: function(response) {
				if (response.success && response.data.bases) {
					// Clear existing options except the first
					$select.find('option:not(:first)').remove();

					// Add bases to select
					response.data.bases.forEach(function(base) {
						$select.append(
							$('<option></option>')
								.attr('value', base.id)
								.text(base.name)
						);
					});

					showMessage('Bases loaded successfully!', 'success');
				} else {
					const errorMsg = response.data && response.data.message
						? response.data.message
						: 'Failed to load bases.';
					showMessage(errorMsg, 'error');
				}
			},
			error: function(xhr, status, error) {
				showMessage('Error loading bases: ' + error, 'error');
			},
			complete: function() {
				$button.prop('disabled', false);
				$loading.hide();
			}
		});
	}

	/**
	 * Validate configuration
	 */
	function validateConfig() {
		const $button = $('#airtable_sync_validate');

		$button.prop('disabled', true).text('Validating...');

		$.ajax({
			url: airtableSyncAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'airtable_sync_validate_config',
				nonce: airtableSyncAdmin.nonce
			},
			success: function(response) {
				if (response.success) {
					showMessage(response.data.message, 'success');
					// Reload page to show updated validation status
					setTimeout(function() {
						window.location.reload();
					}, 1000);
				} else {
					const errorMsg = response.data && response.data.message
						? response.data.message
						: 'Validation failed.';
					showMessage(errorMsg, 'error');

					if (response.data && response.data.errors && response.data.errors.length > 0) {
						// Show errors in console for developer reference
						console.error('Configuration errors:', response.data.errors);
					}
				}
			},
			error: function(xhr, status, error) {
				showMessage('Error validating configuration: ' + error, 'error');
			},
			complete: function() {
				$button.prop('disabled', false).text('Revalidate Configuration');
			}
		});
	}

	/**
	 * Load tables for field inspector
	 */
	function loadTablesForInspector() {
		const $select = $('#airtable_inspector_table');

		// Check if select exists (may not be visible if credentials aren't set)
		if ($select.length === 0) {
			return;
		}

		$.ajax({
			url: airtableSyncAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'airtable_sync_get_tables',
				nonce: airtableSyncAdmin.nonce
			},
			success: function(response) {
				if (response.success && response.data.tables) {
					// Clear existing options
					$select.empty();
					$select.append('<option value="">Select a table...</option>');

					// Add tables to select
					response.data.tables.forEach(function(table) {
						$select.append(
							$('<option></option>')
								.attr('value', table.id)
								.text(table.name)
								.data('table', table)
						);
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Error loading tables for inspector:', error);
			}
		});
	}

	/**
	 * Load table fields for inspection
	 */
	function loadTableFields() {
		const $button = $('#airtable_sync_load_table_fields');
		const $loading = $('#airtable_inspector_loading');
		const $results = $('#airtable_inspector_results');
		const $tbody = $('#airtable_inspector_fields');
		const tableId = $('#airtable_inspector_table').val();

		if (!tableId) {
			return;
		}

		// Show loading state
		$button.prop('disabled', true);
		$loading.show();
		$results.hide();

		$.ajax({
			url: airtableSyncAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'airtable_sync_get_table_schema',
				nonce: airtableSyncAdmin.nonce,
				table_id: tableId
			},
			success: function(response) {
				if (response.success && response.data.fields) {
					$tbody.empty();

					// Add each field to the table
					response.data.fields.forEach(function(field) {
						const $row = $('<tr></tr>');

						$row.append(
							$('<td></td>').html(
								'<strong>' + escapeHtml(field.name) + '</strong>'
							)
						);

						$row.append(
							$('<td></td>').html(
								'<code class="airtable-field-id">' + escapeHtml(field.id) + '</code>' +
								' <button type="button" class="button button-small copy-field-id" data-field-id="' + escapeHtml(field.id) + '" title="Copy field ID">Copy</button>'
							)
						);

						$row.append(
							$('<td></td>').html(
								'<span class="field-type-badge">' + escapeHtml(field.type) + '</span>'
							)
						);

						$tbody.append($row);
					});

					// Add copy functionality
					$('.copy-field-id').on('click', function() {
						const fieldId = $(this).data('field-id');
						copyToClipboard(fieldId);
						$(this).text('Copied!').prop('disabled', true);
						setTimeout(() => {
							$(this).text('Copy').prop('disabled', false);
						}, 2000);
					});

					$results.show();
					showMessage('Fields loaded successfully! Copy field IDs below.', 'success');
				} else {
					const errorMsg = response.data && response.data.message
						? response.data.message
						: 'Failed to load fields.';
					showMessage(errorMsg, 'error');
				}
			},
			error: function(xhr, status, error) {
				showMessage('Error loading table fields: ' + error, 'error');
			},
			complete: function() {
				$button.prop('disabled', false);
				$loading.hide();
			}
		});
	}

	/**
	 * Run sync for a specific table
	 */
	function runSync($button) {
		const tableId = $button.data('table-id');
		const $mappingDisplay = $button.closest('.airtable-sync-mapping-display');
		const $syncStatus = $mappingDisplay.find('.sync-status');
		const $syncMessage = $syncStatus.find('.sync-message');
		const $syncResults = $mappingDisplay.find('.sync-results');

		// Disable button and show loading state
		$button.prop('disabled', true);
		$syncStatus.show();
		$syncMessage.text('Syncing records...');
		$syncResults.hide();

		$.ajax({
			url: airtableSyncAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'airtable_sync_run_sync',
				nonce: airtableSyncAdmin.nonce,
				table_id: tableId
			},
			success: function(response) {
				if (response.success) {
					// Update sync message
					$syncMessage.text(response.data.message);

					// Show results
					if (response.data.stats) {
						displaySyncResults($mappingDisplay, response.data.stats, response.data.hasErrors);
					}

					// Show success or warning message at top
					if (response.data.hasErrors) {
						showMessage(response.data.message, 'warning');
					} else {
						showMessage(response.data.message, 'success');
					}

					// Hide sync status after delay
					setTimeout(function() {
						$syncStatus.fadeOut();
					}, 2000);
				} else {
					const errorMsg = response.data && response.data.message
						? response.data.message
						: 'Sync failed.';
					$syncMessage.text(errorMsg);
					showMessage(errorMsg, 'error');

					// Hide sync status after delay
					setTimeout(function() {
						$syncStatus.fadeOut();
					}, 3000);
				}
			},
			error: function(xhr, status, error) {
				const errorMsg = 'Error running sync: ' + error;
				$syncMessage.text(errorMsg);
				showMessage(errorMsg, 'error');

				// Hide sync status after delay
				setTimeout(function() {
					$syncStatus.fadeOut();
				}, 3000);
			},
			complete: function() {
				$button.prop('disabled', false);
			}
		});
	}

	/**
	 * Display sync results
	 */
	function displaySyncResults($mappingDisplay, stats, hasErrors) {
		const $syncResults = $mappingDisplay.find('.sync-results');

		// Update stat values
		$syncResults.find('[data-stat="processed"]').text(stats.processed || 0);
		$syncResults.find('[data-stat="created"]').text(stats.created || 0);
		$syncResults.find('[data-stat="updated"]').text(stats.updated || 0);
		$syncResults.find('[data-stat="skipped"]').text(stats.skipped || 0);
		$syncResults.find('[data-stat="unpublished"]').text(stats.unpublished || 0);
		$syncResults.find('[data-stat="errors"]').text(stats.errors || 0);

		// Add status class
		$syncResults.removeClass('has-errors has-success');
		$syncResults.addClass(hasErrors ? 'has-errors' : 'has-success');

		// Show the results
		$syncResults.slideDown();
	}

	/**
	 * Copy text to clipboard
	 */
	function copyToClipboard(text) {
		// Modern approach
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).catch(function(err) {
				console.error('Failed to copy:', err);
				fallbackCopy(text);
			});
		} else {
			fallbackCopy(text);
		}
	}

	/**
	 * Fallback copy method
	 */
	function fallbackCopy(text) {
		const $temp = $('<textarea>');
		$('body').append($temp);
		$temp.val(text).select();
		try {
			document.execCommand('copy');
		} catch (err) {
			console.error('Fallback copy failed:', err);
		}
		$temp.remove();
	}

	/**
	 * Show a temporary message
	 */
	function showMessage(message, type) {
		const $message = $('<div></div>')
			.addClass('notice notice-' + type + ' is-dismissible')
			.html('<p>' + escapeHtml(message) + '</p>')
			.insertAfter('.wrap h1');

		// Add dismiss functionality
		$message.append('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button>');

		$message.find('.notice-dismiss').on('click', function() {
			$message.fadeOut(function() {
				$(this).remove();
			});
		});

		// Auto-dismiss after 5 seconds
		setTimeout(function() {
			$message.fadeOut(function() {
				$(this).remove();
			});
		}, 5000);
	}

	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
	}

	// Initialize when document is ready
	$(document).ready(init);

})(jQuery);

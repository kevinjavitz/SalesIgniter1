/**
* Table Transpose v0.1.0: Transposes HTML Tables
* Copyright (c) 2010 Jason Tran, licensed under MIT/GPL
*/
jQuery.fn.tableTranspose = function(options) {
	var mOptions = jQuery.extend(true, jQuery.fn.tableTranspose.options, options);

	return this.each(function() {
		var oldTable = jQuery(this);
		var oldRows = oldTable.children('tbody' + (mOptions.includeTHEAD ? ', thead' : '')).children('tr');
		var oldColumns = 0;
		
		var newTable = [];

		/* Find the maximum number of columns in the old table */
		oldRows.each(function() {
			oldColumns = Math.max(jQuery(this).children('th, td').size(), oldColumns);
		});

		/* Create new DOM rows for each of the old columns */
		for (var counter = 0; counter < oldColumns; counter++) {
			newTable.push(jQuery('<tr></tr>'));
		}

		/* Transpose */
		oldRows.each(function(index, object) {
			jQuery(this).children('th, td').each(function(index, object) {
				if (mOptions.swapSpans) {
					var rowspan = jQuery(object).attr('colspan') || 1;
					var colspan = jQuery(object).attr('rowspan') || 1;
			
					if (rowspan > 1 || colspan > 1) {
						jQuery(object).attr({rowspan: rowspan, colspan: colspan});
					}
				}

				newTable[index].append(object);
			});
		});

		/* Clean up the old table and re-insert */
		jQuery(oldTable).children('tbody' + (mOptions.includeTHEAD ? ', thead' : '')).remove();

		jQuery(newTable).each(function(index, object) {
			oldTable.append(object);
		});
	});
};

jQuery.fn.tableTranspose.options = {
	includeTHEAD: true,
	swapSpans: true
};
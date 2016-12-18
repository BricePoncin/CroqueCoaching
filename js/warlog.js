/* JAVASCRIPT CONTRACTS 2 */
	
        $(document).ready(function () {
			// DataTable
			var datatableInstance = $('#tab_defenses').DataTable({
				'sortable': true,
				'searchable': false,
				'paging': false,
				'scrollX': false,
				'scrollY': 300
			});
        });

        $(document).ready(function () {
			// DataTable
			var datatableInstance = $('#tab_attaques').DataTable({
				'sortable': true,
				'searchable': false,
				'paging': false,
				'scrollX': false,
				'scrollY': 300
			});
        });
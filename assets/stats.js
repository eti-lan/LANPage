jQuery.noConflict();
var $j = jQuery;

$j(document).ready(function() {
    $j(window).load(function() {
        $j("#stats").DataTable({
            paging: 1,
            info: 0,
            searching: 0,
			pageLength: 50,
            columnDefs: [{
                targets: [0, 1, 2, 7],
                orderable: 1
            }],
            order: [
                [7, "desc"]
            ],
            language: {
                sEmptyTable: "Keine Daten in der Tabelle vorhanden",
                sInfo: "_START_ bis _END_ von _TOTAL_ Einträgen",
                sInfoEmpty: "0 bis 0 von 0 Einträgen",
                sInfoFiltered: "(gefiltert von _MAX_ Einträgen)",
                sInfoPostFix: "",
                sInfoThousands: ".",
                sLengthMenu: "_MENU_ ",
                sLoadingRecords: "Wird geladen...",
                sProcessing: "Bitte warten...",
                sSearch: "Suchen",
                sZeroRecords: "Keine Einträge vorhanden.",
                oPaginate: {
                    sFirst: "Erste",
                    sPrevious: "Zurück",
                    sNext: "Nächste",
                    sLast: "Letzte"
                }
            }
        })
    })
});

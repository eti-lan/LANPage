<?php
$catering['title'] = 'Catering';
$catering['new_order'] = 'Neue Bestellung aufgeben';
$catering['current_orders'] = 'Aktuelle Bestellungen heute';
$catering['name'] = 'Name';
$catering['flyer_number'] = 'Nummer auf Flyer';
$catering['quantity'] = 'Anzahl';
$catering['size'] = 'Größe';
$catering['size_normal'] = 'Normal';
$catering['size_small'] = 'Klein';
$catering['size_big'] = 'Groß';
$catering['comments'] = 'Bemerkungen';
$catering['price'] = 'Einzelpreis inkl. Lieferung';
$catering['order_button'] = 'Bestellen';
$catering['orders_summary'] = 'Zusammenfassung';
$catering['status_legend'] = 'Status-Legende';
$catering['status_new'] = 'Neu';
$catering['status_processing'] = 'In Bearbeitung';
$catering['status_completed'] = 'Fertig';
$catering['total_today'] = 'Gesamt heute:';
$catering['orders_count'] = 'Bestellungen:';
$catering['view_flyer'] = 'Aktuellen Flyer "%s" anzeigen';
$catering['admin_panel'] = 'Zur Verwaltung';
$catering['time'] = 'Zeit';
$catering['status'] = 'Status';
$catering['total'] = 'Gesamt';
$catering['total_today'] = 'Gesamt heute';
$catering['currency'] = '&#8364;';
$catering['error_occurred'] = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
$catering['total_sum'] = 'Gesamtsumme';
$catering['error'] = 'Fehler';
$catering['quantity_times'] = 'x';
$catering['total_summary'] = 'Gesamt heute';
$catering['orders'] = 'Bestellungen';

// Validierungsmeldungen
$catering['validation'] = array(
    'name_required' => 'Bitte einen Namen angeben',
    'flyer_number_invalid' => 'Bitte eine gültige Flyer-Nummer angeben',
    'quantity_invalid' => 'Bitte eine gültige Anzahl angeben',
    'size_invalid' => 'Bitte eine gültige Größe auswählen',
    'price_invalid' => 'Bitte einen gültigen Preis angeben'
);

// Statusmeldungen
$catering['messages'] = array(
    'order_success' => 'Bestellung erfolgreich aufgegeben',
    'save_error' => 'Fehler beim Speichern der Bestellung',
    'database_error' => 'Datenbankfehler',
    'general_error' => 'Ein Fehler ist aufgetreten',
    'validation_error' => 'Validierungsfehler',
    'invalid_request' => 'Ungültige Anfrage-Methode',
    'post_only' => 'Nur POST-Requests sind erlaubt'
);

// Admin-Panel Übersetzungen
$catering['admin'] = array(
    'title' => 'Bestellungen Verwalten',
    'stats' => array(
        'total_orders' => 'Gesamtbestellungen',
        'total_revenue' => 'Gesamtumsatz',
        'paid_revenue' => 'Bezahlter Umsatz',
        'unpaid_revenue' => 'Offene Zahlungen'
    ),
    'add_order' => 'Neue Bestellung manuell hinzufügen',
    'current_orders' => 'Aktuelle Bestellungen',
    'confirm_delete' => 'Wirklich ALLE Einträge löschen?',
    'confirm_delete_checkbox' => 'Ich bestätige, dass ich alle Einträge löschen möchte',
    'delete_all' => 'Alle Einträge löschen',
    'actions' => 'Aktionen',
    'delete' => 'Löschen',
    'paid_status' => 'Bezahlt',
    'add_button' => 'Bestellung hinzufügen'
);
$catering['admin']['edit_order'] = 'Bestellung bearbeiten';
$catering['admin']['save_changes'] = 'Änderungen speichern';
$catering['admin']['cancel'] = 'Abbrechen';
$catering['admin']['unpaid_status'] = 'Nicht bezahlt';
$catering['admin']['edit'] = 'Bearbeiten';
$catering['admin']['confirm_delete_single'] = 'Bestellung wirklich löschen?';
$catering['flyer'] = array(
    'management' => 'Flyer-Management',
    'toggle' => 'Flyer-Verwaltung anzeigen',
    'upload' => array(
        'title' => 'Flyer hochladen',
        'name' => 'Flyer-Name',
        'file' => 'PDF-Datei',
        'button' => 'Hochladen',
        'error' => 'Nur PDF-Dateien sind erlaubt'
    ),
    'list' => array(
        'name' => 'Name',
        'filename' => 'Dateiname',
        'upload_date' => 'Upload-Datum',
        'status' => 'Status',
        'actions' => 'Aktionen'
    ),
    'status' => array(
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv'
    ),
    'actions' => array(
        'view' => 'Ansehen',
        'delete' => 'Löschen',
        'close' => 'Schließen'
    ),
    'messages' => array(
		'update_success' => 'Status erfolgreich aktualisiert',
		'delete_confirm' => 'Flyer wirklich löschen?',
		'delete_success' => 'Flyer erfolgreich gelöscht',
		'upload_success' => 'Flyer erfolgreich hochgeladen',
		'default_error' => 'Ein Fehler ist aufgetreten',
		'table_error' => 'Fehler beim Aktualisieren der Tabelle'
    )
);

?>

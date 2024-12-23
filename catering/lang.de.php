<?php
// Allgemeine Übersetzungen
$catering = [
    'title' => 'Catering',
    'time' => 'Zeit',
    'name' => 'Name',
    'status' => 'Status',
    'error' => 'Fehler',
    'currency' => '&#8364;',
    
    // Bestellungsbezogene Übersetzungen
    'new_order' => 'Neue Bestellung aufgeben',
    'current_orders' => 'Aktuelle Bestellungen heute',
    'flyer_number' => 'Nummer auf Flyer',
    'quantity' => 'Anzahl',
    'quantity_times' => 'x',
    'comments' => 'Bemerkungen',
    'price' => 'Einzelpreis inkl. Lieferung',
    'order_button' => 'Bestellen',
    'orders' => 'Bestellungen',
    
    // Größen
    'size' => 'Größe',
    'size_normal' => 'Normal',
    'size_small' => 'Klein',
    'size_big' => 'Groß',
    
    // Status
    'status_legend' => 'Status-Legende',
    'status_new' => 'Neu',
    'status_processing' => 'In Bearbeitung',
    'status_completed' => 'Fertig',
    
    // Summen und Zusammenfassungen
    'total' => 'Gesamt',
    'total_today' => 'Gesamt heute',
    'total_sum' => 'Gesamtsumme',
    'total_summary' => 'Gesamt heute',
    'orders_summary' => 'Zusammenfassung',
    'orders_count' => 'Bestellungen:',
    
    // Links und Buttons
    'view_flyer' => 'Aktuellen Flyer "%s" anzeigen',
    'admin_panel' => 'Zur Verwaltung',
    
    // Fehlermeldungen
    'error_occurred' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
];

// Validierungsmeldungen
$catering['validation'] = [
    'name_required' => 'Bitte einen Namen angeben',
    'flyer_number_invalid' => 'Bitte eine gültige Flyer-Nummer angeben',
    'quantity_invalid' => 'Bitte eine gültige Anzahl angeben',
    'size_invalid' => 'Bitte eine gültige Größe auswählen',
    'price_invalid' => 'Bitte einen gültigen Preis angeben'
];

// Statusmeldungen
$catering['messages'] = [
    'order_success' => 'Bestellung erfolgreich aufgegeben',
    'save_error' => 'Fehler beim Speichern der Bestellung',
    'database_error' => 'Datenbankfehler',
    'general_error' => 'Ein Fehler ist aufgetreten',
    'validation_error' => 'Validierungsfehler',
    'invalid_request' => 'Ungültige Anfrage-Methode',
    'post_only' => 'Nur POST-Requests sind erlaubt'
];

// Admin-Panel Übersetzungen
$catering['admin'] = [
    'title' => 'Bestellungen Verwalten',
    'stats' => [
        'total_orders' => 'Gesamtbestellungen',
        'total_revenue' => 'Gesamtumsatz',
        'paid_revenue' => 'Bezahlter Umsatz',
        'unpaid_revenue' => 'Offene Zahlungen'
    ],
    'add_order' => 'Neue Bestellung manuell hinzufügen',
    'current_orders' => 'Aktuelle Bestellungen',
    'confirm_delete' => 'Wirklich ALLE Einträge löschen?',
    'confirm_delete_checkbox' => 'Ich bestätige, dass ich alle Einträge löschen möchte',
    'confirm_delete_single' => 'Bestellung wirklich löschen?',
    'delete_all' => 'Alle Einträge löschen',
    'actions' => 'Aktionen',
    'delete' => 'Löschen',
    'edit' => 'Bearbeiten',
    'edit_order' => 'Bestellung bearbeiten',
    'save_changes' => 'Änderungen speichern',
    'cancel' => 'Abbrechen',
    'paid_status' => 'Bezahlt',
    'unpaid_status' => 'Nicht bezahlt',
    'add_button' => 'Bestellung hinzufügen'
];

// Flyer-Management Übersetzungen
$catering['flyer'] = [
    'management' => 'Flyer-Management',
    'toggle' => 'Flyer-Verwaltung anzeigen',
    'upload' => [
        'title' => 'Flyer hochladen',
        'name' => 'Flyer-Name',
        'file' => 'PDF-Datei',
        'button' => 'Hochladen',
        'error' => 'Nur PDF-Dateien sind erlaubt'
    ],
    'list' => [
        'name' => 'Name',
        'filename' => 'Dateiname',
        'upload_date' => 'Upload-Datum',
        'status' => 'Status',
        'actions' => 'Aktionen'
    ],
    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv'
    ],
    'actions' => [
        'view' => 'Ansehen',
        'delete' => 'Löschen',
        'close' => 'Schließen'
    ],
    'messages' => [
        'update_success' => 'Status erfolgreich aktualisiert',
        'delete_confirm' => 'Flyer wirklich löschen?',
        'delete_success' => 'Flyer erfolgreich gelöscht',
        'upload_success' => 'Flyer erfolgreich hochgeladen',
        'default_error' => 'Ein Fehler ist aufgetreten',
        'table_error' => 'Fehler beim Aktualisieren der Tabelle'
    ]
];
?>

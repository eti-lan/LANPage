<?php
require_once 'config.php';
header('Content-Type: application/json');

$db = Database::getInstance();
$flyers = $db->query('SELECT * FROM flyers ORDER BY upload_date DESC');

$html = '';
while ($flyer = $flyers->fetchArray(SQLITE3_ASSOC)) {
    $html .= '<tr>
        <td>'.htmlspecialchars($flyer['name']).'</td>
        <td>'.htmlspecialchars($flyer['filename']).'</td>
        <td>'.date('d.m.Y H:i', strtotime($flyer['upload_date'])).'</td>
        <td>
            <button type="button" 
                    onclick="updateFlyerStatus('.$flyer['id'].')"
                    class="status-button '.($flyer['is_active'] ? 'active' : '').'">
                '.($flyer['is_active'] ? 'Aktiv' : 'Inaktiv').'
            </button>
        </td>
        <td>
            <a href="flyer/'.htmlspecialchars($flyer['filename']).'" 
               target="_blank" class="view-button">Ansehen</a>
            <button type="button" 
                    onclick="deleteFlyer('.$flyer['id'].')"
                    class="delete-button">Löschen</button>
        </td>
    </tr>';
}

echo json_encode(['html' => $html]);
?>

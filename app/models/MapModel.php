<?php
require_once __DIR__ . '/BaseModel.php';

class MapModel extends BaseModel {

    public function getAllWithCoords() {
        return $this->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets 
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            WHERE f.latitude IS NOT NULL AND f.longitude IS NOT NULL
            GROUP BY f.id ORDER BY f.created_at DESC
        ")->fetchAll();
    }

    public function updateCoords($id, $lat, $lng) {
        $this->query("UPDATE fiche_entreprise SET latitude = ?, longitude = ? WHERE id = ?", [$lat, $lng, $id]);
    }

    public function geocode($ville) {
        $villes = [
            'tunis'=>[36.8065,10.1815],'sfax'=>[34.7406,10.7603],
            'sousse'=>[35.8256,10.6084],'monastir'=>[35.7643,10.8113],
            'bizerte'=>[37.2744,9.8739],'gabes'=>[33.8815,10.0982],
            'kairouan'=>[35.6781,10.0963],'gafsa'=>[34.4250,8.7842],
            'nabeul'=>[36.4561,10.7376],'ariana'=>[36.8625,10.1956],
            'ben arous'=>[36.7533,10.2283],'manouba'=>[36.8101,10.0956],
            'medenine'=>[33.3399,10.5055],'tozeur'=>[33.9197,8.1336],
            'hammamet'=>[36.4000,10.6167],'djerba'=>[33.8076,10.8451]
        ];
        $v = strtolower(trim($ville));
        return $villes[$v] ?? [36.8065, 10.1815];
    }
}

<?php
require_once __DIR__ . '/BaseModel.php';

class CO2Model extends BaseModel {

    private $coefficients = [
        'transport' => 0.192,   // kg CO2 par km (voiture moyenne)
        'energie'   => 0.475,   // kg CO2 par kWh (Tunisie mix)
        'dechets'   => 2.5,     // kg CO2 par kg de déchets non triés
        'eau'       => 0.344    // kg CO2 par m³ d'eau
    ];

    public function calculate($data) {
        $co2 = [
            'transport' => ($data['transport_km'] ?? 0) * $this->coefficients['transport'],
            'energie'   => ($data['energie_kwh'] ?? 0) * $this->coefficients['energie'],
            'dechets'   => ($data['dechets_kg'] ?? 0) * $this->coefficients['dechets'],
            'eau'       => ($data['eau_m3'] ?? 0) * $this->coefficients['eau']
        ];

        $total = array_sum($co2);

        // Sauvegarde
        $this->query("
            INSERT INTO co2_calculation 
            (id_user, id_fiche_entreprise, transport_km, energie_kwh, dechets_kg, eau_m3, total_co2_kg)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            1, // id_user temporaire
            $data['id_fiche_entreprise'] ?? null,
            $data['transport_km'] ?? 0,
            $data['energie_kwh'] ?? 0,
            $data['dechets_kg'] ?? 0,
            $data['eau_m3'] ?? 0,
            $total
        ]);

        return [
            'details' => $co2,
            'total'   => round($total, 2),
            'equivalence' => $this->getEquivalence($total)
        ];
    }

    private function getEquivalence($kg) {
        if ($kg > 10000) return "🌳 " . round($kg/250) . " arbres à planter par an";
        if ($kg > 1000)  return "🚗 " . round($kg/192) . " trajets Tunis-Paris en voiture";
        return "💡 Équivalent de " . round($kg/0.475) . " kWh d'électricité";
    }

    public function getHistory($id_user = 1) {
        return $this->query("
            SELECT c.*, f.nom as entreprise_nom 
            FROM co2_calculation c 
            LEFT JOIN fiche_entreprise f ON c.id_fiche_entreprise = f.id
            WHERE c.id_user = ?
            ORDER BY c.created_at DESC LIMIT 10
        ", [$id_user])->fetchAll();
    }
}
<?php
// controleur/gestionRespiration.php

class GestionRespiration {
    public function getExercices() {
        return [
            ['nom' => '748 Pattern', 'description' => 'Avancé avec apnée', 'inhale' => 7, 'hold' => 4, 'exhale' => 8],
            ['nom' => '55 Pattern',  'description' => 'Équilibré sans apnée', 'inhale' => 5, 'hold' => 0, 'exhale' => 5],
            ['nom' => '46 Pattern',  'description' => 'Apaisant avec expiration longue', 'inhale' => 4, 'hold' => 0, 'exhale' => 6],
        ];
    }
}
?>

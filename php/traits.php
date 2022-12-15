<?php
include 'php/dice.php';
class Traits {
    private $height;
    private $weight;
    private $age;
    private $eyes;
    private $skin;
    private $hair;

    private $conn;

    function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Trasforma un array JSON in un array PHP
     * @param  {String} Array JSON
     * @return {Object} Array PHP contenente i dati dell' array passato come JSON
     */
    function JsonToPhpArr($js) {
        $str = substr($js, 11, -2);
        $arr = explode(',', $str);
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = substr($arr[$i], 2, -1);
        }
        return $arr;
    }

    /**
     * Genera i tratti avanzati del personaggio in base alla razza: altezza, peso, età, colore occhi/pelle/capelli
     * @param  {Object} conn connessione al database
     * @param  {Number} raceID ID della razza all'interno del database
     */
    function Generate($raceID) {
        $myQuery = "SELECT races.ID, traits.BaseHeight, traits.HeightModifier, traits.BaseWeight, traits.WeightModifier, traits.MinAge, traits.MaxAge, traits.eyes_colors, traits.skin_colors, traits.hair_colors FROM races INNER JOIN traits ON races.traits=traits.ID WHERE races.ID = " . $raceID;
        $that = $this->conn->query($myQuery);
        $race = $that->fetch_assoc();

        $this->age = rand($race["MinAge"], $race["MaxAge"]);

        $colors = $this->JsonToPhpArr($race["eyes_colors"]);
        $this->eyes = $colors[rand(0, count($colors) - 1)];

        $colors = $this->JsonToPhpArr($race["skin_colors"]);
        $this->skin = $colors[rand(0, count($colors) - 1)];

        $colors = $this->JsonToPhpArr($race["hair_colors"]);
        if ($colors == null) {
            $this->hair = "none";
        } else {
            $this->hair = $colors[rand(0, count($colors) - 1)];
        }

        $dice = new dice();
        $this->height = $race["BaseHeight"] + $dice->roll($race["HeightModifier"]);
        $this->weight = $race["BaseWeight"] * $dice->roll($race["WeightModifier"]);

        //echo ("Traits-> [height:" . $this->height . "] [weight:" . $this->weight . "] [age: " . $this->age . "] [eyes:" . $this->eyes . "] [skin:" . $this->skin . "] [hair:" . $this->hair . "]<br>");
        
        
    }

    function SetHeight(int min, int max){
        $this->height = rand(min, max);
    }

    function SetWeight(int min, int max){
        $this->weight = rand(min, max);
    }

    function SetHairColor(String color){
        $this->hair = color;
    }

    function SetEyesColor(String color){
        $this->eyes = color;
    }

    function SetSkinColor(String color){
        $this->skin = color;
    }

    /**
     * Restituisce tutti i tratti sotto forma di array
     */
    public function GetTraits() {
        return [
            "height" => $this->height,
            "weight" => $this->weight,
            "age" => $this->age,
            "eyes" => $this->eyes,
            "skin" => $this->skin,
            "hair" => $this->hair
        ];
    }
}

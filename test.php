<?php

class Facture{
    public function montantTTC($ht) {
    return $ht*(1+20/100);
    }
}

$facture1 = new Facture();
echo $facture1
?>
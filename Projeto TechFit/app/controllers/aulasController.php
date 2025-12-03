<?php 

    function aulasController()
    {
        $modalidadeSelecionada = $_GET["modalidade"] ?? 'todas';
        $aulas = Aulas::getAulas($modalidadeSelecionada);
        $data = [
            "modalidadeSelecionada" => $modalidadeSelecionada,
            "titulo" => "Aulas",
            "aulas" => $aulas,
            "modalidades" => Modalidades::getModalidades(),
        ];
        render("aulasView", $data["titulo"], $data);
    
    }
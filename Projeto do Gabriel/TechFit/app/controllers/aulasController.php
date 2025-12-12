<?php 

    function aulasController()
    {
        $uri = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');
        $modalidadeSelecionada = $_GET["modalidade"] ?? 'todas';
        if($_SERVER['REQUEST_METHOD'] === "POST" && ($uri === '/aulas/agendar' || $uri === '/aulas')){
            agendarAula($_POST['id_aula']);
            return;
        }

        Aulas::fecharAulasPassadas();
        $aulas = Aulas::getAulas($modalidadeSelecionada);

        // descobre o id_aluno do usuário logado (se for aluno)
        $id_aluno = null;
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'aluno') {
            $aluno = Aluno::getAlunoByUserID($_SESSION['user_id']);
            $id_aluno = $aluno['id_aluno'] ?? null;
        }

        $data = [
            "modalidadeSelecionada" => $modalidadeSelecionada,
            "titulo" => "Aulas",
            "aulas" => $aulas,
            "modalidades" => Modalidades::getModalidades(),
            "id_aluno" => $id_aluno,
        ];
        render("aulasView", $data["titulo"], $data);
    
    }

    function agendarAula($id){
        try {
            $sucesso = Aulas::agendarAula((int)$id, (int)$_SESSION["user_id"]);

            if ($sucesso) {
                flash('Aula agendada com sucesso!', 'success');
                header('Location: /profile?page=agenda');
            } else {
                flash('Não foi possível agendar esta aula. Verifique se ela já está agendada ou se não há mais vagas.', 'error');
                header('Location: /aulas');
            }
            exit;
        } catch (Exception $e) {
            flash('Erro ao agendar aula. Tente novamente mais tarde.', 'error');
            header('Location: /aulas');
            exit;
        }
    }

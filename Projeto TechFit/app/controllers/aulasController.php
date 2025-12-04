<?php 

    function aulasController()
    {
    // Se é POST, tratar tentativa de agendamento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_aula'])) {
            // validar sessão
            if (empty($_SESSION['user_id'])) {
                flash('Você precisa estar logado para agendar uma aula.', 'error');
                header('Location: /login');
                exit;
            }

            $id_aula = (int)$_POST['id_aula'];
            // obter id_aluno via Aluno model
            $al = Aluno::getAlunoByUserID($_SESSION['user_id']);
            if (!$al) {
                flash('Complete seu cadastro antes de agendar aulas.', 'error');
                header('Location: /profile?page=concluir');
                exit;
            }
            $id_aluno = (int)$al['id_aluno'];

            // checar se já está agendado
            if (Aulas::checkAgendado($id_aluno, $id_aula)) {
                flash('Você já está inscrito nesta aula.', 'error');
                header('Location: /aulas');
                exit;
            }

            // checar lotação
            $inscritos = Aulas::getInscritos($id_aula);
            // obter capacidade da aula
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('SELECT quantidade_pessoas FROM Aulas WHERE id_aula = :id');
            $stmt->execute([':id' => $id_aula]);
            $capacidade = (int)$stmt->fetchColumn();
            if ($inscritos >= $capacidade) {
                // permitir entrar na lista de espera
                // Inserir com status 'espera'
                $now = date('Y-m-d H:i:s');
                $ins = $pdo->prepare('INSERT INTO Agendamento (data_agendamento, status, id_aula, id_aluno) VALUES (:data, :status, :id_aula, :id_aluno)');
                $ins->execute([':data' => $now, ':status' => 'espera', ':id_aula' => $id_aula, ':id_aluno' => $id_aluno]);
                flash('A aula está lotada — você foi colocado na lista de espera.', 'success');
                header('Location: /profile?page=agenda');
                exit;
            }

            // inserir agendamento com status agendado
            $now = date('Y-m-d H:i:s');
            $ins = $pdo->prepare('INSERT INTO Agendamento (data_agendamento, status, id_aula, id_aluno) VALUES (:data, :status, :id_aula, :id_aluno)');
            $ins->execute([':data' => $now, ':status' => 'agendado', ':id_aula' => $id_aula, ':id_aluno' => $id_aluno]);
            flash('Aula agendada com sucesso!', 'success');
            header('Location: /profile?page=agenda');
            exit;
        }

        $modalidadeSelecionada = $_GET["modalidade"] ?? 'todas';
        $aulas = Aulas::getAulas($modalidadeSelecionada);

        // se usuário logado, buscar seus agendamentos para sinalizar na view
        $booked = [];
        $waitlist = [];
        if (!empty($_SESSION['user_id'])) {
            $al = Aluno::getAlunoByUserID($_SESSION['user_id']);
            if ($al) {
                $id_aluno = (int)$al['id_aluno'];
                // buscar agendamentos do aluno
                $pdo = Connect::conectar();
                $s = $pdo->prepare('SELECT id_aula, status FROM Agendamento WHERE id_aluno = :id_aluno');
                $s->execute([':id_aluno' => $id_aluno]);
                $rows = $s->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    if ($r['status'] === 'agendado') $booked[(int)$r['id_aula']] = true;
                    if ($r['status'] === 'espera') $waitlist[(int)$r['id_aula']] = true;
                }
            }
        }

        $data = [
            "modalidadeSelecionada" => $modalidadeSelecionada,
            "titulo" => "Aulas",
            "aulas" => $aulas,
            "modalidades" => Modalidades::getModalidades(),
            'booked' => $booked,
            'waitlist' => $waitlist,
        ];
        render("aulasView", $data["titulo"], $data);
    
    }
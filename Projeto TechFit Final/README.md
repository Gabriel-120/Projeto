Projeto TechFit Final

Este diretório contém a versão mesclada do projeto TechFit.

Como usar o script de mesclagem

1) Confirme que você tem PHP CLI instalado (Windows: `php -v`).
2) Execute o script a partir da raiz do workspace (onde estão as pastas "Projeto do Gabriel" e "Projeto TechFit").

Exemplo de comando (PowerShell):

php .\Projeto\Projeto TechFit Final\scripts\merge_sources.php "Projeto do Gabriel/TechFit" "Projeto TechFit" "Projeto TechFit Final"

O script fará:
- copiar recursivamente todos os arquivos das duas fontes para `Projeto TechFit Final`.
- quando encontrar arquivos com o mesmo caminho relativo nas duas fontes, escolhe a versão com mais linhas.

Depois de rodar o script:
- Ajuste `app/config/.env` com suas credenciais locais (use `app/config/.env.example` como template).
- Importe o banco de dados (arquivos SQL disponíveis nas pastas `db/` das origens).
- Verifique `public/index.php` como front controller mínimo.

Observações:
- O script não resolve conflitos semânticos (funcionalidade diferente entre versões com mesmo tamanho); revise manualmente arquivos importantes como `adminController.php`.
- Faça um backup antes de rodá-lo se tiver alterações locais importantes.

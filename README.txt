HELPDESK FACIL - PHP + MySQL

REQUISITOS
- XAMPP com Apache e MySQL/MariaDB
- Navegador
- VS Code (opcional)

INSTALACAO
1. Extraia a pasta helpdesk_facil para C:\xampp\htdocs\
2. Inicie Apache e MySQL no XAMPP.
3. Acesse http://localhost/phpmyadmin
4. Importe o arquivo database.sql. Ele cria/recria as tres tabelas: usuarios, categorias e chamados.
   IMPORTANTE: o SQL apaga os registros antigos dessas tres tabelas para corrigir estruturas de versões anteriores. Faça backup antes se precisar manter dados.
5. Acesse http://localhost/helpdesk_facil/

BANCO DE DADOS
- usuarios: armazena nome, email e telefone dos solicitantes.
- categorias: armazena as categorias dos chamados.
- chamados: armazena as solicitacoes, prioridade e status ENUM, com chaves estrangeiras para usuarios e categorias.
O SQL inclui registros iniciais de exemplo para testar o sistema.

CONEXAO
A configuracao padrao em config/conexao.php usa host localhost, banco helpdesk_facil, usuario root e senha vazia. Altere se necessario.

FUNCIONALIDADES
- Painel com totais por status e chamados recentes.
- Criar chamado digitando o nome do solicitante (se o nome ainda nao existir, ele e cadastrado automaticamente; nomes ja cadastrados aparecem como sugestao) e escolhendo a categoria.
- Listar e pesquisar chamados por titulo, solicitante ou categoria.
- Editar chamado, prioridade e status.
- Excluir chamado com confirmacao.

CSS SEPARADO
- css/base.css: estilos compartilhados.
- css/index.css: painel.
- css/chamados.css: listagem.
- css/novo.css: cadastro.
- css/editar.css: edicao.

Projeto didatico para uso local. Consultas realizadas com PDO e prepared statements.

HELPDESK FACIL - PHP + MySQL

REQUISITOS
- XAMPP com Apache e MySQL
- Navegador
- VS Code (opcional)

INSTALACAO
1. Extraia a pasta helpdesk_facil para C:\xampp\htdocs\
2. Inicie Apache e MySQL no XAMPP.
3. Acesse http://localhost/phpmyadmin
4. Importe o arquivo database.sql. Ele cria/recria as tres tabelas: usuarios, categorias e chamados.
   IMPORTANTE: o SQL apaga os registros antigos dessas tres tabelas para corrigir estruturas de versões anteriores. Faça backup antes se precisar manter dados.
   (Se você pular este passo, o sistema cria o banco sozinho na primeira vez que abrir a página.)
5. Acesse http://localhost/helpdesk_facil/ e entre com um dos usuarios de teste.

CADASTRO
- Na tela de login, o link "Criar conta" abre o cadastro (nome, e-mail, telefone opcional e senha de no minimo 8 caracteres).
- Toda conta criada ali e do tipo Usuario e ja entra no sistema ao terminar. Administrador so existe pelo banco (carlos@email.com).

USUARIOS DE TESTE (senha de todos: 12345678)
- carlos@email.com  -> Administrador (pode alterar o status dos chamados)
- ana@email.com     -> Usuario
- pedro@email.com   -> Usuario

BANCO DE DADOS
- usuarios: armazena nome, email e telefone dos solicitantes.
- categorias: armazena as categorias dos chamados.
- chamados: armazena as solicitacoes, prioridade e status ENUM, com chaves estrangeiras para usuarios e categorias.
O SQL inclui registros iniciais de exemplo para testar o sistema.

CONEXAO
A configuração padrão em config/conexao.php usa host localhost, banco helpdesk_facil, usuario root e senha vazia. Altere se necessario.

FUNCIONALIDADES
- Painel com totais por status e chamados recentes.
- Criar chamado digitando o nome do solicitante (se o nome ainda nao existir, ele e cadastrado automaticamente; nomes ja cadastrados aparecem como sugestao) e escolhendo a categoria.
- Listar e pesquisar chamados por titulo, solicitante ou categoria.
- Permissoes: o Administrador edita e exclui qualquer chamado; os demais usuarios so editam e excluem os chamados que eles mesmos abriram (coluna id_criador). Nas listas, chamados de outras pessoas aparecem sem os botoes de acao.
- Solicitante: usuario comum sempre abre chamado no proprio nome (campo travado); somente o Administrador digita ou troca o solicitante, em novo e em editar.
- Editar chamado e prioridade.
- Administrador: altera o status direto nas listas (Painel e Chamados), escolhendo a opcao no proprio selo de status, sem abrir o chamado.
- Excluir chamado com confirmacao.

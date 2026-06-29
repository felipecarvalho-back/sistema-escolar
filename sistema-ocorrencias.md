# Plano de Implementação: Sistema de Ocorrências Escolares

Este documento descreve o plano detalhado para criar o sistema de ocorrências escolares, aproveitando os recursos nativos do framework Forge MVC (como autenticação, ORM, migrations, filas e disparo de e-mails).

## Open Questions

Antes de iniciar a escrita de código, precisamos alinhar algumas decisões de negócio:

> [!IMPORTANT]
> 1. **Contatos dos Responsáveis:** Um aluno pode ter múltiplos responsáveis (ex: mãe e pai) cadastrados com e-mails diferentes? Se sim, o e-mail de convocação na 3ª ocorrência deve ser enviado para todos eles ou para um contato principal?
> 2. **Fluxo de Contagem de Ocorrências:** A regra de enviar a convocação ao atingir 3 ocorrências deve considerar apenas ocorrências com status **"Aprovada"**? Ocorrências pendentes de aprovação pelo coordenador contam para o limite?
> 3. **Interface Visual:** Faremos a renderização das telas usando o motor de Views PHP interno combinando com **HTMX** para interações dinâmicas (sem reload) ou prefere focar em carregamento de páginas tradicionais com formulários simples?
> 4. **Zerar Contagem:** A contagem de ocorrências para convocação dos pais é zerada ao fim de cada ano letivo/semestre ou é cumulativa vitalícia?

---

## Arquitetura do Banco de Dados (Proposta)

Para atender a todos os fluxos e regras, propomos o seguinte esquema de tabelas nas Migrations:

### 1. `usuarios`
Gerencia todos os usuários que acessam o sistema.
- `id` (PK)
- `nome`
- `email` (Unique)
- `senha`
- `perfil` (enum: 'responsavel', 'professor', 'secretaria')
- `timestamps`

### 2. `alunos`
Cadastro dos estudantes.
- `id` (PK)
- `nome`
- `data_nascimento`
- `timestamps`

### 3. `responsaveis_alunos`
Tabela pivô de relacionamento N:N entre responsáveis (usuários) e alunos.
- `responsavel_id` (FK -> usuarios.id)
- `aluno_id` (FK -> alunos.id)
- `parentesco` (ex: Pai, Mãe, Tio, etc.)

### 4. `turmas`
Salas de aula (ex: 1º Ano A, 2º Ano B).
- `id` (PK)
- `nome`
- `professor_coordenador_id` (FK -> usuarios.id - Professor coordenador desta turma)
- `timestamps`

### 5. `alunos_turmas`
Relacionamento N:N ou 1:N entre alunos e turmas em um determinado período.
- `aluno_id` (FK -> alunos.id)
- `turma_id` (FK -> turmas.id)
- `ano_letivo`

### 6. `ocorrencias`
Registros das ocorrências dos alunos.
- `id` (PK)
- `aluno_id` (FK -> alunos.id)
- `turma_id` (FK -> turmas.id)
- `autor_id` (FK -> usuarios.id - Quem cadastrou)
- `descricao` (Texto detalhado da ocorrência)
- `status` (enum: 'pendente', 'aprovada', 'rejeitada')
- `aprovado_por_id` (FK -> usuarios.id, nulo se cadastrado por coordenador/secretaria diretamente)
- `created_at`
- `updated_at`

---

## Etapas de Desenvolvimento Propostas

### Fase 1: Setup e Banco de Dados (Migrations e Models)
1. Criar as tabelas utilizando as migrations do Forge (`php forge make:migration`).
2. Configurar os Models correspondentes estendendo `Core\Database\Model` e definindo os relacionamentos:
   - `Aluno` -> `belongsToMany` Responsável.
   - `Ocorrencia` -> `belongsTo` Aluno, Turma, Autor.
   - `Turma` -> `belongsTo` ProfessorCoordenador.

### Fase 2: Autenticação e Perfis (Middleware)
1. Rodar `php forge setup:auth` para gerar a base de autenticação do projeto.
2. Adaptar o fluxo de login para redirecionar cada perfil ao seu respectivo painel (dashboard).
3. Criar o middleware `VerificarPerfilMiddleware` para restringir rotas da secretaria, professores comuns e responsáveis.

### Fase 3: Lógica de Ocorrências e Fluxo de Aprovação
1. **Cadastro por Professores:** Professores comuns podem selecionar alunos da escola, relatar o fato e salvar. Caso o professor seja o coordenador da sala daquele aluno, a ocorrência entra automaticamente como `aprovada`. Caso contrário, entra como `pendente`.
2. **Aprovação pelo Coordenador/Secretaria:** Painel para o professor coordenador ver ocorrências pendentes de sua respectiva turma e aprová-las ou rejeitá-las. A secretaria também pode aprovar ocorrências de qualquer aluno diretamente.

### Fase 4: Notificação em Fila e Envio de E-mail (Job)
1. Criar um Job de fila chamado `EnviarEmailConvocacaoJob` (`php forge make:job` ou similar).
2. Na regra de aprovação da ocorrência:
   - Contar a quantidade de ocorrências ativas com status `aprovada` para o respectivo aluno.
   - Se for igual a 3, despachar o Job de envio de e-mail para a fila.
   - O Job formatará um e-mail informando sobre a convocação escolar e enviará para os responsáveis vinculados ao aluno através do `MailManager` (PHPMailer).

### Fase 5: Desenvolvimento dos Painéis Visuais (Views + HTMX)
1. **Painel do Responsável:** Visualização simples com a ficha do aluno, telefones salvos e histórico de ocorrências com status `aprovada`.
2. **Painel do Professor:** Formulário rápido de criação de ocorrência, listagem das ocorrências que ele abriu e área de aprovação (caso seja coordenador).
3. **Painel da Secretaria:** Busca global de alunos, visualização detalhada de contatos dos pais e histórico completo de ocorrências.

---

## Plano de Verificação e Testes

### Verificação Automatizada
- Executar testes unitários e de integração locais para validar o fluxo do contador de ocorrências e regras de aprovação.
- Simular processamento da fila de envio utilizando `php forge queue:work` localmente.

### Verificação Manual
1. Entrar como Professor A e registrar uma ocorrência para o Aluno X (cuja turma é coordenada pelo Professor B). Validar que a ocorrência está com status `pendente`.
2. Entrar como Professor B (Coordenador), aprovar a ocorrência. Validar mudança de status.
3. Repetir o processo até atingir a 3ª ocorrência aprovada para o Aluno X.
4. Validar se o registro de e-mail foi inserido na fila e se o e-mail de convocação aos pais foi gerado e enviado com sucesso.

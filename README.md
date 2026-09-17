# Barbearia Prime — Site + Agendamento + Painel Administrativo

Site institucional com agendamento online e painel administrativo seguro,
em **PHP + MySQL puro** (sem frameworks), com verificação automática de
conflito de horário e um assistente de dúvidas (chat) no site.

---

## O que mudou em relação à versão anterior

1. **Acesso administrativo protegido** — antes, `admin.html` abria direto,
   sem nenhuma senha. Agora existe login de verdade (`admin/login.php`) com:
   - Senha com hash bcrypt (nunca fica em texto puro)
   - Bloqueio automático da conta após 5 tentativas erradas (15 min)
   - Proteção contra CSRF em todos os formulários administrativos
   - Sessão seca configurada com cookie `HttpOnly`, `SameSite` e expiração
     por inatividade (2h)
2. **Conflito de horário resolvido no servidor** — antes o agendamento era
   salvo só no navegador do próprio cliente (`localStorage`), então dois
   clientes diferentes podiam "ver" o mesmo horário como livre. Agora tudo
   fica num banco de dados único: quando alguém tenta marcar um horário que
   já está ocupado para aquele barbeiro, o sistema avisa **"horário
   indisponível"** antes mesmo de enviar (checagem em tempo real) e recusa
   no servidor caso dois clientes cliquem "confirmar" ao mesmo tempo.
3. **Emojis removidos** — substituídos por ícones em SVG (WhatsApp, chat,
   check de sucesso) ou por elementos tipográficos simples, mantendo a
   identidade visual sem depender de fontes de emoji do sistema operacional.
4. **Layout responsivo** — menu mobile (hambúrguer), grids que se ajustam
   em telas pequenas, tabelas com rolagem horizontal no painel admin, e
   botões/formulários adaptados para toque.
5. **WhatsApp direto** — botão flutuante em todas as páginas e link na seção
   de contato, abrindo `https://wa.me/5571997063936` já com a mensagem
   "Olá, barbearia prime. Estou com dúvidas" preenchida.
6. **Assistente de dúvidas (chat)** — um widget de chat no canto da tela
   responde perguntas sobre pacotes mensais, formas de pagamento e deixa
   claro que **não é possível atender sem agendamento**. Veja a seção
   "Sobre o assistente de dúvidas" abaixo para entender como funciona e
   como evoluir para uma IA de verdade.

---

## 1. Requisitos

- PHP 8.0+ com extensões `pdo_mysql`
- MySQL 5.7+ / MariaDB 10.3+ (a checagem de disponibilidade usa `GET_LOCK`,
  disponível em ambos)
- Servidor Apache (para o `.htaccess`) ou Nginx com regra equivalente

## 2. Instalação (sem Docker — XAMPP/Laragon/hospedagem tradicional)

1. Suba os arquivos para o servidor (ou rode localmente com
   XAMPP/Laragon/MAMP).
2. Importe o banco: `mysql -u root -p < sql/schema.sql`
3. Configure a conexão em `config/database.php` (ou defina as variáveis de
   ambiente `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` — o
   arquivo já lê do ambiente automaticamente, com esses valores como
   padrão de desenvolvimento local).
4. Defina a variável de ambiente `ALLOW_INSTALL=1` (ou, se não usar
   variáveis de ambiente, comente temporariamente o bloqueio no topo de
   `install.php`), acesse **`http://seu-endereco/install.php`** e crie a
   conta do administrador com uma senha forte (mín. 8 caracteres,
   maiúscula, minúscula e número).
5. **Desative `ALLOW_INSTALL`** (volte para `0` ou remova a variável)
   depois de criar a conta.
6. Pronto:
   - Site público: `index.html`
   - Agendamento: `cliente.php`
   - Painel administrativo: `admin/login.php`

## 3. Rodando com Docker localmente

O projeto já inclui `Dockerfile` e `docker-compose.yml` (site + MySQL
prontos para teste local):

```bash
docker compose up --build
```

Acesse `http://localhost:8080`. O `docker-compose.yml` já sobe um MySQL,
importa `sql/schema.sql` automaticamente na primeira vez e deixa
`ALLOW_INSTALL=1` ligado — então é só ir direto em
`http://localhost:8080/install.php` e criar sua conta de administrador.

## 4. Deploy no Render

O Render funciona muito bem para o **site** (via Docker), mas **não
oferece banco de dados MySQL gerenciado** — só PostgreSQL e Redis. Como
este projeto usa MySQL (inclusive a trava `GET_LOCK` para evitar
agendamentos duplicados), você vai precisar de um MySQL externo. Opções
com plano gratuito: [PlanetScale](https://planetscale.com),
[Railway](https://railway.app), [Aiven](https://aiven.io) ou um MySQL na
sua própria hospedagem.

Passo a passo:

1. **Suba este projeto para um repositório no GitHub** (o Render faz
   deploy a partir de um repositório Git).
2. **Crie o banco MySQL** em um dos provedores acima e rode o
   `sql/schema.sql` nele (a maioria oferece um console web ou um comando
   `mysql -h host -u usuario -p banco < sql/schema.sql`).
3. No painel do Render, clique em **New + → Web Service**, conecte o
   repositório e escolha **Runtime: Docker** (o Render detecta o
   `Dockerfile` automaticamente). Pode usar o plano gratuito.
4. Em **Environment**, adicione as variáveis de ambiente:
   - `DATABASE_URL` → `mysql://usuario:senha@host:porta/nome_do_banco`
     (o provedor do banco te dá essa string, ou monte com `DB_HOST`,
     `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME` separados — o
     `config/database.php` aceita os dois formatos)
   - `WHATSAPP_NUMERO` → `5571997063936` (ou o número real, formato
     E.164 sem "+")
   - `WHATSAPP_MENSAGEM_PADRAO` → a mensagem padrão do botão
   - `NOME_NEGOCIO` → nome exibido no site/painel
   - `ALLOW_INSTALL` → deixe **`0`** por enquanto
   - Não é preciso definir `PORT`: o Render injeta essa variável
     automaticamente e o `docker/entrypoint.sh` já lê ela para configurar
     o Apache.
5. Clique em **Deploy**. Quando terminar, mude `ALLOW_INSTALL` para `1`,
   espere o redeploy automático, acesse
   `https://seu-app.onrender.com/install.php` e crie a conta do
   administrador.
6. **Volte `ALLOW_INSTALL` para `0`** (ou remova a variável) e aguarde o
   redeploy — isso desativa a página de instalação de novo (ela responde
   404 quando desativada, então não precisa se preocupar em "esquecer"
   arquivos sensíveis no ar).
7. Pronto: `https://seu-app.onrender.com` é o site, e
   `/admin/login.php` é o painel.

> No plano gratuito do Render o serviço "dorme" após um tempo sem uso e
> demora alguns segundos para acordar na primeira visita — normal, não é
> erro.

Se preferir, o arquivo `render.yaml` incluído no projeto já descreve esse
serviço como um Blueprint (Render → New + → Blueprint), mas você ainda
precisa configurar `DATABASE_URL` manualmente pois o banco é externo.

## 5. Estrutura de pastas

```
/config        → conexão com banco, sessão segura, CSRF (acesso direto bloqueado)
/includes      → autenticação do admin e regras de disponibilidade (acesso direto bloqueado)
/api           → endpoints usados pelo formulário (verificar disponibilidade / agendar)
/admin         → login, painel, atualização de status e reagendamento
/css           → estilo (responsivo, sem emojis)
/js            → menu mobile, lógica do formulário de agendamento, chatbot
/sql           → schema.sql (acesso direto bloqueado)
/docker        → entrypoint.sh e config do Apache usados pelo Dockerfile
Dockerfile, docker-compose.yml, render.yaml → deploy em container
install.php    → criação da conta inicial do admin (protegido por ALLOW_INSTALL)
```

## 6. Como funciona a checagem de horário indisponível

Quando o cliente escolhe barbeiro + data + horário no formulário, o
JavaScript consulta `api/verificar_disponibilidade.php` e já avisa na hora
se está ocupado. Mas essa checagem "otimista" sozinha não impede que dois
clientes cliquem em "Confirmar" ao mesmo tempo — por isso, ao enviar de
fato, `api/agendar.php`:

1. Pede uma trava exclusiva do MySQL para aquele barbeiro+data+horário
   (`GET_LOCK`), o que faz a segunda requisição esperar a primeira terminar;
2. Confirma de novo que o horário segue livre;
3. Só então grava o agendamento.

Agendamentos **cancelados** liberam o horário automaticamente para novos
clientes (não contam como ocupação).

## 7. Sobre o assistente de dúvidas (chat)

O widget de chat implementado é um **assistente baseado em regras**
(palavras-chave → resposta pronta), 100% local, sem custo e sem depender de
internet ou de chave de API. Ele já responde sobre pacotes mensais, formas
de pagamento e deixa claro que **não atendemos sem agendamento prévio**.

Isso foi uma escolha deliberada: conectar o chat a uma IA generativa de
verdade (como a API da Anthropic/Claude) exige um **backend intermediário**
que guarde a chave de API em segredo — colocar a chave direto no
JavaScript do site a exporia publicamente para qualquer visitante, o que é
um risco de segurança sério. Se você quiser evoluir para uma IA
conversacional de verdade, a forma seguro é: o `chatbot.js` chama um
endpoint seu (ex: `api/chat.php`), e é o `chat.php` (rodando no servidor)
quem guarda a chave e conversa com a API da IA. Posso implementar esse
backend se você quiser.

## 8. Sugestões de atualizações futuras

- **Confirmação por WhatsApp automática**: ao agendar, enviar uma mensagem
  automática ao cliente confirmando data/horário (via WhatsApp Cloud API
  da Meta, como fizemos no sistema do escritório de advocacia).
- **Lembrete de horário**: aviso automático X horas antes do atendimento.
- **Login/histórico do cliente**: o cliente ver seus próprios agendamentos
  e cancelar/reagendar sozinho, sem precisar do admin.
- **Gestão de barbeiros e serviços pelo painel**: hoje eles estão fixos no
  banco; dá para criar telas de cadastro (como fizemos com clientes no
  sistema jurídico).
- **Bloqueio de horários por barbeiro** (folgas, almoço, férias).
- **Relatórios**: faturamento por período, serviço mais pedido, barbeiro
  mais procurado.
- **Autenticação em duas etapas (2FA) no admin**: código por e-mail/SMS
  além da senha, igual ao sistema do escritório de advocacia.
- **IA conversacional real** no chat, com backend seguro (ver seção 7).
- **HTTPS obrigatório** em produção (essencial para a sessão do admin ser
  realmente segura — o cookie já está configurado para exigir HTTPS quando
  disponível).

Quer que eu implemente algum desses agora? É só pedir.

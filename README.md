## My VHL - Alerts

Esse projeto permite criar mensagens de alertas para os usuários da Minha BVS, informando novos documentos nos temas de interesse.

### Instalação

1. Instalar a ferramenta [Mautic](https://github.com/mautic/mautic)
```markdown
git clone https://github.com/mautic/mautic.git
```
2. Instalar o repositório no diretório principal do Mautic (obrigatório nomear como `alerts`)
```markdown
cd mautic/
git clone https://github.com/bireme/my-vhl-alerts.git alerts
```
3. Renomear e configurar os arquivos:
```markdown
- config/config.php.template -> config/config.php
- mautic/config/config.php.template -> mautic/config/config.php
- mautic/config/BasicAuth.php.template -> mautic/config/BasicAuth.php
```

### Workflow

#### Mautic

- Criar os campos personalizados (nome - alias - tipo):
  - ID Minha BVS - my_vhl_id - número
  - É usuário da Minha BVS? - my_vhl_user - operador lógico
- Criar o segmento de usuários Minha BVS
  - Aplicar filtro com base no campo `my_vhl_user`
- Criar a(s) campanha(s)
  - A fonte de contatos deve ser o segmento de usuários Minha BVS

#### Alertas

- Importar a lista de usuários ativos da Minha BVS para o Mautic
```markdown
php console init
```
- Executar os _cron jobs_
  - Atualizar segmentos
  - Disparar campanha(s)

OBS: Agendar os comandos no cron do servidor.

### Cron Jobs

Para que os comandos funcionem corretamente, é necessário copiar o arquivo `cronjobs` para o diretório `mautic/app/`

#### Comandos:

- Atualizar segmentos:
```markdown
./cronjobs segments update
```
- Atualizar campanha:
```markdown
./cronjobs campaigns rebuild <id_campanha>
```
- Disparar campanhas:
```markdown
./cronjobs campaigns trigger <id_campanha>
```
- Enviar mensagens (Marketing Messages):
```markdown
./cronjobs messages send
```

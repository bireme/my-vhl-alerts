## My VHL - Alerts

[My VHL - Alerts](https://bireme.github.io/my-vhl-alerts/) permite criar mensagens de alertas para os usuários da Minha BVS, informando novos documentos nos temas de interesse.

### Instalação

1. Instalar a ferramenta [Mautic](https://github.com/mautic/mautic)
```markdown
git clone https://github.com/mautic/mautic.git
```
2. Instalar o plugin [MauticAlertsBundle](https://github.com/bireme/MauticAlertsBundle)
```markdown
cd mautic/plugins
git clone https://github.com/bireme/MauticAlertsBundle.git
```
3. Instalar o repositório no diretório principal do Mautic (obrigatório nomear como `alerts`)
```markdown
cd mautic/
git clone https://github.com/bireme/my-vhl-alerts.git alerts
```
4. Renomear e configurar os arquivos:
```markdown
config/config.php.template >> config/config.php
mautic/config/config.php.template >> mautic/config/config.php
mautic/config/BasicAuth.php.template >> mautic/config/BasicAuth.php
```

### Workflow

#### Mautic

1. Criar os campos personalizados (nome - alias - tipo):
    - `ID MinhaBVS - my_vhl_id - número`
    - `É usuário da MinhaBVS? - my_vhl_user - operador lógico`
    - `Envio de Alerta - send_alert - operador lógico`
    - `Ativo - my_vhl_active - operador lógico`
    - `Idioma dos Alertas - alerts_lang - select`
        - `Português | pt`
        - `Espanhol | es`
        - `Inglês | en`
    - `Origem da MinhaBVS - my_vhl_source - select`
        - `LDAP | ldap`
        - `BIREME Accounts | bireme_accounts`
        - `Google | google`
        - `Facebook | facebook`
        - `e-BlueInfo | e-blueinfo`
2. Criar o segmento de usuários Minha BVS
    - Aplicar filtro com base nos campos `my_vhl_user` e `send_alert`
3. Criar o email para os alertas
    - Utilizar o token `{alerts}` no corpo do email
4. Criar a campanha
    - A fonte de contatos deve ser o segmento de usuários Minha BVS

#### Alertas

1. Importar a lista de usuários ativos da Minha BVS para o Mautic
```markdown
$ php console init
```
2. Executar os _cron jobs_
    1. Atualizar segmentos
    2. Atualizar campanha
    3. Disparar campanha

OBS: Agendar os comandos no cron do servidor.

### Cron Jobs

Para que os comandos funcionem corretamente, é necessário copiar o arquivo `cronjobs` para o diretório `mautic/bin/`

#### Comandos:

- Atualizar segmentos:
```markdown
$ ./cronjobs segments update
```
- Atualizar campanha:
```markdown
$ ./cronjobs campaigns rebuild <id_campanha>
```
- Disparar campanha:
```markdown
$ ./cronjobs campaigns trigger <id_campanha>
```

#### Cronfig

Caso queria executar os _cron jobs_ pelo painel da área administrativa do Mautic, basta instalar o plugin [Cronfig](https://github.com/cronfig/mautic-cronfig)

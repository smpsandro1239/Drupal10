# Desafio Web Developer - Drupal 10: Gestão de Candidaturas

Este projeto implementa um site básico em Drupal 10 para gestão de candidaturas a vagas de emprego, conforme o desafio técnico proposto.

## Funcionalidades Implementadas

1.  **Sistema de Submissão de CVs (Componente 1):**
    *   **Tipo de Conteúdo "Candidatura"**:
        *   Campos: Nome, Morada, Distrito (dropdown), Idade do candidato, Anexo do CV (ficheiro PDF/DOC/DOCX).
        *   Configurado através do módulo customizado `desafio_setup`.
    *   **Página de Pesquisa de CVs**:
        *   URL: `/pesquisar-cvs`
        *   Exibe CVs submetidos.
        *   Permite filtrar por Distrito e Idade.
        *   *Esta View deve ser criada manualmente pelo utilizador através da UI do Drupal.*

2.  **Bloco de Saudação Dinâmica (Componente 2):**
    *   Módulo customizado `greeting_block`.
    *   Exibe "Bom dia!", "Boa tarde!" ou "Boa noite!" com base na hora do servidor.
    *   O bloco fica disponível para ser adicionado a qualquer região através da UI de Blocos do Drupal.

3.  **Tema Personalizado com Bootstrap 5 (Componente 3):**
    *   Tema customizado `desafio_theme`, subtema do `bootstrap5`.
    *   Inclui estrutura SASS para personalização de estilos.
    *   Template Twig `node--candidatura.html.twig` para estilizar a visualização das candidaturas.

## Estrutura de Diretórios Relevantes

```
web/
├── modules/
│   └── custom/
│       ├── desafio_setup/      # Módulo para configuração inicial (Content Type Candidatura)
│       │   ├── config/install/ # Arquivos YAML de configuração
│       │   └── desafio_setup.info.yml
│       └── greeting_block/     # Módulo do bloco de saudação
│           ├── src/Plugin/Block/GreetingBlock.php
│           └── greeting_block.info.yml
└── themes/
    └── custom/
        └── desafio_theme/      # Tema personalizado
            ├── css/
            │   └── style.css   # CSS compilado (precisa ser gerado a partir do SASS)
            ├── scss/           # Arquivos SASS
            │   ├── _variables.scss
            │   └── style.scss
            ├── templates/
            │   └── node--candidatura.html.twig
            ├── desafio_theme.info.yml
            └── desafio_theme.libraries.yml
```

## Instruções de Instalação e Configuração Local

**Pré-requisitos:**
*   Ambiente Drupal 10 funcional (PHP, Composer, Servidor Web, Base de Dados).
*   Drush (recomendado).
*   SASS Compiler (e.g., Dart SASS: `npm install -g sass`).
*   Tema base Bootstrap 5 para Drupal (`drupal/bootstrap5`).

**Passos:**

1.  **Clonar/Copiar os Ficheiros:**
    *   Coloque os módulos `desafio_setup` e `greeting_block` na pasta `web/modules/custom/`.
    *   Coloque o tema `desafio_theme` na pasta `web/themes/custom/`.

2.  **Instalar Tema Base Bootstrap 5:**
    *   Se ainda não o fez, execute na raiz do seu projeto Drupal:
        ```bash
        composer require drupal/bootstrap5
        ```
    *   Aceda a `/admin/appearance` e instale o tema "Bootstrap 5" (não precisa de o definir como padrão).

3.  **Ativar Módulo de Setup (Content Type):**
    *   Aceda a `/admin/modules`.
    *   Ative o módulo "Desafio Setup".
    *   Isto irá criar o tipo de conteúdo "Candidatura" com os campos especificados.
    *   Em alternativa, via Drush: `drush en desafio_setup -y`

4.  **Configurar Permissões para "Candidatura":**
    *   Aceda a `/admin/people/permissions`.
    *   Na secção "Node", para o tipo de conteúdo "Candidatura", atribua as permissões:
        *   **Anonymous user**: `Candidatura: Create new content`
        *   **Authenticated user**: `Candidatura: Create new content` (e outras que desejar, como editar/ver as próprias).
    *   Guarde as permissões.

5.  **Criar View "Pesquisar CVs":**
    *   Aceda a `/admin/structure/views` e clique em "Add view".
    *   **Configuração básica:**
        *   Nome: `Pesquisar CVs`
        *   Mostrar: `Content` do tipo `Candidatura`.
        *   Ordenar por: `Newest first`.
    *   **Criar página:**
        *   Título da Página: `Pesquisar Candidaturas`
        *   Caminho: `/pesquisar-cvs`
        *   Formato de exibição: `Table` ou `Unformatted list` (usando "Rendered entity" para aplicar o template Twig).
    *   **Configuração da View (após clicar em "Continue & configure"):**
        *   **Campos:** Adicione `Nome (field_nome)`, `Distrito (field_distrito)`, `Idade (field_idade_candidato)`, `Anexo CV (field_anexo_cv)`, `Authored on (Data de submissão)`.
        *   **Filtros Expostos:**
            *   `Content: Distrito (field_distrito)` (Dropdown)
            *   `Content: Idade do candidato (field_idade_candidato)` (Operador: "Is greater than or equal to")
        *   **Paginação:** `Display a specified number of items` | `20 items`.
    *   Guarde a View.

6.  **Ativar Módulo "Greeting Block":**
    *   Aceda a `/admin/modules`.
    *   Ative o módulo "Greeting Block".
    *   Via Drush: `drush en greeting_block -y`
    *   Limpe o cache do Drupal: `drush cr` ou em `/admin/config/development/performance`.

7.  **Adicionar Bloco de Saudação:**
    *   Aceda a `/admin/structure/block`.
    *   Escolha uma região e clique em "Place block".
    *   Procure e adicione o bloco "Saudação Dinâmica".
    *   Configure a visibilidade se necessário e guarde.

8.  **Compilar SASS para o Tema:**
    *   Navegue até à raiz do seu projeto Drupal no terminal.
    *   Execute o comando de compilação SASS:
        ```bash
        sass web/themes/custom/desafio_theme/scss/style.scss web/themes/custom/desafio_theme/css/style.css
        ```
    *   Se tiver um sistema de build (Gulp, Webpack), use o comando apropriado.

9.  **Ativar o Tema "Desafio Theme":**
    *   Aceda a `/admin/appearance`.
    *   Encontre "Desafio Theme" e clique em "Install and set as default".
    *   Limpe o cache do Drupal novamente.

10. **Testar:**
    *   Navegue pelo site, submeta candidaturas, pesquise CVs e verifique se o bloco de saudação e os estilos do tema estão a funcionar corretamente.

## Scripts Úteis (Exemplos com Drush e SASS CLI)

*   Limpar cache do Drupal:
    ```bash
    drush cr
    ```
*   Ativar um módulo:
    ```bash
    drush en NOME_DO_MODULO -y
    ```
*   Compilar SASS (manual):
    ```bash
    sass web/themes/custom/desafio_theme/scss/style.scss web/themes/custom/desafio_theme/css/style.css --watch
    ```
    (O argumento `--watch` recompila automaticamente quando os ficheiros SASS são alterados).

---

Este README fornece os passos essenciais para configurar e testar o projeto.
Lembre-se que a criação da View e a compilação do SASS são passos manuais cruciais.
```

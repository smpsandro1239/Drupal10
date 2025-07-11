# Desafio Web Developer - Drupal 10: Gestão de Candidaturas

Este projeto implementa um site básico em Drupal 10 para gestão de candidaturas a vagas de emprego, conforme o desafio técnico proposto. Demonstra funcionalidades core do Drupal, desenvolvimento de módulos e temas.

## Funcionalidades Implementadas

1.  **Sistema de Submissão de CVs (Componente 1):**
    *   **Tipo de Conteúdo "Candidatura"**:
        *   Campos: Nome, Morada, Distrito (dropdown), Idade do candidato, Anexo do CV (ficheiro PDF/DOC/DOCX).
        *   Toda a configuração do tipo de conteúdo e dos seus campos é fornecida pelo módulo `desafio_setup` através de ficheiros YAML em `config/install`.
    *   **Página de Pesquisa de CVs (`/pesquisar-cvs`)**:
        *   Exibe CVs submetidos numa tabela com paginação.
        *   Permite filtrar por Distrito e Idade (mínima).
        *   A View é definida no ficheiro `views.view.pesquisar_cvs.yml` dentro do módulo `desafio_setup`, sendo criada automaticamente com a ativação do módulo.

2.  **Bloco de Saudação Dinâmica (Componente 2):**
    *   Módulo customizado `greeting_block`.
    *   Exibe "Bom dia!", "Boa tarde!" ou "Boa noite!" com base na hora do servidor.
    *   O bloco é automaticamente posicionado na região "Content" do tema `desafio_theme` através do ficheiro `block.block.desafio_theme_greetingblock.yml` no módulo `greeting_block`.

3.  **Tema Personalizado com Bootstrap 5 (Componente 3):**
    *   Tema customizado `desafio_theme`, subtema do `bootstrap5`.
    *   Inclui estrutura SASS para personalização de estilos (`scss/_variables.scss` e `scss/style.scss`).
    *   Template Twig `node--candidatura.html.twig` para estilizar a visualização detalhada das candidaturas.

4.  **Qualidade de Código e Boas Práticas:**
    *   **Gestão de Configuração:** As configurações do tipo de conteúdo, da View de pesquisa e do posicionamento do bloco são fornecidas como ficheiros YAML nos respetivos módulos, demonstrando o uso do sistema de Configuration Management do Drupal.
    *   **Testes Unitários:** O módulo `greeting_block` inclui um teste unitário (`GreetingBlockTest.php`) para validar a lógica de saudação do bloco.

## Estrutura de Diretórios Relevantes

```
web/
├── modules/
│   └── custom/
│       ├── desafio_setup/      # Módulo para configuração inicial (Content Type Candidatura, View Pesquisar CVs)
│       │   ├── config/install/ # Arquivos YAML de configuração
│       │   │   ├── node.type.candidatura.yml
│       │   │   ├── field.storage.node.*.yml
│       │   │   ├── field.field.node.candidatura.*.yml
│       │   │   ├── core.entity_form_display.node.candidatura.default.yml
│       │   │   ├── core.entity_view_display.node.candidatura.default.yml
│       │   │   └── views.view.pesquisar_cvs.yml
│       │   └── desafio_setup.info.yml
│       └── greeting_block/     # Módulo do bloco de saudação
│           ├── config/install/
│           │   └── block.block.desafio_theme_greetingblock.yml
│           ├── src/Plugin/Block/GreetingBlock.php
│           ├── tests/src/Unit/GreetingBlockTest.php
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
*   Ambiente Drupal 10 funcional (PHP >= 8.1, Composer, Servidor Web, Base de Dados).
*   Drush (recomendado).
*   SASS Compiler (e.g., Dart SASS: `npm install -g sass`).
*   Tema base Bootstrap 5 para Drupal (`drupal/bootstrap5`).

**Passos:**

1.  **Clonar/Copiar os Ficheiros:**
    *   Coloque os módulos `desafio_setup` e `greeting_block` na pasta `web/modules/custom/`.
    *   Coloque o tema `desafio_theme` na pasta `web/themes/custom/`.

2.  **Instalar Dependências (incluindo Tema Base Bootstrap 5):**
    *   Execute na raiz do seu projeto Drupal:
        ```bash
        composer require drupal/bootstrap5
        ```
    *   (Opcional, se o Bootstrap 5 ainda não estiver instalado como tema) Aceda a `/admin/appearance` e instale o tema "Bootstrap 5". Ele não precisa ser o tema padrão, apenas estar instalado para que o `desafio_theme` funcione como subtema.

3.  **Ativar os Módulos Customizados:**
    *   Aceda a `/admin/modules`.
    *   Ative os módulos "Desafio Setup" e "Greeting Block".
    *   Em alternativa, via Drush:
        ```bash
        drush en desafio_setup greeting_block -y
        ```
    *   **Importante:** A ativação destes módulos irá:
        *   Criar o tipo de conteúdo "Candidatura" e seus campos.
        *   Criar a View "Pesquisar CVs" em `/pesquisar-cvs`.
        *   Posicionar o bloco "Saudação Dinâmica" (se o tema `desafio_theme` já estiver ativo ou for ativado posteriormente).

4.  **Configurar Permissões para "Candidatura":**
    *   Aceda a `/admin/people/permissions`.
    *   Na secção "Node", para o tipo de conteúdo "Candidatura", atribua as permissões:
        *   **Anonymous user**: `Candidatura: Create new content`
        *   **Authenticated user**: `Candidatura: Create new content` (e outras que desejar, como editar/ver as próprias).
    *   Guarde as permissões.

5.  **Compilar SASS para o Tema:**
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

## Melhorias de Profissionalismo (Recente)

Foram implementadas melhorias significativas no tema `desafio_theme` e nos templates para um aspeto mais profissional:

*   **Estilos SASS Refinados:**
    *   Paleta de cores, tipografia, espaçamento e estilos globais foram aprimorados em `scss/_variables.scss` e `scss/style.scss`.
    *   Estilização melhorada para formulários, mensagens do Drupal, tabelas de Views, paginação, e o bloco de saudação.
    *   Adicionada estrutura básica para cabeçalho e rodapé do site.
*   **Template `node--candidatura.html.twig` Melhorado:**
    *   Utiliza classes do Bootstrap 5 (como Cards e Grid) para uma apresentação mais estruturada e profissional das páginas de candidatura individual.
*   **Ajustes no Formulário de Candidatura:**
    *   Adicionados placeholders e descrições de ajuda para maior clareza nos campos do formulário de submissão de candidatura (via arquivos YAML no módulo `desafio_setup`).
*   **Recomendações para a View "Pesquisar CVs":**
    *   Foram dadas sugestões detalhadas sobre como configurar a View na interface do Drupal para melhor aproveitar os estilos do tema e as funcionalidades do Bootstrap (ex: formato Tabela vs. Lista de entidades renderizadas, classes CSS, etc.).

**Importante após atualizações:**
*   **Recompile o SASS:** Sempre que houver alterações nos ficheiros `.scss` do tema `desafio_theme`, é crucial recompilar para `css/style.css`:
    ```bash
    sass web/themes/custom/desafio_theme/scss/style.scss web/themes/custom/desafio_theme/css/style.css
    ```
*   **Limpe o Cache do Drupal:** Após ativar módulos, alterar configurações, ou atualizar o tema, limpe sempre o cache:
    ```bash
    drush cr
    ```
    Ou através da interface administrativa em `/admin/config/development/performance`.
*   **Aplique as Configurações YAML:** Se atualizou ficheiros YAML de configuração de módulos já instalados (como `desafio_setup`), essas alterações precisam ser importadas para a configuração ativa do site (geralmente via `drush config:import` ou pela UI de gestão de configuração), ou aplicando as alterações manualmente na UI do Drupal.

Estas melhorias visam elevar a qualidade visual e a experiência de utilização do site.
```

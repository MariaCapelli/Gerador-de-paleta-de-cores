# 🎨 ChromaHarmonia - Gerador de Paleta de Cores Harmônicas

> Sistema web moderno para geração, visualização, edição, exportação e auditoria completa de paletas de cores harmônicas baseadas no **círculo cromático**, desenvolvido rigorosamente de acordo com as especificações de [requisitos.md](requisitos.md) e inspirado no [Adobe Color Wheel](https://color.adobe.com/br/create/color-wheel) e [Adobe Color Image Extractor](https://color.adobe.com/br/create/image).

---

## 📌 Sumário
- [Recursos Principais](#-recursos-principais)
- [Harmonias Cromáticas Implementadas](#-harmonias-cromáticas-implementadas)
- [Stack Tecnológica](#-stack-tecnológica)
- [Arquitetura e Separação de Responsabilidades](#-arquitetura-e-separação-de-responsabilidades)
- [Banco de Dados & Auditoria](#-banco-de-dados--auditoria)
- [Instalação e Como Executar](#-instalação-e-como-executar)
- [Conformidade com os Requisitos](#-conformidade-com-os-requisitos)

---

## 🚀 Recursos Principais

1. **Círculo Cromático Interativo (Estilo Adobe Color)**:
   - Roda de cores renderizada em HTML5 Canvas interativo.
   - Seleção da cor base arrastando ponteiros pelo círculo ou informando código HEX.
   - Ajuste fino através de sliders RGB e Native Color Picker.
   - Sincronização em tempo real de ponteiros de harmonia com cálculo de ângulos trigonométricos.

2. **Extração de Cores a Partir de Imagem**:
   - Faça upload ou arraste qualquer imagem (PNG, JPG, WEBP).
   - 5 marcadores interativos sobre a imagem para amostragem pontual de pixels em tempo real.
   - Salve a paleta extraída diretamente na sua galeria.

3. **Exibição Multiformato e Cópia em 1 Clique**:
   - Cada cor gerada exibe instantaneamente seus valores em:
     - **HEX** (ex: `#3B82F6`)
     - **RGB** (ex: `rgb(59, 130, 246)`)
     - **HSL** (ex: `hsl(217, 91%, 60%)`)
   - Cópia instantânea para a área de transferência com notificação visual não obstrutiva (**TOAST**).
   - Cálculo automático de contraste (WCAG) para legibilidade de texto sobre a cor.

4. **Gestão Completa de Paletas (CRUD)**:
   - Salvar paleta informando nome, descrição, cor base e tipo de harmonia.
   - Visualização com detalhes técnicos e exportação.
   - Edição de metadados e cores.
   - Exclusão com diálogo de confirmação via **MODAL customizado** (sem `alert`/`confirm` nativos).

5. **Exportação de Código**:
   - Exportação em **Variáveis CSS** (`:root { --color-1: ... }`)
   - Exportação em **Configuração do Tailwind CSS** (`theme.extend.colors`)
   - Exportação em formato **JSON**
   - Exportação em arquivo vetorial **SVG Swatch**

6. **Auditoria Completa & Rastreabilidade**:
   - Todo evento de **Criação**, **Edição** ou **Exclusão** gera um registro no banco de dados.
   - Armazena usuário responsável, ação realizada, data/hora exata, IP de origem e snapshot das cores alteradas.
   - Painel de auditoria com estatísticas e filtros por ação, usuário e período.

---

## 🌈 Harmonias Cromáticas Implementadas

A lógica matemática de geração das cores é isolada na classe `ColorService` (`services/ColorService.php`):

| Harmonia | Descrição Teórica |
| :--- | :--- |
| **Análoga** | Matizes vizinhos no círculo cromático (-30°, -15°, 0°, +15°, +30°). Transmite harmonia visual e serenidade. |
| **Monocromática** | Variações equilibradas de luminosidade e saturação de um mesmo matiz. Ideal para designs minimalistas. |
| **Tríade** | Três pontos equidistantes no círculo cromático espaçados em 120° (0°, 120°, 240°). Vibrante e equilibrada. |
| **Complementar** | Cor base combinada com o seu ângulo diametralmente oposto a 180°. Alto contraste e impacto visual. |
| **Complementar Dividida** | Cor base combinada com as duas cores adjacentes ao seu complemento (180° ± 30°). Menos agressiva que a complementar pura. |
| **Tétrade (Quadrada)** | Quatro cores distribuídas em ângulos retos de 90° (0°, 90°, 180°, 270°). Grande riqueza cromática. |
| **Homóloga** | Matizes adjacentes imediatos com transição suave e brilho gradual. |

---

## 🛠️ Stack Tecnológica

- **Backend**: PHP 8+ estruturado com sessões nativas (`session_start()`).
- **Banco de Dados**: MySQL / MariaDB via conexão segura **PDO** (com Prepared Statements).
- **Frontend**: HTML5 Semântico, CSS3, Tailwind CSS (Mobile-First) e Vanilla JavaScript (ES6+).
- **Componentes Visuais**: HTML5 Canvas 2D para renderização de gradientes cromáticos e marcadores polares.

---

## 📁 Arquitetura e Separação de Responsabilidades

```
Gerador-de-paleta-de-cores/
├── bd.php                       # Conexão PDO segura e auto-inicializador de tabelas
├── database.sql                 # Script SQL para criação do banco e estrutura de tabelas
├── index.php                    # Interface principal com Círculo Cromático interativo
├── extrair_imagem.php           # Extrator de cores a partir de fotos (Canvas Sampler)
├── paletas.php                  # Galeria de paletas salvas com busca, filtros e modais
├── historico.php                # Tela de Histórico e Auditoria com filtros e visualizador JSON
├── login.php                    # Login seguro com sessões nativas PHP
├── registrar.php                # Formulário de cadastro de novos usuários
├── logout.php                   # Encerramento seguro de sessão
│
├── includes/                    # Componentes reutilizáveis
│   ├── header.php               # Barra de navegação responsiva e menu mobile
│   ├── footer.php               # Rodapé com créditos e links técnicos
│   ├── toast.php                # Sistema de notificações Toast dinâmico e via sessão
│   └── modal.php                # Modais customizados para substituir alert() e confirm()
│
├── services/                    # Camada de Regras de Negócio Isoladas
│   ├── ColorService.php         # Algoritmos cromáticos e conversões HEX <-> RGB <-> HSL
│   ├── AuthService.php          # Gerenciamento de credenciais, bcrypt e sessões
│   └── AuditService.php         # Rastreabilidade e gravação de logs de auditoria
│   └── PaletteService.php       # Operações de CRUD de paletas via PDO
│
├── actions/                     # Controladores de Ação (POST / Endpoints)
│   ├── salvar_paleta.php        # Persistência de paletas com gatilho de auditoria
│   ├── editar_paleta.php        # Atualização de paletas com histórico comparativo
│   ├── excluir_paleta.php       # Exclusão segura de paletas com registro de auditoria
│   └── auth.php                 # Autenticação, login demo e logout
│
├── api/                         # Endpoints RESTful para requisições assíncronas (AJAX)
│   ├── generate.php             # Geração de harmonias em tempo real (JSON)
│   ├── palette_info.php         # Consulta de dados detalhados de uma paleta
│   └── export.php               # Exportação em CSS, Tailwind, JSON e SVG
│
└── assets/                      # Recursos de Interface
    ├── css/
    │   └── style.css            # Estilos customizados, scrollbars e ponteiros do canvas
    └── js/
        ├── app.js               # Gerenciador global de Toasts, Modais e Clipboard
        ├── color-wheel.js       # Implementação matemática do círculo cromático
        ├── generator.js         # Orquestrador da tela principal e swatches
        └── image-extractor.js   # Amostrador de cores via canvas para fotos
```

---

## 🗄️ Banco de Dados & Auditoria

O banco de dados é inicializado automaticamente na primeira execução através do arquivo `bd.php`, garantindo facilidade no ambiente XAMPP.

### Diagrama Simplificado das Tabelas:

```
[usuarios] 1 -------< N [paletas]
    |
    +---------------< N [auditoria]
```

- **`usuarios`**: Armazena credenciais criptografadas via `password_hash()` (Bcrypt).
- **`paletas`**: Armazena as informações da paleta (nome, descrição, harmonia, cor base, array JSON com as cores e timestamps).
- **`auditoria`**: Armazena toda operação (`usuario_id`, `usuario_nome`, `acao`, `registro_afetado`, `detalhes`, `ip_origem`, `data_hora`).

---

## ⚙️ Instalação e Como Executar

### Opção 1: Executando via Servidor Interno do PHP (Mais Rápido)
1. Certifique-se de que o **MySQL** está rodando no seu computador (por exemplo, pelo Painel do XAMPP).
2. Abra um terminal na pasta do projeto e inicie o servidor embutido do PHP:
   ```bash
   php -S localhost:8000
   ```
3. Abra seu navegador em:
   ```
   http://localhost:8000
   ```

### Opção 2: Executando via Apache do XAMPP
1. Inicie o **Apache** e o **MySQL** pelo **XAMPP Control Panel**.
2. Coloque a pasta do projeto dentro de `C:\xampp\htdocs\gerador-paletas` ou crie um link.
3. Acesse no navegador:
   ```
   http://localhost/gerador-paletas
   ```

> **Credenciais de Teste Padrão**:
> - **E-mail**: `maria@paletas.com`
> - **Senha**: `admin123`
> *(Ou clique no botão "Demo Rápido" na barra superior para logar instantaneamente em 1 clique)*.

---

## ✅ Conformidade com os Requisitos

| Requisito do `requisitos.md` | Status | Implementação |
| :--- | :---: | :--- |
| **PHP estruturado com sessões nativas** | Concluído | `AuthService.php`, `session_start()`, sessões seguras em todo o ciclo de vida. |
| **Banco de dados MySQL com PDO** | Concluído | `bd.php`, todas as queries utilizam prepared statements com `:placeholders`. |
| **HTML5, CSS e Tailwind CSS Mobile-First** | Concluído | Layout totalmente responsivo com grid flexível, menu hamburger e cards táteis. |
| **Cores base e harmonia no círculo cromático** | Concluído | Canvas 2D interativo com cálculo polar em `color-wheel.js` e `ColorService.php`. |
| **Harmonias especificadas** | Concluído | Monocromática, Análoga, Complementar, Complementar Dividida, Tríade, Tétrade e Homóloga. |
| **Exibição e cópia de HEX, RGB e HSL** | Concluído | Cada swatch exibe os 3 formatos com botão individual de cópia rápida e toast de confirmação. |
| **Salvar paleta (nome, descrição e usuário)** | Concluído | Modal dedicado, persistência vinculada ao `usuario_id` da sessão. |
| **Visualizar, Editar e Excluir paletas** | Concluído | Interface de gerenciamento completa em `paletas.php` com modais interativos. |
| **Página de Histórico e Auditoria** | Concluído | Tela `historico.php` com logs detalhados de criação, edição e exclusão de paletas. |
| **Lógica de cores separada da interface** | Concluído | `ColorService.php` e `PaletteService.php` 100% isolados das views e endpoints. |
| **Mensagens de feedback claras na interface (TOAST)** | Concluído | `toast.php` + `window.showToast()` com animações suaves e auto-dismiss. |
| **Substituição de caixas nativas por MODAL** | Concluído | Zero `alert()` ou `confirm()`; todos substituídos pelo `universalModal`. |
| **Referências Adobe Color** | Concluído | Roda cromática estilo Adobe Color + Extrator de cores a partir de foto. |

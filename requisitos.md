# Gerador de Paleta de Cores

## Objetivos
Desenvolver um sistema web para geração de paletas de cores homologas, análogas e tríade a partir de uma cor escolhida pelo usuário, utilizando o círculo cromático para gerar combinações de cores harmônicas. O sistema deve permitir gerar, visualizar, salvar, editar e excluir paletas de cores, mantendo um histórico completo das alterações realizadas pelos usuários para futuras auditorias.

### Stack Tecnológico
- Backend: PHP estruturado com sessões nativas
- Banco de dados: MySQL
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negócio (CORE)
- O usuário deve escolher uma cor base e selecionar o tipo de harmonia desejada. O sistema deverá gerar automaticamente as cores da paleta com base no círculo cromático.
- A paleta poderá utilizar harmonias como monocromática, análoga, complementar, complementar dividida, tríade e tétrade. As cores geradas deverão apresentar seus códigos HEX, RGB e HSL, permitindo ao usuário copiar os códigos.
- O usuário poderá salvar a paleta informando nome e descrição. As paletas salvas deverão ficar vinculadas ao usuário responsável e poderão ser posteriormente visualizadas, editadas ou excluídas.
- O sistema deverá possuir uma página de histórico para consulta das paletas e das alterações realizadas. Toda criação, edição ou exclusão deverá gerar um registro de auditoria contendo o usuário, ação, data e registro afetado.
- A lógica de geração das cores deverá ser separada da interface, permitindo adicionar novas harmonias futuramente sem alterar a estrutura principal do sistema.

##### Regras Globais
- Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas lógicas complexas.
- Separe os arquivos de forma lógica: um arquivo para conexão com a base (bd.php) e scripts de backend isolados e views em HTML5/PHP, nunca faça o sistema como um monolito, deixe sempre separados todas as regras para facilitar os futuros upgrades.
 - Estilize as telas em Tailwind de forma responsiva priorizando o MobileFrist.
 - Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
 - sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL

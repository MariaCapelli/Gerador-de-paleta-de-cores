# Gerador de Paleta de Cores

## Objetivos
Desenvolver um sistema web para geração de paletas de cores homologas, análogas e tríade a partir de uma cor escolhida pelo usuário, utilizando o círculo cromático para gerar combinações de cores harmônicas. O sistema deve permitir gerar, visualizar, salvar, editar e excluir paletas de cores, mantendo um histórico completo das alterações realizadas pelos usuários para futuras auditorias.

### Stack Tecnológico
- Backend: PHP estruturado com sessões nativas
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negócio (CORE)
O sistema deve ter uma página de históricos e manter sempre os logs de qualquer alteração feita por 
qualquer usuário, para auditorias futuras.

##### Regras Globais
- Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas lógicas complexas.
- Separe os arquivos de forma lógica: um arquivo para conexão com a base (bd.php) e scripts de backend isolados e views em HTML5/PHP, nunca faça o sistema como um monolito, deixe sempre separados todas as regras para facilitar os futuros upgrades.
 - Estilize as telas em Tailwind de forma responsiva priorizando o MobileFrist.
 - Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
 - sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL

# Exemplo de Docker Compose usando Flask e Redis

Este exemplo foi retirado da documentação do Docker Compose, e pode ser encontrado em sua forma original em [https://docs.docker.com/compose/gettingstarted/](https://docs.docker.com/compose/gettingstarted/).

Assim como no exemplo [1. Site simples em Flask](../1.site-flask/), vamos construir uma imagem para o nosso site com Flask baseada na imagem do Python. Porém, é o Docker Compose que vai construir essa imagem para nós. Também precisamos que esse site se comunique com o Redis, e mais uma vez deixaremos para o Docker Compose o trabalho de subir os containers e "amarrá-los" juntos. Para quem não conhece, o Redis é um banco de dados em memória de código aberto, que serve para armazenar conjuntos de chave-valor (basicamente uma _hash table_). Mais informações no site [https://redis.io/](https://redis.io/).

Primeiramente, criaremos um arquivo `docker-compose.yml` com o seguinte conteúdo:
```yml
version: "3.9"
services:
  web:
    build: .
    ports:
      - "8000:5000"
  redis:
    image: "redis:7.0-alpine"
```

Esse arquivo define dois serviços:
- `web`, que é construído a partir do `Dockerfile` na pasta atual (`build: .`), e cuja porta 5000 do container ficará vinculada à porta 8000 da nossa máquina (`ports: - "8000:5000"`);
- `redis`, que é simplesmente um container rodando a imagem `redis:7.0-alpine` baixada do Docker Hub (mais detalhes sobre esta imagem em [https://hub.docker.com/_/redis](https://hub.docker.com/_/redis)).

Note que não expomos a porta do Redis (por padrão é a 6379) na definição do `docker-compose.yml`. O Docker Compose automaticamente cria uma rede privada e coloca os dois containers nela, portanto a comunicação entre eles é liberada, e o serviço `web` consegue chegar na porta 6379 do `redis` sem nenhuma configuração adicional, usando o próprio nome do serviço definido no `docker-compose.yml` (no caso, `redis`). Só precisamos expor a porta `5000` do container `web` pois queremos acessar o site da nossa máquina host (ou seja, de fora do container).

Com essa definição feita, basta executar o comando:

```bash
# Se você estiver usando uma versão antiga do Docker, talvez precise usar um hífen no comando: docker-compose up
docker compose up
```

Esse comando automaticamente constrói a imagem para o serviço `web` compilando o `Dockerfile` na pasta local, cria os containers para os serviços `web` e `redis`, e inicia eles. Pronto, agora acessando a porta 8000 do nosso computador pelo navegador ([http://localhost:8000/](http://localhost:8000/)) poderemos ver o site rodando no container que preparamos (lembre que o Flask roda na porta 5000, mas acessamos pela porta 8000 no nosso computador pois fizemos o mapeamento 8000:5000 no `docker-compose.yml`).

Da forma como usamos, esse comando "prende" o terminal e mostra os logs dos containers que foram iniciados, portanto pressionar <kbd>Ctrl</kbd>+<kbd>C</kbd> pára os containers. Caso você queira executar os containers em segundo plano e continuar usando o terminal, basta passar a flag `-d` no comando: `docker compose up -d`. Aí, para parar os containers basta usar o comando `docker compose stop`.

Por fim, para apagar os containers criados pelo comando `docker compose up`, basta usar o comando `docker compose down`. Ele automaticamente remove os containers criados e a rede que foi criada para interligá-los. As imagens compiladas continuam disponíveis no seu _registry_ Docker local.

Caso queira verificar o resultado final no seu computador sem precisar compilar a imagem, ela está disponível já compilada no repositório de pacotes do GitHub. Basta alterar o `docker-compose.yml` para apontar para a imagem:

```yml
version: "3.9"
services:
  web:
    image: "ghcr.io/larcc-group/escola-inverno-2022-docker:2-flask-redis-compose"
    ports:
      - "8000:5000"
  redis:
    image: "redis:7.0-alpine"
```
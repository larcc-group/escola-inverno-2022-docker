# Sistema de lista de tarefas em PHP + MySQL ou Redis

Aqui temos uma aplicação em PHP de uma lista de tarefas (_To-Do list_) preparada para funcionar com dois bancos diferentes: MySQL e Redis. A aplicação roda na imagem do PHP versão 8, com servidor Apache embutido (`php:8.0-apache`).

### Usando MySQL
Para construir a aplicação usando o MySQL vamos criar o arquivo `Dockerfile` com o seguinte conteúdo:

```dockerfile
FROM php:8.0-apache
RUN docker-php-ext-install mysqli
COPY src/* /var/www/html/
```

Conforme a [documentação da imagem do PHP no Docker Hub](https://hub.docker.com/_/php), esta imagem possui executáveis auxiliares pra gerenciar as extensões do PHP, tal como o `docker-php-ext-install` que usamos aqui para instalar a extensão `mysqli`, usada para conectar no banco MySQL. A imagem com Apache também está preparada para servir os arquivos na pasta `/var/www/html/`, motivo pelo qual copiamos os arquivos php da pasta `src` para lá.

Com o `Dockerfile` preparado, vamos criar o `docker-compose.yml` com o conteúdo:
```yml
version: '3.9'
services:
  app-mysql:
    build:
      context: .
    environment:
      - MYSQL_HOST=banco-mysql
    ports:
      - 8080:80
  banco-mysql:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=senhasupersecreta
      - MYSQL_DATABASE=bancotodo
      - MYSQL_USER=admin
      - MYSQL_PASSWORD=123
    volumes:
      - ./banco:/docker-entrypoint-initdb.d
```

Esse arquivo define dois serviços:
- `app-mysql`: é construído a partir do `Dockerfile` que criamos, mapeando a porta `8080` da máquina para a porta `80` onde o Apache é disponibilizado no container. O caminho do banco de dados é passado na variável de ambiente `MYSQL_HOST`, e é o nome do container que define o serviço do MySQL;
- `banco-mysql`: é um container rodando a imagem `mysql:8.0` (baixada automaticamente do Docker Hub, mais detalhes em [https://hub.docker.com/_/mysql](https://hub.docker.com/_/mysql)). Definimos variáveis de ambiente com as configurações de usuário e senha que devem ser criados e montamos um volume com o script da pasta `banco` no caminho `/docker-entrypoint-initdb.d` do container. Conforme a [documentação no Docker Hub](https://hub.docker.com/_/mysql), esse script será executado automaticamente quando o banco for iniciado.

Com essa definição feita, para iniciar os serviços basta executar o comando:

```bash
# Se você estiver usando uma versão antiga do Docker, talvez precise usar um hífen no comando: docker-compose up
docker compose up
```

Caso queira verificar o resultado final no seu computador sem precisar compilar a imagem, ela está disponível já compilada no repositório de pacotes do GitHub. Basta alterar o `docker-compose.yml` para apontar para a imagem:

```yml
version: '3.9'
services:
  app-mysql:
    image: ghcr.io/larcc-group/escola-inverno-2022-docker:3-php-todo-list-mysql
    environment:
      - MYSQL_HOST=banco-mysql
    ports:
      - 8080:80
  banco-mysql:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=senhasupersecreta
      - MYSQL_DATABASE=bancotodo
      - MYSQL_USER=admin
      - MYSQL_PASSWORD=123
    volumes:
      - ./banco:/docker-entrypoint-initdb.d
```

### Usando Redis
Para construir a aplicação usando o Redis vamos criar o arquivo `Dockerfile` com o seguinte conteúdo:

```dockerfile
FROM php:8.0-apache
RUN pecl install redis-5.3.7 \
	&& docker-php-ext-enable redis
COPY src/* /var/www/html/
```

Fazemos a instalação da extensão do Redis usando o comando `pecl`, e o auxiliar `docker-php-ext-enable` para habilitar a extensão instalada.

Com o `Dockerfile` preparado, vamos criar o `docker-compose.yml` com o conteúdo:
```yml
version: '3.9'
services:
  app-redis:
    build:
      context: .
    environment:
      - REDIS_HOST=banco-redis
    ports:
      - 8080:80
  banco-redis:
    image: redis:7.0
```

Esse arquivo define dois serviços:
- `app-redis`: é construído a partir do `Dockerfile` que criamos, mapeando a porta `8080` da máquina para a porta `80` onde o Apache é disponibilizado no container. O caminho do banco de dados é passado na variável de ambiente `MYSQL_HOST`, e é o nome do container que define o serviço do MySQL;
- `banco-redis`: é um container rodando a imagem `redis:7.0` (baixada automaticamente do Docker Hub, mais detalhes em [https://hub.docker.com/_/redis](https://hub.docker.com/_/redis)).

Com essa definição feita, para iniciar os serviços basta executar o comando:

```bash
# Se você estiver usando uma versão antiga do Docker, talvez precise usar um hífen no comando: docker-compose up
docker compose up
```

Caso queira verificar o resultado final no seu computador sem precisar compilar a imagem, ela está disponível já compilada no repositório de pacotes do GitHub. Basta alterar o `docker-compose.yml` para apontar para a imagem:

```yml
version: '3.9'
services:
  app-redis:
    image: ghcr.io/larcc-group/escola-inverno-2022-docker:3-php-todo-list-redis
    environment:
      - REDIS_HOST=banco-redis
    ports:
      - 8080:80
  banco-redis:
    image: redis:7.0
```
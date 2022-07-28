# Exemplo de site simples usando Flask

Este é um exemplo de site simples usando o Flask. O código do website usando Flask está no arquivo [`app.py`](app.py). Ele simplesmente renderiza o template HTML do arquivo [`index.html`](templates/index.html).

Abaixo vamos ver como:
- Executar o site usando a [imagem do Python 3.7 no Linux Alpine do Docker Hub](https://hub.docker.com/_/python)
- Construir uma imagem própria com os arquivos do site

### Usando a imagem do Python 3.7 no Linux Alpine do Docker Hub

Primeiramente, execute o container com a imagem do Python, permitindo que ele acesse a pasta com o arquivo `app.py`. Esse acesso é fornecido através da flag `-v`, onde você deve passar o caminho da pasta atual. Por exemplo, se o projeto está na pasta C:/Projetos/1.site-flask, você deve trocar `-v<pasta>:/home` no comando abaixo por `-vC:/Projetos/1.site-flask:/home`.

```bash
# Troque a <pasta> pelo caminho da pasta atual
docker run -it -v<pasta>:/home -w/home -p 5000:5000 python:3.7-alpine /bin/sh
```

Efetivamente, este comando vai baixar a imagem Docker `python:3.7-alpine` do Docker Hub se você não tiver ela na sua máquina, executar um container com essa imagem, e abrir um terminal para executarmos mais comandos dentro do container. Explicações sobre o comando:  
- `docker run` - indica ao Docker que queremos que ele crie e execute um novo container.
- `-it` - indica que queremos um container interativo, porque vamos executar mais comandos nele.
- `-v<pasta>:/home` - conforme vimos acima, dá acesso ao container à pasta atual. Efetivamente criamos um atalho para a pasta do nosso computador no caminho `/home` do container.
- `-w/home` - indica ao container que vamos trabalhar na pasta `/home` (que o comando anterior já definiu que é um atalho para os nossos arquivos).
- `-p 5000:5000` - o Flask roda o site na porta 5000, portanto essa parte indica ao Docker para vincular a porta 5000 da nossa máquina para a porta 5000 do container.
- `python:3.7-alpine` - é a imagem e tag do Docker Hub que vamos usar no container. Veja no Docker Hub mais detalhes sobre a imagem Python: [https://hub.docker.com/_/python](https://hub.docker.com/_/python).
- `/bin/sh` - queremos abrir um terminal linux (`sh`) dentro do container para executarmos mais comandos

Feito isso, agora vamos instalar o Flask dentro do container com o comando:

```bash
pip install flask
```

Este comando instala o Flask usando o gerenciador de pacotes do Python, o [pip](https://pypi.org/project/pip/). Após a instalação do Flask, podemos executá-lo com o comando:

```bash
flask run --host 0.0.0.0
```

Pronto, agora acessando a porta 5000 do nosso computador pelo navegador ([http://localhost:5000/](http://localhost:5000/)) poderemos ver o site rodando no container que preparamos.

Para parar o servidor, basta apertar Ctrl+C. Depois, digitando o comando `exit` você sai do container. O container que você criou vai continuar existindo, você pode ver ele com `docker container ls -a`, e apagar ele com `docker container rm <nome>`.

### Preparando uma imagem própria do site

É muito trabalhoso preparar o container sempre que se quer executar o site, então é muito melhor deixá-lo preparado (com Flask instalado, por exemplo) e simplesmente executar a imagem com o site já embutido nela. Para isso, vamos criar um arquivo `Dockerfile` com o seguinte conteúdo:

```dockerfile
# syntax=docker/dockerfile:1
FROM python:3.7-alpine
WORKDIR /home
ENV FLASK_APP=app.py
ENV FLASK_RUN_HOST=0.0.0.0
COPY requirements.txt requirements.txt
RUN pip install -r requirements.txt
EXPOSE 5000
COPY . .
CMD ["flask", "run"]
```

Esses comandos especificam uma imagem construída com praticamente os mesmos comandos que fizemos antes diretamente no container: a partir da imagem do Python 3.7 (`FROM python:3.7-alpine`), vamos trabalhar na pasta `/home` (`WORKDIR /home`), vamos usar um arquivo `requirements.txt` (`COPY requirements.txt requirements.txt`) com a lista de pacotes do pip que precisamos (`RUN pip install -r requirements.txt`), que no caso é só o Flask, mas é uma boa prática usar este arquivo para listar os pacotes, e vamos expor a porta 5000 (`EXPOSE 5000`). Por fim, vamos copiar todos os arquivos do site para dentro do container (`COPY . .`) e automaticamente executar `flask run` quando o container for iniciado (`CMD ["flask", "run"]`). Aqui nós usamos uma variável de ambiente para definir o host (`ENV FLASK_RUN_HOST=0.0.0.0`) e o arquivo (`ENV FLASK_APP=app.py`) que o Flask vai executar, mas é efetivamente a mesma coisa que fizemos antes passando `--host 0.0.0.0` no comando `flask run`.

Com este arquivo `Dockerfile`, podemos construir uma imagem do nosso site:

```bash
docker build -t meu-site-flask:latest .
```

Com a flag `-t`, demos o nome de `meu-site-flask` (e a tag `latest`) para a imagem que estamos construindo. Neste momento o Docker já vai executar os comandos para instalar o Flask (`pip install`) e copiar os arquivos do site para dentro da imagem. Depois, quando quisermos executar o site basta executarmos:

```bash
docker run -it -p 5000:5000 meu-site-flask:latest
```

Pronto, agora acessando a porta 5000 do nosso computador pelo navegador ([http://localhost:5000/](http://localhost:5000/)) poderemos ver o site rodando no container com a imagem que preparamos.

Note que não precisamos dar acesso aos arquivos do site com a flag `-v`, pois os arquivos do site já estão dentro da imagem. As configurações de pasta (`/home`) também já estão prontas, e não precisamos usar o terminal (`/bin/sh`) para executar o Flask pois a imagem já está preparada para rodar ele automaticamente. A flag `-it` nesse caso é opcional, apenas para podermos ver os logs do servidor Flask sendo executado. Apesar de termos identificado a porta 5000 no Dockerfile, ainda precisamos mapear ela no `docker run`, pois o Docker nos permite escolher a porta que será vinculada no momento da execução.

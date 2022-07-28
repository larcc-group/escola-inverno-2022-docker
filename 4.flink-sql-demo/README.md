# Exemplo de uso complexo do Docker Compose para executar Flink com MySQL

Este exemplo foi retirado do site do próprio Flink, e pode ser encontrado em sua forma original em [https://flink.apache.org/2020/07/28/flink-sql-demo-building-e2e-streaming-application.html](https://flink.apache.org/2020/07/28/flink-sql-demo-building-e2e-streaming-application.html).

O `docker-compose.yml` deste exemplo define 9 serviços:

- `sql-client`: container com a imagem `jark/demo-sql-client:0.2`
- `jobmanager`: container com a imagem `flink:1.11.0-scala_2.11`, que expõe a porta 8081
- `taskmanager`: container com a imagem `flink:1.11.0-scala_2.11`
- `datagen`: container com a imagem `jark/datagen:0.2`, que possui os dados que são exibidos. Esses dados de comportamento de usuário são um conjunto de dados público oferecido pela empresa Alibaba, chamado "[_User Behavior Data from Taobao for Recommendation_](https://tianchi.aliyun.com/dataset/dataDetail?dataId=649)".
- `mysql`: container com a imagem `jark/mysql-example:0.2`, que expõe a porta 3306 (a porta padrão do banco MySQL)
- `zookeeper`: container com a imagem `wurstmeister/zookeeper:3.4.6`, que expõe a porta 2181
- `kafka`: container com a imagem `wurstmeister/kafka:2.12-2.2.1`, que expõe as portas 9092 e 9094
- `elasticsearch`: container com a imagem `docker.elastic.co/elasticsearch/elasticsearch:7.6.0`, que expõe as portas 9200 e 9300
- `kibana`: container com a imagem `docker.elastic.co/kibana/kibana:7.6.0`, que expõe a porta 5601

Após "subir" os containers com o comando `docker compose up`, pode-se acessar o Kibana na URL [http://localhost:5601/](http://localhost:5601/) e o dashboard do Flink na URL [http://localhost:8081/](http://localhost:8081/).

Para ver as primeiras 10 mensagens geradas pelo Kafka, podemos executar o comando a seguir:
```bash
docker-compose exec kafka bash -c 'kafka-console-consumer.sh --topic user_behavior --bootstrap-server kafka:9094 --from-beginning --max-messages 10'
```

Para criar as tabelas no banco SQL, vamos executar o seguinte comando:
```bash
docker-compose exec sql-client ./sql-client.sh
```

Isso abrirá o terminal SQL do container para que possamos executar os scripts SQL do arquivo `banco.sql`.
